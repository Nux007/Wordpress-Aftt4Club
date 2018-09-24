<?php 
error_reporting(E_ALL);
// For tests only.
if(file_exists("../../lib/pdf/fpdf.php"))
    require_once( "../../lib/pdf/fpdf.php" );


// Printing plugin version of "liste de forces as PDF".
if(isset($_GET["print_plugin_pdf"]) && $_GET["print_plugin_pdf"] == true){
    include_once "../../api/Aftt.php";
    include_once "../../api/AfttClubs.php";
    include_once "../../api/AfttMembers.php";
    
    $Club = new AfttClub();
    $Club->init($_GET["club_index"]);
    $ldf = new ListeDeForces($Club);
    $cmap = unserialize(htmlspecialchars_decode(base64_decode($_GET['cmap'])));
    $ldf->setColorsMap($cmap);
    $ldf->printPDF();
} 
    

    
class ListeDeForces{
    
    private $Club;
    public $colorsMap = array("header" => "#9cfff4", "th" => "#9cfff4", "borders" => "#f0f0f0", "even" => "#f0f0f0", "odd" => "#FFFFFF");
    
    public function __construct($Club, $colors=false){
        $this->Club = $Club;
        
        if($colors !== false)
            $this->colorsMap = $colors;
    }
    
    
    // Print the "Liste de Force" contents as HTML.
    public function printHTML($buttons=false){
        // TODO ajouter les différentes catégories de listes de forces + les intégrer et gérer les changements...
        // Colors configuration
        
        if(file_exists("../css/admin/listedeforces_1.css"))
            // Does not exists outside tests context, so, using this sall hack for tests purpose !
            echo '<link rel="stylesheet" type="text/css" href="../css/admin/listedeforces_1.css" media="screen" />';
        
            
        $subline = "Liste de forces - Saison: " . $this->Club->getSeason();
        $header = $this->Club->index . " - " . $this->Club->name;
                
        ?>
        
        <div class="wrap">
            <div class="wrap" id="wrap">
                
                <div id="ldf_header" style="background-color: <?php echo $this->colorsMap["header"]; ?>">
                    <p id='club_name'>
                        <span style='font-size:26px; font-weight: bold;'><?php echo $header; ?></span><br />
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
        
        $style= "border: 1px solid " . $this->colorsMap["borders"] . ";";
        foreach($this->Club->getMembers() as $Member){
            $fill = " background-color: " . (($Member->position %2) == 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]);
      
            echo "<tr style='". $style . $fill . "'>";
            echo "<td id='ordre' style='". $style . $fill . "'>".$Member->position ."</td>";
            echo "<td id='index' style='". $style . $fill . "'> ".$Member->rankingIndex."</td>";
            echo "<td id='affiliation' style='". $style . $fill . "'>".$Member->affiliation."</td>";
            echo "<td id='nom' style='". $style . $fill . "'>".$Member->lastName."</td>";
            echo "<td id='prenom' style='". $style . $fill . "'>".$Member->firstName."</td>";
            echo "<td id='classement' style='". $style . $fill . "'>".$Member->ranking."</td>";
            echo "</tr>";
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
                           href="<?php echo $target; ?>?cmap=<?php echo $cmap ?>&print_plugin_pdf=true&club_index=<?php echo $this->Club->index ?>" 
                           id="print_ldf_plugin_ver" class="button-secondary" style="margin-right: 12px;">
                           Imprimer cette version
                        </a>
                        
                        <a target="_blank" href="<?php echo $this->Club->getAfttLdfLink(); ?>" class="button-secondary">
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
        $header = $this->Club->index . " - " . $this->Club->name;        
        $subline = "Liste de forces - Saison: " . $this->Club->getSeason();
     
        // Writing club and season infos.
        $pdf->SetFont( 'Arial', 'B', 15 );
        $pdf->Ln(0);
        $pdf->SetFillColor( $header_r, $header_g, $header_b );
        $pdf->Cell( 182, 15, $header, 1, 0, 'C', true );
        
        // Writing club current season and table caption.
        $pdf->SetFont( 'Arial', '', 10 );
        $pdf->Ln(9);
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
        
        foreach ( $this->Club->getMembers() as $Member ) {
            // Create the data cells
            list($r, $g, $b) = sscanf((($Member->position % 2) == 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]), "#%02x%02x%02x");
            $pdf->SetFillColor( $r, $g, $b );
            
            $pdf->Cell( 18, 6, $Member->position, 1, 0, 'C', true );
            $pdf->Cell( 18, 6, $Member->rankingIndex, 1, 0, 'C', true );
            $pdf->Cell( 24, 6, $Member->affiliation, 1, 0, 'C', true );
            $pdf->Cell( 54, 6, $Member->lastName, 1, 0, 'C', true );
            $pdf->Cell( 54, 6, $Member->firstName, 1, 0, 'C', true );
            $pdf->Cell( 14, 6, $Member->ranking, 1, 0, 'C', true );
            
            $pdf->Ln(6);
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