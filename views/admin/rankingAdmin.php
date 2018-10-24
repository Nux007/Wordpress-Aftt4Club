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
    include_once "../../api/AfttDivisions.php";
    
    include_once "../../lib/pdf/fpdf.php";
    include_once "../../lib/helpers/Utils.php";
    include_once "../../lib/helpers/Members.php";
    include_once "../../lib/helpers/Divisions.php";
    
    include_once "../common/ranking.php";
}


/**
 * Create an admin view for divisions ranking.
 *
 * @category   Admin divisions ranking view.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class RankingAdmin extends DivisionsRankingView
{
    
    /**
     * ListeDeForcesAdmin view constructor.
     * @param string
     */
    public function __construct($club_index, $exclusions)
    {
        parent::__construct($club_index, $exclusions);
    }
    
    
    /**
     * Prints ldf as html.
     */
    public function print()
    {   
        
        $target = plugin_dir_url(__FILE__) . "rankingAdmin.php";
        $cmap = base64_encode(htmlspecialchars(serialize($this->getColorsMap())));
        ?>
        <div id="admin_wrap" style="width:70%">
        
        <!-- Impression du document. -->
        <h2><?php _e("Print ranking", "aftt4club")?></h2><hr>
        <span><?php _e("That's better if you wait until tuesday before printing rankings.", "aftt4club") ?></span>
        
        <div style="margin-top: 1%; margin-bottom:1%">        
            <a target="_blank" href="<?php echo $target; ?>?cmap=<?php echo $cmap ?>&print_plugin_pdf=true&club_index=<?php echo $this->_club_index ?>" 
               class="button-primary" style="margin-right: 12px;"><?php _e("Print", "aftt4club")?>
            </a>
        </div>
        
        <!-- Affichage du classement -->
        <h2><?php _e("Last known rankings", "aftt4club") ?></h2><hr>
            <?php $this->printHTML(); ?>
        </div>
        <?php
    }
    
    
    
    /**
     * Prints an exclusion form that allow to exclude divisions from output.
     * @param array $exclusions
     */
    public function printExclusionsForm($exclusions)
    {
        ?>
        <div style="width: 50%; margin-bottom: 10px;">
        <h2><?php _e("Excluded from ranking", "aftt4club") ?></h2><hr>
        <span><?php _e("You can exclude the division ranking of your choise here, for exemple, if a division was forfeited.", "aftt4club") ?></span>
              </div>
              <?php
              if(get_option("aftt4club_index") !== false) {
                  ?>
                  <form method="post" name="divisions_exceptions" id="divisions_exceptions" action="">
                      <select id='aftt4club_divisions_excluded' name='aftt4club_divisions_excluded'>
                          <?php
                          foreach($this->_divisions as $division) {
                              echo "<option value='".$division->getDivisionId()."," . $division->getDivisionName() . "'>". $division->getDivisionName() ."</option>";
                          }
                          ?>
                      </select>
                      
                      <?php submit_button(); ?>
                  </form>
                  
                  
                  <?php
                  if(count($exclusions) > 0) {
                      ?>
                      <table class="aftt_ldf" style="width: 68%;">
                          <tr class="rowtitle medium">
                              <th class="wide_text green" scope="col"><?php _e("Division Id", "aftt4club") ?></th>
                              <th class="wide_text green" scope="col"><?php _e("Division", "aftt4club")?></th>
                          </tr>
                      
                      <?php 
                      foreach($exclusions as $excluded) {
                          echo "<tr>";
                          echo "<td><strong>Exclusion: </strong>".$excluded["division_name"] ."</td>";
                          echo '<td><form method="post" action="" name="remove-'.$excluded["division_id"].'">
                                      <input type="submit" value="Supprimer" name="delete_exclusion" id="delete_exclusion" />
                                      <input type="hidden" name="division_id" id="division_id" value="'.$excluded["division_id"].'" />
                                      <input type="hidden" name="division_name" id="division_name" value="'.$excluded["division_name"].'" />
                                    </form></td>';
                          echo "</tr>";
                      }
                      
                      ?>
                      </table>
                      
                      <?php 
                      
                  }
              }
              else { ?>
                  <span style="color: red; margin-top: 10px;"><?php _e("You must provide your club indice before accessing this contents !", "aftt4club") ?></span>
                  <?php
              } 
    }
    
    
    
    /**
     * Prints the "liste de forces" contents as PDF.
     */
    
    public function printPDF()
    {
        $pdf = new PrintablePDF( 'P', 'mm', 'A4' );
        
        // Colors configuration
        list($header_r, $header_g, $header_b) = sscanf($this->colorsMap["header"], "#%02x%02x%02x");
        list($border_r, $border_g, $border_b) = sscanf($this->colorsMap["borders"], "#%02x%02x%02x");
        
        $pdf->SetDrawColor( $border_r, $border_g, $border_b );
        $pdf->SetTextColor( 0, 0, 0);
        
        $counter = 0;
        
        foreach($this->getRankings() as $divID => $ranking) {
            
            if( ($counter % 2) == 0 ) {
                $pdf->AddPage();
                $pdf->SetLeftMargin(15);
                $pdf->Ln(0);
                $pdf->SetFont( 'Arial', 'B', 12 );
                $pdf->Cell(182, 10, "Saison: " . $this->getSeason() . " , Semaine " . $this->getCurrentWeek(), 0, 0, 'C', false);
                $pdf->Ln(10);
            }
            
            $counter++;
            
            // LDF header configuration.
            $header = $ranking[0]->getDivisionName();
            $pdf->SetFont( 'Arial', 'B', 12 );
            
            // Writing club and season infos.
            $pdf->SetFillColor( $header_r, $header_g, $header_b );
            $pdf->Cell( 182, 10, utf8_decode($header), 1, 0, 'C', true );
            
            /* Last known results */
            
            $result = $this->getResults()[$divID];
            $pdf->Ln( 12 );
            $pdf->SetFont( 'Arial', 'B', 9 );
            $pdf->SetFillColor( 255, 255, 255 );
            $pdf->Cell( 82, 5, utf8_decode("Visités"), 1, 0, 'C', true );
            $pdf->Cell( 82, 5, "Visiteurs", 1, 0, 'C', true );
            $pdf->Cell( 18, 5, "Score", 1, 0, 'C', true );
            
            $pdf->Ln( 5 );
            
            // Create the table data rows
            $pdf->SetFont( 'Arial', '', 10 );
            $i = 1;
            
            foreach ( $result as $game ) {
                // Create the data cells
                if($game->getHomeClubIndice() == $this->_club_index || $game->getAwayClubIndice() == $this->_club_index)
                    list($r, $g, $b) = sscanf("#FFFF6B", "#%02x%02x%02x");
                    else
                        list($r, $g, $b) = sscanf((($i % 2) != 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]), "#%02x%02x%02x");
                        
                        $pdf->SetFillColor( $r, $g, $b );
                        
                        $pdf->Cell( 82, 5, $game->getHomeTeam(), 1, 0, 'C', true );
                        $pdf->Cell( 82, 5, $game->getAwayTeam(), 1, 0, 'C', true );
                        $pdf->Cell( 18, 5, $game->getScore(), 1, 0, 'C', true );
                        
                        $pdf->Ln(5);
                        $i++;
            }            
            
            /* General ranking */
            // Create the table header row
            $pdf->Ln( 5 );
            $pdf->SetFont( 'Arial', 'B', 11 );
            $pdf->SetFillColor( 255, 255, 255 );
            $pdf->Cell( 18, 5, "Place", 1, 0, 'C', true );
            $pdf->Cell( 74, 5, "Equipe", 1, 0, 'C', true );
            $pdf->Cell( 18, 5, utf8_decode("Joué"), 1, 0, 'C', true );
            $pdf->Cell( 18, 5, utf8_decode("Gagné"), 1, 0, 'C', true );
            $pdf->Cell( 18, 5, "Perdu", 1, 0, 'C', true );
            $pdf->Cell( 18, 5, "Nul", 1, 0, 'C', true );
            $pdf->Cell( 18, 5, "Points", 1, 0, 'C', true );
            
            $pdf->Ln( 5 );
            
            // Create the table data rows
            $pdf->SetFont( 'Arial', '', 10 );
            $i = 1;
            
            foreach ( $ranking as $team ) {
                // Create the data cells
                if($team->getTeamClub() == $this->_club_index)
                    list($r, $g, $b) = sscanf("#FFFF6B", "#%02x%02x%02x");
                else
                    list($r, $g, $b) = sscanf((($team->getPosition() % 2) != 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]), "#%02x%02x%02x");
                                
                $pdf->SetFillColor( $r, $g, $b );
                
                $pdf->Cell( 18, 5, $team->getPosition(), 1, 0, 'C', true );
                $pdf->Cell( 74, 5, $team->getTeam(), 1, 0, 'C', true );
                $pdf->Cell( 18, 5, $team->getGamesPlayed(), 1, 0, 'C', true );
                $pdf->Cell( 18, 5, $team->getGamesWon(), 1, 0, 'C', true );
                $pdf->Cell( 18, 5, $team->getGamesLost(), 1, 0, 'C', true );
                $pdf->Cell( 18, 5, $team->getGamesDraw(), 1, 0, 'C', true );
                $pdf->Cell( 18, 5, $team->getPoints(), 1, 0, 'C', true );
                
                $pdf->Ln(5);
            }
            
            $pdf->Ln(18);
        }
        
        // Includes js to open the print dialog box.
        $pdf->IncludeJS("this.print({bUI: false, bSilent: false, bShrinkToFit: true});");
        $pdf->Output( "divisions_ranking.pdf", "I" );
    }

    
    
}


// Exclusions.
if(isset($_POST["aftt4club_divisions_excluded"])) {
    $data = array();
    if(get_option("aftt4club_divisions_exclusions") !== false)
        $data = get_option("aftt4club_divisions_exclusions");
    $division = explode(",", $_POST["aftt4club_divisions_excluded"]);    
    $data[] = array("division_id" => $division[0], "division_name" => $division[1]);
    update_option("aftt4club_divisions_exclusions", $data);
}



// Delete challenge exclusion.
if(isset($_POST["delete_exclusion"])) {
    $data = get_option("aftt4club_divisions_exclusions");
    foreach($data as $key => &$exception) {
        
        if(intval($exception["division_id"]) == intval($_POST["division_id"])){
            unset($data[$key]);
        }
    }
    
    update_option("aftt4club_divisions_exclusions", $data);
}



// Printing divisions ranking as PDF".
if(isset($_GET["print_plugin_pdf"]) && $_GET["print_plugin_pdf"] == true) {
    $ranking = new RankingAdmin($_GET["club_index"]);
    $cmap = unserialize(htmlspecialchars_decode(base64_decode($_GET['cmap'])));
    $ranking->setColorsMap($cmap);
    $ranking->printPDF();
}

?>