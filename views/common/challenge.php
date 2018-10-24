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


/**
 * Create a common view for club challenge.
 *
 * @category   View, Members managment.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class ChallengeView  extends ColorMap
{
    
    protected $_Club;
    protected $challenge; 
    protected $challenge_data;
    
    /**
     * ClubMembersChallenge common view constructor.
     * @param AfttClub $Club
     * @param boolean $colors
     */
    public function __construct($index, $colors=false, $login, $password, $exclusions)
    {
        $Club = new AfttClub($login, $password);
        $Club->init($index);
        $this->_Club = $Club;
        
        if($colors !== false) {
            $this->colorsMap = $colors;
        }
        
        $this->challenge = $this->_Club->getChallenge($exclusions);
        $this->challenge_data = $this->challenge->getChallengeData();
    }
    
    
    /**
     * Return the club obj.
     * @return AfttClub
     */
    public function getClub()
    {
        return $this->_Club;
    }
    
    
    /**
     * Print the "Challenge" contents as HTML.
     * @param boolean $headers display the header or not.
     */
    public function printHTML()
    {
        if(file_exists("../css/shortcodes.css"))
            echo '<link rel="stylesheet" type="text/css" href="../css/shortcodes.css" media="screen" />';
        
        ?>
        
        <div class="wrap" style="text-align: center;">
            <!-- Headers. -->
            <div class="wrap" id="wrap">
                <div id="challenge_header"> 
                    <div id='left'><span><?php echo "Challenge " . $this->getClub()->getIndex(); ?></span></div>
                    <div id='cup' ><img alt="cup" src="<?php echo plugin_dir_url( __FILE__ ); ?>../../images/cup.png"></img></div>
                    <div id='right'><span><?php echo __("Week: ", "aftt4club") . $this->challenge->getLastWeek(); ?></span></div>
                </div>
              
            <!-- Challenge contents. -->
                <table class="aftt_ldf" id="challenge_data" style="border: 1px solid <?php echo $this->colorsMap["borders"]; ?>;width: 100%;border-spacing: 0;">
                  <thead>                  
                    <tr class="rowtitle medium">
                      <th class="two_digits green" scope="col"><?php _e("PL", "aftt4club") ?></th>
                      <th class="wide_text green"  scope="col"><?php _e("Player", "aftt4club") ?></th>
                      <th class="two_digits green" scope="col"><?php _e("Rank", "aftt4club") ?></th>
                      <th class="two_digits green" scope="col"><?php _e("Vict", "aftt4club") ?></th>
                      <th class="small_text green" scope="col"></th>
                      <th class="two_digits green" scope="col"><?php _e("Lss", "aftt4club") ?></th>
                      <th class="two_digits green" scope="col"><?php _e("Played", "aftt4club") ?></th>
                      <th class="four_digits green" scope="col">%</th>
                      <th class="four_digits green" scope="col"><?php _e("PTS", "aftt4club") ?></th>
                    </tr>
                  </thead>
                  <tbody>  
        <?php
        
        $style= "border: 1px solid " . $this->colorsMap["borders"] . ";";
        $position = 1;
        foreach($this->challenge_data as $crow) {
            $images = "";
            $unbeaten = $this->challenge->isUnbeatenGames($crow["player_id"], $this->challenge->getLastWeek());
            $unbeaten_sets =  $this->challenge->isUnbeatenGamesSets($crow["player_id"], $this->challenge->getLastWeek());
            
            if($unbeaten)
                $images .= '<img class="unbeaten" alt="unbeaten" src="' . plugin_dir_url( __FILE__ ) . '../../images/cup.png" />';
            
            if($unbeaten_sets)
                $images .= '<img class="unbeaten" alt="unbeaten sets" src="' . plugin_dir_url( __FILE__ ) . '../../images/all_sets.png" />';
            
            $fill = " background-color: " . (($position %2) != 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]);
      
            echo "<tr class='medium' style='". $style . $fill . "'>";
            
            echo "<td class='two_digits'  style='". $style . $fill . "'>".  $position             ."</td>";
            echo "<td class='wide_text'   style='". $style . $fill . "'> ". $crow["player"]       ."</td>";
            echo "<td class='two_digits'  style='". $style . $fill . "'>".  $crow["ranking"]      ."</td>";
            echo "<td class='two_digits'  style='". $style . $fill . "'>".  $crow["total_won"]    ."</td>";
            
            echo "<td class='small_text'  style='". $style . $fill . "'> " . $images . "</td>";
            
            echo "<td class='two_digits'  style='". $style . $fill . "'>".  $crow["total_lost"]   ."</td>";
            echo "<td class='two_digits'  style='". $style . $fill . "'>".  $crow["total_played"] ."</td>";
            echo "<td class='four_digits' style='". $style . $fill . "'>".  number_format($crow["average"], 2)  ."%</td>";
            echo "<td class='four_digits' style='". $style . $fill . "'>".  $crow["points"]       ."</td>";
            
            echo "</tr>";
            $position++;
        }
        
        ?>
                  </tbody>
                </table>
                
                
                <!-- Footer note. -->
                <div id='challenge_footer'>
                    <div>
                        <img alt='cup' src="<?php echo plugin_dir_url( __FILE__ ); ?>../../images/cup.png"></img>
                        <span><?php _e("Players that won all thier games this week.", "aftt4club") ?></span>
                    </div>
                    
                    <div>
                        <img alt='perfect_sets' src="<?php echo plugin_dir_url( __FILE__ ); ?>../../images/all_sets.png"></img>
                        <span><?php _e("Players that wan all their sets this week.", "aftt4club") ?></span>
                    </div>
                    
                </div>
            </div>
        </div>
        <?php
    }  
    
    
}

?>