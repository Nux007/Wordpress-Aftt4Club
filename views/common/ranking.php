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
 * Crate a generic view for club divisions ranking.
 *
 * @category   Common divisions ranking managment views.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */

class DivisionsRankingView extends ColorMap
{
    
    private $_ranking;
    private $_results;
    private $_season;
    private $_week;
    protected $_divisions;
    protected $_club_index;
    
    
    /**
     * DivisionsRanking constructor.
     * @param string $club_index
     */
    public function __construct($club_index, $exclusions)
    {
        $this->_club_index = $club_index;
        $dvobj = new AfttDivisions($club_index, $exclusions);
        $this->_season = $dvobj->getSeason();
        $this->_week = $dvobj->getCurrentWeek();
        $this->_divisions = $dvobj->getDivisions(); 
        
        foreach($this->_divisions as $division) {
            $this->_ranking[$division->getDivisionId()] = $dvobj->getDivisionRanking($division->getDivisionId());
            $this->_results[$division->getDivisionId()] = $dvobj->getDivisionResults($division->getDivisionId(), $this->getCurrentWeek());
        }
        
    }
    
    
    
    /**
     * Return current target season.
     * @return string
     */
    public function getSeason()
    {
        return $this->_season;
    }
    
    
    /**
     * Return the current week.
     * @return string
     */
    public function getCurrentWeek()
    {
        return $this->_week;
    }
    
    
    /**
     * Print the division ranking contents as HTML.
     */
    public function printHTML()
    {
        
        if(file_exists("../css/shortcodes.css")) {
            // Does not exists outside tests context, so, using this small hack for tests purpose !
            echo '<link rel="stylesheet" type="text/css" href="../css/shortcodes.css" media="screen" />';
        }
        
        ?>
        
        <div id="weekname"><span>Semaine: <?php echo $this->getCurrentWeek(); ?></span></div>
        <div class="wrap">
        <?php 
        foreach($this->_ranking as $divId => $ranking) {
            $style   = "border: 1px solid " . $this->colorsMap["borders"] . ";";
            $results = $this->_results[$divId];
        ?>
            <div class="wrap" id="wrap" style="margin-bottom: 60px;text-align: center;">
                <div id="div_result_header" style="display: flex; justify-content: center; background-color: <?php echo $this->colorsMap["header"]; ?>; text-align: center;">
                    <p id='division_name'>
                        <?php
                        $headersColor = $this->colorsMap["textHeaders"];
                        $thHeadersColor = $this->colorsMap["textThead"];
                        ?>
                        <span style='color:<?php echo $headersColor; ?>; font-size:26px; font-weight: bold;'><?php echo $ranking[0]->getDivisionName(); ?></span><br />
                    </p> 
                </div>
                
                <!-- Last known results table -->
                <table class="div_result" id="ldf" style="border: 1px solid <?php echo $this->colorsMap["borders"]; ?>;">
                  <thead>
                    <tr class="rowtitle" style="color: <?php echo $thHeadersColor; ?>">
                      <th class="team" scope="col"><?php _e("Home", "aftt4club") ?></th>
                      <th id="team" scope="col"><?php _e("Away", "aftt4club") ?></th>
                      <th class="number" scope="col"><?php _e("Score", "aftt4club") ?></th>
                    </tr>
                  </thead>
                  <tbody>  
                <?php
                
                $counter = 1;
                foreach($results as $key => $game) {
                    $fill = " background-color: " . (($counter %2) != 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]);
                    $owner_fill = " background-color: #FFFF6B;";
                    $selected = ($game->getHomeClubIndice() == $this->_club_index || $game->getAwayClubIndice() == $this->_club_index) ? $owner_fill : $fill;
              
                    echo "<tr style='". $style . $fill . "'>";
                    echo "<td class='team' style='". $style . $selected . "'> ".$game->getHomeTeam() ."</td>";
                    echo "<td class='team' style='"  . $style . $selected . "'> ".$game->getAwayTeam()."</td>";
                    echo "<td class='number' style='". $style . $selected . "'> ".$game->getScore()."</td>";
                    echo "</tr>";
                    $counter++;
                }
                ?>
                     </tbody>
                  </table>
              
              
                
                <!-- Global ranking table -->
                <table class="div_result" id="ldf" style="border: 1px solid <?php echo $this->colorsMap["borders"]; ?>;">
                  <thead>                  
                    <tr class="rowtitle" style="color: <?php echo $thHeadersColor; ?>">
                      <th class="number" scope="col"><?php _e("Place", "aftt4club") ?></th>
                      <th id="team" scope="col"><?php _e("Team", "aftt4club") ?></th>
                      <th class="number" scope="col"><?php _e("Played", "aftt4club") ?></th>
                      <th class="number" scope="col"><?php _e("Won", "aftt4club") ?></th>
                      <th class="number" scope="col"><?php _e("Lost", "aftt4club") ?></th>
                      <th class="number" scope="col"><?php _e("Nul", "aftt4club") ?></th>
                      <th class="number" scope="col"><?php _e("Points", "aftt4club") ?></th>
                    </tr>
                  </thead>
                  <tbody>  
            <?php
                        
            foreach($ranking as $team) {
                $fill = " background-color: " . (($team->getPosition() %2) != 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]);
                $owner_fill = " background-color: #FFFF6B;";
                $selected = ($team->getTeamClub() == $this->_club_index) ? $owner_fill : $fill;
          
                echo "<tr style='". $style . $fill . "'>";
                echo "<td class='number' style='". $style . $selected . "'> ".$team->getPosition() ."</td>";
                echo "<td class='team' style='"  . $style . $selected . "'> ".$team->getTeam()."</td>";
                echo "<td class='number' style='". $style . $selected . "'> ".$team->getGamesPlayed()."</td>";
                echo "<td class='number' style='". $style . $selected . "'> ".$team->getGamesWon()."</td>";
                echo "<td class='number' style='". $style . $selected . "'> ".$team->getGamesLost()."</td>";
                echo "<td class='number' style='". $style . $selected . "'> ".$team->getGamesDraw()."</td>";
                echo "<td class='number' style='". $style . $selected . "'> ".$team->getPoints()."</td>";
                echo "</tr>";
            }
            ?>
                 </tbody>
              </table>
            </div>
        <?php 
        }
        ?>
        </div>
        <?php
    }  

    /**
     * Return the ranking list.
     * @return array
     */
    public function getRankings()
    {
        unset($this->_ranking["3652"]);
        return $this->_ranking;   
    }
    
    
    /**
     * Return the results list.
     * @return array
     */
    public function getResults()
    {
        return $this->_results;
    }
}

?>