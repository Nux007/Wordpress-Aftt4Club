<?php     

/**
* Aftt4Club is a wordpress plugin that helps to manage you Table Tennis club. 
* Copyright (C) 2018  Nux007
*    
* This file is part of Aftt4Club wordpress plugin.
*    
* Aftt4Club is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* (at your option) any later version.
*    
* Aftt4Club is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*    
* You should have received a copy of the GNU General Public License
* along with Aftt4Club. If not, see <http://www.gnu.org/licenses/>.
**/

if(file_exists("../../api/Aftt.php")) {
    include_once "../../api/Aftt.php";
    include_once "../../api/AfttClubs.php";
    include_once "../../api/AfttMembers.php";
    include_once "../common/listeDeForces.php";
    include_once "../../lib/pdf/fpdf.php";
}


/**
 * Create an admin view for club 'liste de forces'.
 *
 * @category   Admin Members managment view.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class ListeDeForcesAdmin extends ListeDeForces 
{
        
    /**
     * ListeDeForcesAdmin view constructor.
     * @param string
     */
    public function __construct($club_index)
    {
        $Club = new AfttClub();
        $Club->init($club_index);
        parent::__construct($Club);
    }
    
    
    /**
     * Prints ldf as html.
     */
    public function print()
    {
        $this->printHTML($headers=true);
        
        // Display custo ldf PDF link.
        $target = plugin_dir_url(__FILE__) . "listeDeForcesAdmin.php";
        $cmap = base64_encode(htmlspecialchars(serialize($this->colorsMap)));
        ?>
        <div class="ldf_actions">
            <fieldset class="ldf_cfg_fieldset">
                <legend>Impression de la liste de forces</legend>
                <span>Si vous imprimez la liste de forces version plugin (premier bouton), les proportions seront automatiquement ajustées au format papier A4,
                      et la boîte de dialogue d'impression s'ouvrira toute seule !</span><br /><br />
                <a target="_blank" 
                   href="<?php echo $target; ?>?cmap=<?php echo $cmap ?>&print_plugin_pdf=true&club_index=<?php echo $this->getClub()->getIndex() ?>" 
                   id="print_ldf_plugin_ver" class="button-secondary" style="margin-right: 12px;">
                   Imprimer cette version
                </a>
                
                <a target="_blank" href="<?php echo $this->getClub()->getAfttLdfLink(); ?>" class="button-secondary">
                   Imprimer version Aftt
                </a>
            
          </fieldset>
        </div>
        <?php 
    }
    
    
    
    /**
     * Prints the "liste de forces" contents as PDF.
     */
    public function printPDF()
    {
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
        $header = $this->getClub()->getIndex() . " - " . $this->getClub()->getName();
        $subline = "Liste de forces - Saison: " . $this->getClub()->getSeason();
        
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
        
        foreach ( $this->getClub()->getMembers() as $Member ) {
            // Create the data cells
            list($r, $g, $b) = sscanf((($Member->getPosition() % 2) == 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]), "#%02x%02x%02x");
            $pdf->SetFillColor( $r, $g, $b );
            
            $pdf->Cell( 18, 6, $Member->getPosition(), 1, 0, 'C', true );
            $pdf->Cell( 18, 6, $Member->getRankingIndex(), 1, 0, 'C', true );
            $pdf->Cell( 24, 6, $Member->getAffiliationNumber(), 1, 0, 'C', true );
            $pdf->Cell( 54, 6, $Member->getLastName(), 1, 0, 'C', true );
            $pdf->Cell( 54, 6, $Member->getFirstName(), 1, 0, 'C', true );
            $pdf->Cell( 14, 6, $Member->getRanking(), 1, 0, 'C', true );
            
            $pdf->Ln(6);
        }
        
        // Includes js to open the print dialog box.
        $pdf->IncludeJS("this.print({bUI: false, bSilent: false, bShrinkToFit: true});");
        $pdf->Output( "liste_de_forces.pdf", "I" );
    }
    
    
}



// Printing plugin version of "liste de forces as PDF".
if(isset($_GET["print_plugin_pdf"]) && $_GET["print_plugin_pdf"] == true) {
    
    $ldf = new ListeDeForcesAdmin($_GET["club_index"]);
    $cmap = unserialize(htmlspecialchars_decode(base64_decode($_GET['cmap'])));
    $ldf->setColorsMap($cmap);
    $ldf->printPDF();
} 


?>