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
    include_once "../../api/AfttChallenge.php";
    
    include_once "../../lib/pdf/fpdf.php";
    include_once "../../lib/helpers/Utils.php";
    include_once "../../lib/helpers/Members.php";
    include_once "../../lib/helpers/Divisions.php";
    
    include_once "../common/challenge.php";
    include_once "../admin/challengeAdmin.php";
    
}


/**
 * Create an admin view for club members challenge.
 *
 * @category   Admin Members challenge view.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class ClubMembersChallengeAdmin extends ChallengeView
{
    
    private $_login;
    private $_password;
    private $_exceptions;
    
    /**
     * ListeDeForcesAdmin view constructor.
     * @param string
     */
    public function __construct($club_index, $login, $password, $exclusions=array())
    {
        $this->_login = $login;
        $this->_password = $password;
        $this->_exceptions = $exclusions;
        if(is_string($this->_exceptions)) {
            $this->_exceptions = null;
        }
        parent::__construct($club_index, false, $login, $password, $this->_exceptions);
    }
    
    
    /**
     * Prints ldf as html.
     */
    public function print()
    {
        
        $target = plugin_dir_url(__FILE__) . "challengeAdmin.php";
        $cmap = base64_encode(htmlspecialchars(serialize($this->colorsMap)));
        
        ?>
        <div id="admin_wrap" style="width:70%; margin-top: 30px;" >
            <!-- Print buttons. -->
            <h2><?php _e("Printing challenge", "aftt4club") ?></h2><hr>
            <span><?php _e("Challenge creation is fully automated, but, I recommend to wait until tuesday to print. Indeed, challenge depends on a manual Aftt validation", "aftt4club") ?></span>
            <span><strong><?php _e("Warning", "aftt4club")?></strong><?php _e(", only players that have played at least one game are listed here", "aftt4club")?> !</span>
            <div style="margin-top: 1%; margin-bottom:1%">
                <a target="_blank" href="<?php echo $target; ?>?cmap=<?php 
                                               echo $cmap ?>&print_plugin_pdf=true&lg=<?php 
                                               echo base64_encode($this->_login);?>&ps=<?php 
                                               echo base64_encode($this->_password);?>&exceptions=<?php 
                                               echo base64_encode(serialize($this->_exceptions)); ?>&club_index=<?php echo $this->getClub()->getIndex() ?>"
                   class="button-primary" style="margin-right: 12px;"><?php _e("Print challenge", "aftt4club")?></a>
            </div><br />
        
            <!--  Printing LDF -->
            <h2><?php _e("Challenge preview", "aftt4club") ?></h2><hr>
            <?php
                $this->printHTML(true);
            ?>
        </div>
        
        <div id="admin_wrap2" style="width: 70%; margin-top: 40px;">
        <?php
        $this->printHTMLVictories();
        ?>
        </div>
        <?php 
    }
    
    
    
    /**
     * Prints the victories / looses array.
     */
    public function printHTMLVictories()
    {        
        if(file_exists("../css/shortcodes.css"))
            echo '<link rel="stylesheet" type="text/css" href="../css/shortcodes.css" media="screen" />';
            
            ?>
        
        <div class="wrap" style="text-align: center;">
            <!-- Headers. -->
            <div class="wrap" id="wrap">
                <div id="challenge_header"> 
                    <div id='left' style='width:100%'><span><?php _e("Victories and losses", "aftt4club") ?></span></div>
                </div>
              
                <table class="aftt_ldf" id="challenge_data" style="border: 1px solid <?php echo $this->colorsMap["borders"]; ?>;width: 100%;border-spacing: 0;">
                  <thead>                  
                    <tr class="rowtitle medium">
                      <th class="wide_text green" scope="col"></th>
                      <?php 
                      foreach($this->challenge->getAvailablesRankings() as $ranking)
                          echo '<th class="very_small green"  scope="col" colspan="2" style="background-color: #558ed5;">'.$ranking.'</th>';
                      ?>
                      <th class="very_small green"  scope="col" colspan="2" style="background-color: #558ed5;">WO</th>
                    </tr>
                    
                    <tr>
                      <th class="wide_text green" scope="col"></th>
                      <?php 
                      foreach($this->challenge->getAvailablesRankings() as $ranking) {
                          echo '<th class="very_small green" scope="col">G</th>';
                          echo '<th class="very_small green" scope="col" style="background:red;">P</th>';
                      }
                      ?>
                      <th class="very_small green" scope="col" colspan="2">G</th>
                    </tr>
                    
                  </thead>
                  <tbody>  
        <?php
        
        $style= "border: 1px solid " . $this->colorsMap["borders"] . ";";
        $position = 1;
        foreach($this->challenge_data as $user_row) {
                        
            $fill = " background-color: " . (($position %2) != 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]);
      
            echo "<tr class='medium' style='". $style . $fill . "'>";
            echo "<td class='wide_text'  style='". $style . $fill . "; padding-left: 8px;'>". $user_row["player"] ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("NC", array_keys($user_row["victories"]))) ? $user_row["victories"]["NC"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("NC", array_keys($user_row["looses"]))) ? $user_row["looses"]["NC"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("E6", array_keys($user_row["victories"]))) ? $user_row["victories"]["E6"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("E6", array_keys($user_row["looses"]))) ? $user_row["looses"]["E6"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("E4", array_keys($user_row["victories"]))) ? $user_row["victories"]["E4"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("E4", array_keys($user_row["looses"]))) ? $user_row["looses"]["E4"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("E2", array_keys($user_row["victories"]))) ? $user_row["victories"]["E2"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("E2", array_keys($user_row["looses"]))) ? $user_row["looses"]["E2"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("E0", array_keys($user_row["victories"]))) ? $user_row["victories"]["E0"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("E0", array_keys($user_row["looses"]))) ? $user_row["looses"]["E0"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("D6", array_keys($user_row["victories"]))) ? $user_row["victories"]["D6"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("D6", array_keys($user_row["looses"]))) ? $user_row["looses"]["D6"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("D4", array_keys($user_row["victories"]))) ? $user_row["victories"]["D4"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("D4", array_keys($user_row["looses"]))) ? $user_row["looses"]["D4"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("D2", array_keys($user_row["victories"]))) ? $user_row["victories"]["D2"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("D2", array_keys($user_row["looses"]))) ? $user_row["looses"]["D2"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("D0", array_keys($user_row["victories"]))) ? $user_row["victories"]["D0"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("D0", array_keys($user_row["looses"]))) ? $user_row["looses"]["D0"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("C6", array_keys($user_row["victories"]))) ? $user_row["victories"]["C6"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("C6", array_keys($user_row["looses"]))) ? $user_row["looses"]["C6"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("C4", array_keys($user_row["victories"]))) ? $user_row["victories"]["C4"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("C4", array_keys($user_row["looses"]))) ? $user_row["looses"]["C4"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("C2", array_keys($user_row["victories"]))) ? $user_row["victories"]["C2"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("C2", array_keys($user_row["looses"]))) ? $user_row["looses"]["C2"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("C0", array_keys($user_row["victories"]))) ? $user_row["victories"]["C0"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("C0", array_keys($user_row["looses"]))) ? $user_row["looses"]["C0"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("B6", array_keys($user_row["victories"]))) ? $user_row["victories"]["B6"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("B6", array_keys($user_row["looses"]))) ? $user_row["looses"]["B6"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("B4", array_keys($user_row["victories"]))) ? $user_row["victories"]["B4"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("B4", array_keys($user_row["looses"]))) ? $user_row["looses"]["B4"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("B2", array_keys($user_row["victories"]))) ? $user_row["victories"]["B2"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("B2", array_keys($user_row["looses"]))) ? $user_row["looses"]["B2"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("B0", array_keys($user_row["victories"]))) ? $user_row["victories"]["B0"] : "" ) ."</td>";
            echo "<td class='very_small cred'   style='". $style . $fill . "'>". ( (in_array("B0", array_keys($user_row["looses"]))) ? $user_row["looses"]["B0"] : "" )       ."</td>";
            
            echo "<td class='very_small cgreen' style='". $style . $fill . "'>". ( (in_array("WO", array_keys($user_row["victories"]))) ? $user_row["victories"]["WO"] : "" ) ."</td>";
            
            echo "</tr>";
            $position++;
        }
        
        ?>
                  </tbody>
                </table>
                
            </div>
        </div>
        <?php
    }
    
    
    
    /**
     * Prints the exclusion form that allow administrator to remove a week result for a given player.
     */
    public function printExclusionsForm($exclusions)
    {
        ?>
        <div style="width: 50%; margin-bottom: 10px;">
        <h2><?php _e("Challenge exclusions", "aftt4club") ?></h2><hr>
        <span><?php _e("You can exclude some players week results here.", "aftt4club"); 
                    _e("Example: you exclude XY player results for the week three, that will remove all player results and points for the targeted week.", "aftt4club");
              ?></span>
              </div>
              <?php
              if(get_option("aftt4club_index") !== false) {
                  ?>
                  <form method="post" name="challenge_exceptions" id="challenge_exceptions" action="">
                      <select id='aftt4club_challenge_excluded' name='aftt4club_challenge_excluded'>
                          <?php
                          foreach($this->_Club->getMembers() as $member) {
                              echo "<option value='".$member->getFirstName() . "," . $member->getLastName() . ",". $member->getAffiliationNumber()."'>".
                                                     $member->getFirstName() . " " . $member->getLastName().
                                   "</option>";
                          }
                          ?>
                      </select>
                      
                      <select id='aftt4club_challenge_excluded_week' name='aftt4club_challenge_excluded_week'>
                          <?php
                          for($i = 1; $i <= 22; $i++) {
                              echo "<option value='".strval($i)."'>"._e("Week", "aftt4club"). " ". strval($i) ."</option>";
                          }
                          ?>
                      </select>
                      
                      <?php submit_button(); ?>
                  </form>
                  
                  
                  <?php
                  if($this->_exceptions !== null && count($this->_exceptions) > 0) {
                      ?>
                      <table class="aftt_ldf" style="width: 68%;">
                          <tr class="rowtitle medium">
                              <th class="wide_text green" scope="col"><?php _e("Player", "aftt4club") ?></th>
                              <th class="wide_text green" scope="col"><?php _e("Membership", "aftt4club") ?></th>
                              <th class="wide_text green" scope="col"><?php _e("Week", "aftt4club") ?></th>
                              <th class="wide_text green" scope="col"><?php _e("Actions", "aftt4club") ?></th>
                          </tr>
                      
                      <?php 
                      foreach($exclusions as $excluded) {
                          echo "<tr>";
                          echo "<td>".$excluded["player"] ."</td>";
                          echo "<td>".$excluded["unique_id"]."</td>";
                          echo "<td>".$excluded["week"]."</td>";
                          echo '<td><form method="post" action="" name="remove-'.$excluded["unique_id"].'">
                                      <input type="submit" value="Supprimer" name="delete_exclusion" id="delete_exclusion" />
                                      <input type="hidden" name="unique_id" id="unique_id" value="'.$excluded["unique_id"].'" />
                                      <input type="hidden" name="week" id="week" value="'.$excluded["week"].'" />
                                    </form></td>';
                          echo "</tr>";
                      }
                      
                      ?>
                      </table>
                      
                      <?php 
                      
                  }
              }
              else { ?>
                  <span style="color: red; margin-top: 10px;"><?php _e("You must indicate your club index before accessing this content", "aftt4club") ?></span>
                  <?php
              } 
    }
    
    
    
    /**
     * Prints the "liste de forces" contents as PDF.
     */
    public function printPDF()
    {   
        $pdf = new PrintablePDF( 'P', 'mm', 'A4' );
        $pdf->AddPage();
        $pdf->SetLeftMargin(14);
        
        // Colors configuration
        list($border_r, $border_g, $border_b) = sscanf($this->colorsMap["borders"], "#%02x%02x%02x");
        
        $pdf->SetDrawColor( $border_r, $border_g, $border_b );
        
        /**
         * First sheet with global challenge ranking.
         */
        // Writing club and season infos.
        $pdf->Ln(0);
                
        $pdf->SetFont( 'Arial', 'B', 14 );
        $pdf->SetTextColor( 121, 174, 170);
        $pdf->SetFillColor( 255, 192, 0 );
        $pdf->Cell( 80, 9, " Challenge " . $this->_Club->getIndex(), 1, 0, 'C', true );
        
        if(file_exists("../../images/cup.png")) {
            $img_cup = "../../images/cup.png";
            $img_all_sets = "../../images/all_sets.png";
        }
        else {
            $img_cup = plugin_dir_url( __FILE__ ) . "../../images/cup.png";
            $img_all_sets = plugin_dir_url( __FILE__ ) . "../../images/all_sets.png";
        }
        
        $pdf->Image($img_cup, $pdf->GetX(), $pdf->GetY(), 9, 9);
        $pdf->SetX($pdf->GetX() + 9);
        $pdf->Cell( 93, 9, "Semaine: " . $this->challenge->getLastWeek(), 1, 0, 'C', true );
        
        
        // Create the table header row
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(0, 176, 80);
        $pdf->SetFont( 'Arial', 'B', 12 );
        $pdf->Ln( 14 );
        $pdf->SetFont( 'Arial', 'B', 10 );
        $pdf->Cell( 13, 6, "PL", 1, 0, 'C', true );
        $pdf->Cell( 56, 6, "Joueur / Joueuse", 1, 0, 'C', true );
        $pdf->Cell( 13, 6, "CLST", 1, 0, 'C', true );
        $pdf->Cell( 13, 6, "Vict", 1, 0, 'C', true );
        $pdf->Cell( 24, 6, "", 1, 0, 'C', true );
        $pdf->Cell( 13, 6, utf8_decode("Déf"), 1, 0, 'C', true );
        $pdf->Cell( 13, 6, utf8_decode("Joué"), 1, 0, 'C', true );
        $pdf->Cell( 24, 6, "%", 1, 0, 'C', true );
        $pdf->Cell( 13, 6, "PTS", 1, 0, 'C', true );
        
        $pdf->Ln( 6 );
        $pdf->SetFont( 'Arial', '', 12 );
        
        // Create the table data rows
        $pdf->SetFont( 'Arial', '', 10 );
        
        foreach ( $this->challenge_data as $key => $crow ) {
            $unbeaten = $this->challenge->isUnbeatenGames($crow["player_id"], $this->challenge->getLastWeek());
            $unbeaten_sets =  $this->challenge->isUnbeatenGamesSets($crow["player_id"], $this->challenge->getLastWeek());
            
            // Create the data cells
            list($r, $g, $b) = sscanf((($key % 2) == 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]), "#%02x%02x%02x");
            $pdf->SetFillColor( $r, $g, $b );
            
            $pdf->Cell( 13, 6, $key + 1, 1, 0, 'C', true );
            $pdf->Cell( 56, 6, "  " . utf8_decode($crow["player"]), 1, 0, '', true );
            $pdf->Cell( 13, 6, $crow["ranking"], 1, 0, 'C', true );
            $pdf->Cell( 13, 6, $crow["total_won"], 1, 0, 'C', true );
            
            $pdf->Cell( 24, 6, "", 1, 0, '', true);
            
            if($unbeaten) {
                $y = $pdf->GetY();
                $pdf->Image($img_cup, 112, $y, 6, 6); 
                $pdf->SetXY(133, $y);
            }
            
                
            if($unbeaten_sets){
                $y = $pdf->GetY();
                $pdf->Image($img_all_sets, 120, $y, 6, 6);
                $pdf->SetXY(133, $y);
            }
            
            $pdf->Cell( 13, 6, $crow["total_lost"], 1, 0, 'C', true );
            $pdf->Cell( 13, 6, $crow["total_played"], 1, 0, 'C', true );
            $pdf->Cell( 24, 6, number_format($crow["average"], 2) . "%", 1, 0, 'C', true );
            $pdf->Cell( 13, 6, $crow["points"], 1, 0, 'C', true );
            
            $pdf->Ln(6);
        }
        
        $pdf->SetTextColor( 121, 174, 170);
        $pdf->Ln(6);
        
        $pdf->Cell( 182, 6, utf8_decode("Seuls les joueurs ayant participé à au moins un match sont repris dans le challenge."), 0, 0, 'C', false );
        $pdf->Ln(10);
        
        $pdf->Image($img_cup, $pdf->GetX(), $pdf->GetY(), 9, 9);
        $pdf->SetX($pdf->GetX() + 9);
        $pdf->Cell( 182, 9, utf8_decode("Joueurs ayant gagné tous leurs matchs ce week end."), 0, 0, '', false );
        
        $pdf->Ln(10);
        
        $pdf->Image($img_all_sets, $pdf->GetX(), $pdf->GetY(), 9, 9);
        $pdf->SetX($pdf->GetX() + 9);
        $pdf->Cell( 182, 9, utf8_decode("Joueurs n'ayant perdu aucun set ce week end."), 0, 0, '', false );
        
        
        /**
         * Second page with challenge win/loose players details.
         */
        $pdf->AddPage();
        $pdf->Ln(0);
        $pdf->SetFont( 'Arial', 'B', 14 );
        $pdf->SetTextColor( 121, 174, 170);
        $pdf->SetFillColor( 255, 192, 0 );
        $pdf->Cell( 182, 9, utf8_decode("Victoires et défaites"), 1, 0, 'C', true );
        
        // Headers.
        $pdf->Ln(12);
        $pdf->SetFont( 'Arial', '', 10 );
        
        $pdf->SetFillColor( 0, 176, 80 );
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont( 'Arial', 'B', 10 );
        $pdf->Cell( 37.5, 9, "", 0, 0, 'C', true );
        $pdf->SetFillColor( 85, 142, 213 );
        
        $pdf->SetDrawColor( 0, 0, 0 );
        
        foreach($this->challenge->getAvailablesRankings() as $ranking) {
            $pdf->Cell(8.5, 9, $ranking, 1, 0, 'C', true);
        }
        
        $pdf->Ln(8.5);
        $pdf->SetFillColor( 0, 176, 80 );
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont( 'Arial', 'B', 10 );
        $pdf->Cell( 37.5, 9, "", 0, 0, 'C', true );
        
        for($i=0 ; $i < count($this->challenge->getAvailablesRankings()) ; $i++){
            $pdf->SetFillColor( 0, 176, 80 );
            $pdf->Cell(4.25, 9, "G", 1, 0, 'C', true);
            $pdf->SetFillColor( 222, 41, 22 );
            $pdf->Cell(4.25, 9, "P", 1, 0, 'C', true);
        }
            
        $pdf->Ln(9);
        
        // Playersd names and data.
        $pdf->SetDrawColor( $border_r, $border_g, $border_b );
        $pdf->SetFont( 'Arial', 'B', 7 );
        
        
        foreach($this->challenge_data as $key => $crow) {
            list($r, $g, $b) = sscanf((( ($key + 1) % 2) == 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]), "#%02x%02x%02x");            
            $pdf->SetFillColor( $r, $g, $b );
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(37.5, 6, $crow['player'], 1, 0, 'C', true);
            
            // NC
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("NC", array_keys($crow["victories"]))) ? $crow["victories"]["NC"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("NC", array_keys($crow["looses"]))) ? $crow["looses"]["NC"] : "", 1, 0, 'C', true);
            // E6
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("E6", array_keys($crow["victories"]))) ? $crow["victories"]["E6"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("E6", array_keys($crow["looses"]))) ? $crow["looses"]["E6"] : "", 1, 0, 'C', true);
            
            // E4
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("E4", array_keys($crow["victories"]))) ? $crow["victories"]["E4"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("E4", array_keys($crow["looses"]))) ? $crow["looses"]["E4"] : "", 1, 0, 'C', true);
            
            // E2
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("E2", array_keys($crow["victories"]))) ? $crow["victories"]["E2"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("E2", array_keys($crow["looses"]))) ? $crow["looses"]["E2"] : "", 1, 0, 'C', true);
            
            // E0
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("E0", array_keys($crow["victories"]))) ? $crow["victories"]["E0"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("E0", array_keys($crow["looses"]))) ? $crow["looses"]["E0"] : "", 1, 0, 'C', true);
            
            // D6
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("D6", array_keys($crow["victories"]))) ? $crow["victories"]["D6"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("D6", array_keys($crow["looses"]))) ? $crow["looses"]["D6"] : "", 1, 0, 'C', true);
            
            // D4
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("D4", array_keys($crow["victories"]))) ? $crow["victories"]["D4"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("D4", array_keys($crow["looses"]))) ? $crow["looses"]["D4"] : "", 1, 0, 'C', true);
            
            // D2
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("D2", array_keys($crow["victories"]))) ? $crow["victories"]["D2"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("D2", array_keys($crow["looses"]))) ? $crow["looses"]["D2"] : "", 1, 0, 'C', true);
            
            // D0
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("D0", array_keys($crow["victories"]))) ? $crow["victories"]["D0"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("D0", array_keys($crow["looses"]))) ? $crow["looses"]["D0"] : "", 1, 0, 'C', true);
            
            // C6
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("C6", array_keys($crow["victories"]))) ? $crow["victories"]["C6"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("C6", array_keys($crow["looses"]))) ? $crow["looses"]["C6"] : "", 1, 0, 'C', true);
            
            // C4
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("C4", array_keys($crow["victories"]))) ? $crow["victories"]["C4"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("C4", array_keys($crow["looses"]))) ? $crow["looses"]["C4"] : "", 1, 0, 'C', true);
            
            // C2
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("C2", array_keys($crow["victories"]))) ? $crow["victories"]["C2"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("C2", array_keys($crow["looses"]))) ? $crow["looses"]["C2"] : "", 1, 0, 'C', true);
            
            // C0
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("C0", array_keys($crow["victories"]))) ? $crow["victories"]["C0"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("C0", array_keys($crow["looses"]))) ? $crow["looses"]["C0"] : "", 1, 0, 'C', true);
            
            // B6
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("B6", array_keys($crow["victories"]))) ? $crow["victories"]["B6"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("B6", array_keys($crow["looses"]))) ? $crow["looses"]["B6"] : "", 1, 0, 'C', true);
            
            // B4
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("B4", array_keys($crow["victories"]))) ? $crow["victories"]["B4"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("B4", array_keys($crow["looses"]))) ? $crow["looses"]["B4"] : "", 1, 0, 'C', true);
            
            // B2
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("B2", array_keys($crow["victories"]))) ? $crow["victories"]["B2"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("B2", array_keys($crow["looses"]))) ? $crow["looses"]["B2"] : "", 1, 0, 'C', true);
            
            // B0
            $pdf->SetTextColor(0, 176, 80);
            $pdf->Cell(4.25, 6, (in_array("B0", array_keys($crow["victories"]))) ? $crow["victories"]["B0"] : "", 1, 0, 'C', true);
            $pdf->SetTextColor(222, 41, 22);
            $pdf->Cell(4.25, 6, (in_array("B0", array_keys($crow["looses"]))) ? $crow["looses"]["B0"] : "", 1, 0, 'C', true);
            
            $pdf->Ln(6);
            
        }
        
        // Includes js to open the print dialog box.
        $pdf->IncludeJS("this.print({bUI: false, bSilent: false, bShrinkToFit: true});");
        $pdf->Output( "challenge.pdf", "I" );
        
    }
    
    
}

