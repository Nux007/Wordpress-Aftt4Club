<?php 

// For tests only.
if(file_exists("../lib/pdf/fpdf.php"))
    require_once( "../lib/pdf/fpdf.php" );


// Printing plugin version of "liste de forces as PDF".
if(isset($_GET["print_plugin_pdf"]) && $_GET["print_plugin_pdf"] == true){
    include_once "../data/Aftt.php";
    $ldf = new ListeDeForces($_GET["club_indice"]);
    $cmap = unserialize(htmlspecialchars_decode(base64_decode($_GET['cmap'])));
    $ldf->setColorsMap($cmap);
    $ldf->printPDF();
} 
    

    
class ListeDeForces{
    
    private $club_indice; // DB indice.
    private $aftt;
    protected $members;
    public $colorsMap = array("header" => "#9cfff4", "th" => "#9cfff4", "borders" => "#f0f0f0", "even" => "#f0f0f0", "odd" => "#FFFFFF");
    
    public function __construct($indice, $colors=false){
        $this->club_indice = $indice;
        if($colors !== false)
            $this->colorsMap = $colors;
        $this->aftt = new AfttScraper();
        $this->members = $this->aftt->getClubMembers($indice);
        
        $indexes = array_column($this->members, 'index');
        $names   = array_column($this->members, 'nom');
        $class   = array_column($this->members, 'classement');
        
        array_multisort($indexes, SORT_ASC, $class, SORT_ASC, $names, SORT_ASC, $this->members);
    }
    
    
    // Print the "Liste de Force" contents as HTML.
    public function printHTML($buttons=false){
        // TODO ajouter les différentes catégories de listes de forces + les intégrer et gérer les changements...
        // Colors configuration
        
        if(file_exists("../css/admin/listedeforces_1.css"))
            // Does not exists outside tests context, so, using this sall hack for tests purpose !
            echo '<link rel="stylesheet" type="text/css" href="../css/admin/listedeforces_1.css" media="screen" />';
        
            
        $name = $this->aftt->getClubNameAndIndice($this->club_indice);
        $season = $this->aftt->getCurrentSeason();
        
        if($name !== false)
            $clubHeaderName = $name["indice"] . " - " . $name["name"];
            
        if($season !== false)
            $clubCurrentSeason = "Saison: " . $season;
        
        $subline = "Liste de forces";
        if(null !== $clubCurrentSeason)
            $subline .= " - " . $clubCurrentSeason;
                
        ?>
        
        <div class="wrap">
            <div class="wrap" id="wrap">
                
                <div id="ldf_header" style="background-color: <?php echo $this->colorsMap["header"]; ?>">
                    <p id='club_name'>
                        <span style='font-size:26px; font-weight: bold;'><?php echo $clubHeaderName; ?></span><br />
                        <span style='font-size: 16px;'><?php echo $subline; ?></span>
                    </p> 
                </div>
                
                <table class="aftt_ldf" id="ldf" style="border: 1px solid <?php echo $this->colorsMap["borders"]; ?>;">
                  <thead>
                  
                  <?php $border = "background-color: " . $this->colorsMap["th"] . ";"; ?>
                  
                    <tr class="rowtitle" style="<?php echo $border; ?>">
                      <th id='ordre' scope="col" style="<?php echo $border; ?>">Ordre</th>
                      <th id='index' scope="col" style="<?php echo $border; ?>">Index</th>
                      <th id='affiliation' scope="col" style="<?php echo $border; ?>">Affiliation</th>
                      <th id='nom' scope="col" style="<?php echo $border; ?>">Nom</th>
                      <th id='prenom' scope="col" style="<?php echo $border; ?>">Prenom</th>
                      <th id='classement' scope="col" style="<?php echo $border; ?>">Cl.</th>
                    </tr>
                  </thead>
                  <tbody>  
        <?php
        
        $ordre = 1;
        $style= "border: 1px solid " . $this->colorsMap["borders"] . ";";
        foreach($this->members as $member){
            $fill = " background-color: " . (($ordre %2) == 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]);
      
            echo "<tr style='". $style . $fill . "'>";
            echo "<td id='ordre' style='". $style . $fill . "'>".$ordre ."</td>";
            echo "<td id='index' style='". $style . $fill . "'> ".$member['index']."</td>";
            echo "<td id='affiliation' style='". $style . $fill . "'>".$member['numero_affiliation']."</td>";
            echo "<td id='nom' style='". $style . $fill . "'>".$member['nom']."</td>";
            echo "<td id='prenom' style='". $style . $fill . "'>".$member['prenom']."</td>";
            echo "<td id='classement' style='". $style . $fill . "'>".$member['classement']."</td>";
            echo "</tr>";
            $ordre += 1;
        }
        ?>
                  </tbody>
                </table>
            </div>
            <?php 
            if($buttons){ 
                $target = plugin_dir_url(__FILE__) . "listeDeForces.php";
                $cmap = base64_encode(htmlspecialchars(serialize($this->colorsMap)));
            ?>
                <div class="ldf_actions">
                    <fieldset class="ldf_cfg_fieldset">
                        <legend>Impression de la liste de forces</legend>
                        <span>Si vous imprimez la liste de forces version plugin (premier bouton), les proportions seront automatiquement ajustées au format papier A4,
                              et la boîte de dialogue d'impression s'ouvrira toute seule !</span><br /><br />
                        <a target="_blank" 
                           href="<?php echo $target; ?>?cmap=<?php echo $cmap ?>&print_plugin_pdf=true&club_indice=<?php echo $this->club_indice; ?>" 
                           id="print_ldf_plugin_ver" class="button-secondary" style="margin-right: 12px;">
                           Imprimer cette version
                        </a>
                        
                        <a target="_blank" href="<?php echo $this->aftt->getAfttLdfLink($this->club_indice); ?>" class="button-secondary">
                           Imprimer version Aftt
                        </a>
                    
                  </fieldset>
                </div>
            <?php 
            } 
            ?>
        </div>
        <?php
    }  
    
    
    
    // Prints the "liste de forces" contents as PDF.
    public function printPDF(){
        $pdf = new PrintablePDF( 'P', 'mm', 'A4' );
        $pdf->AddPage();
        $pdf->SetLeftMargin(14);
        $pdf->SetTextColor( 0, 0, 0);
        
        // Colors configuration
        list($header_r, $header_g, $header_b) = sscanf($this->colorsMap["header"], "#%02x%02x%02x");
        list($border_r, $border_g, $border_b) = sscanf($this->colorsMap["borders"], "#%02x%02x%02x");
        list($th_r, $th_g, $th_b) = sscanf($this->colorsMap["th"], "#%02x%02x%02x");
        list($even_r, $even_g, $even_b) = sscanf($this->colorsMap["even"], "#%02x%02x%02x");
        list($odd_r, $odd_g, $odd_b) = sscanf($this->colorsMap["odd"], "#%02x%02x%02x");
        
        $pdf->SetDrawColor( $border_r, $border_g, $border_b );
        
        // LDF header configuration.
        $clubCurrentSeason = null;
        $name = $this->aftt->getClubNameAndIndice($this->club_indice);
        $season = $this->aftt->getCurrentSeason();
        
        if($name !== false)
            $clubHeaderName = $name["indice"] . " - " . $name["name"];
        
        if($season !== false)
            $clubCurrentSeason = "Saison: " . $season;
     
        // Writing club and season infos.
        $pdf->SetFont( 'Arial', 'B', 15 );
        $pdf->Ln(0);
        $pdf->SetFillColor( $header_r, $header_g, $header_b );
        $pdf->Cell( 182, 15, (null !== $clubHeaderName) ? $clubHeaderName : "", 1, 0, 'C', true );
        
        // Writing club current season and table caption.
        $pdf->SetFont( 'Arial', '', 10 );
        $pdf->Ln(9);
        $subline = "Liste de forces";
        if(null !== $clubCurrentSeason)
            $subline .= " - " . $clubCurrentSeason;
        $pdf->Cell( 182, 6, $subline, 0, 0, 'C' );

        // Create the table header row
        $pdf->Ln( 14 );
        $pdf->SetFont( 'Arial', 'B', 10 );
        $pdf->SetFillColor( $th_r, $th_g, $th_b );
        $pdf->Cell( 18, 6, "Ordre", 1, 0, 'C', true );
        $pdf->Cell( 18, 6, "Index", 1, 0, 'C', true );
        $pdf->Cell( 24, 6, "Affiliation", 1, 0, 'C', true );
        $pdf->Cell( 54, 6, "Nom", 1, 0, 'C', true );
        $pdf->Cell( 54, 6, "Prenom", 1, 0, 'C', true );
        $pdf->Cell( 14, 6, "Cl.", 1, 0, 'C', true );
        
        $pdf->Ln( 6 );
        
        // Create the table data rows
        $fill = false;
        $pdf->SetFont( 'Arial', '', 10 );
        $i = 1;
        
        foreach ( $this->members as $member ) {
            // Create the data cells
            list($r, $g, $b) = sscanf((($i % 2) == 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]), "#%02x%02x%02x");
            $pdf->SetFillColor( $r, $g, $b );
            
            $pdf->Cell( 18, 6, $i, 1, 0, 'C', true );
            $pdf->Cell( 18, 6, $member['index'], 1, 0, 'C', true );
            $pdf->Cell( 24, 6, $member['numero_affiliation'], 1, 0, 'C', true );
            $pdf->Cell( 54, 6, $member['nom'], 1, 0, 'C', true );
            $pdf->Cell( 54, 6, $member['prenom'], 1, 0, 'C', true );
            $pdf->Cell( 14, 6, $member['classement'], 1, 0, 'C', true );
            
            $pdf->Ln(6);
            $i += 1;
        }
        
        $pdf->IncludeJS("this.print({bUI: false, bSilent: false, bShrinkToFit: true});");  
        $pdf->Output( "liste_de_forces.pdf", "I" );
    }
    
    
    // Sets the color maps for html and pdf generation.
    public function setColorsMap($head, $th=null, $border=null, $even=null, $odd=null) {
        if(is_array($head))
            $this->colorsMap = $head;
        else    
            $this->colorsMap = array("header" => $head, "th" => $th, "borders" => $border, "even" => $even, "odd" => $odd);
    }
    
}





?>  