// Exclusions.
if(isset($_POST["aftt4club_challenge_excluded"])) {
    $data = array();
    if(get_option("aftt4club_challenge_exclusions") !== false)
        $data = get_option("aftt4club_challenge_exclusions");
    
    $player = explode(",", $_POST["aftt4club_challenge_excluded"]);
    $data[] = array("unique_id" => $player[2], "week" => $_POST["aftt4club_challenge_excluded_week"], "player" => $player[0] . " " . $player[1]);
    update_option("aftt4club_challenge_exclusions", $data);
}


// Delete challenge exclusion.
if(isset($_POST["delete_exclusion"])) {
    $data = get_option("aftt4club_challenge_exclusions");
    foreach($data as $key => &$exception) {
      
        if(intval($exception["unique_id"]) == intval($_POST["unique_id"])){
            if(intval($exception["week"]) == intval($_POST["week"])) {
                unset($data[$key]);
            }
        }
    }
    
    update_option("aftt4club_challenge_exclusions", $data);
}



// Printing plugin version of "liste de forces as PDF".
if(isset($_GET["print_plugin_pdf"]) && $_GET["print_plugin_pdf"] == true) {
    $exceptions = base64_decode($_GET["exceptions"]);
    $challenge_pdf = new ClubMembersChallengeAdmin($_GET["club_index"], base64_decode($_GET['lg']), base64_decode($_GET["ps"]), unserialize($exceptions) );
    $cmap = unserialize(htmlspecialchars_decode(base64_decode($_GET['cmap'])));
    $challenge_pdf->setColorsMap($cmap);
    $challenge_pdf->printPDF();
    
} 