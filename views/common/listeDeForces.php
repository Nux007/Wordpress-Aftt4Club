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
 * Crate a generic view for club 'liste de forces'.
 * 
 * Used to define common functions and properties for both, 
 * admin and front 'liste de forces' views.
 *
 * @category   Common Members managment views.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class ListeDeForces extends ColorMap
{
    private $_Club;
    
    /**
     * ListeDeForces common view constructor.
     * @param AfttClub $Club
     * @param boolean $colors
     */
    public function __construct($Club, $colors=false)
    {
        $this->_Club = $Club;
        
        if($colors !== false) {
            $this->colorsMap = $colors;
        }
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
     * Print the "Liste de Force" contents as HTML.
     * @param boolean $headers display the header or not.
     */
    protected function printHTML($headers=true)
    {   
        if(file_exists("../css/shortcodes.css")) {
            // Does not exists outside tests context, so, using this sall hack for tests purpose !
            echo '<link rel="stylesheet" type="text/css" href="../css/shortcodes.css" media="screen" />';
        }
        
        if($headers) {
            $subline = "Liste de forces - Saison: " . $this->_Club->getSeason();
            $header = $this->_Club->getIndex() . " - " . $this->_Club->getName();
        }
        ?>
        
        <div class="wrap"  style="text-align: center;">
            <div class="wrap" id="wrap">
                <?php 
                if($headers) {
                ?>
                    <div id="ldf_header" style="background-color: <?php echo $this->colorsMap["header"]; ?>">
                        <p id='club_name'>
                            <span style='font-size:26px; font-weight: bold;'><?php echo $header; ?></span><br />
                            <span style='font-size: 16px;'><?php echo $subline; ?></span>
                        </p> 
                    </div>
                <?php
                }
                ?>
                <table class="aftt_ldf" id="ldf" style="border: 1px solid <?php echo $this->colorsMap["borders"]; ?>;width:100%; border-spacing: 0;">
                  <thead>
                  
                  <?php $border = "background-color: " . $this->colorsMap["th"] . ";text-align: center;"; ?>
                  
                    <tr class="rowtitle" style="<?php echo $border; ?>">
                      <th id='ordre' scope="col" style="<?php echo $border; ?>"><?php _e("Order", "aftt4club") ?></th>
                      <th id='index' scope="col" style="<?php echo $border; ?>"><?php _e("Index", "aftt4club") ?></th>
                      <th id='affiliation' scope="col" style="<?php echo $border; ?>"><?php _e("Membership", "aftt4club") ?></th>
                      <th id='nom' scope="col" style="<?php echo $border; ?>"><?php _e("Last Name", "aftt4club") ?></th>
                      <th id='prenom' scope="col" style="<?php echo $border; ?>"><?php _e("First Name", "aftt4club") ?></th>
                      <th id='classement' scope="col" style="<?php echo $border; ?>"><?php _e("Rk.", "aftt4club") ?></th>
                    </tr>
                  </thead>
                  <tbody>  
        <?php
        
        $style= "border: 1px solid " . $this->colorsMap["borders"] . ";";
        
        foreach($this->_Club->getMembers() as $Member) {
            $fill = " background-color: " . (($Member->getPosition() %2) != 0 ? $this->colorsMap["even"] : $this->colorsMap["odd"]);
      
            echo "<tr style='". $style . $fill . "'>";
            echo "<td id='ordre' style='". $style . $fill . "'>".$Member->getPosition() ."</td>";
            echo "<td id='index' style='". $style . $fill . "'> ".$Member->getRankingIndex()."</td>";
            echo "<td id='affiliation' style='". $style . $fill . "'>".$Member->getAffiliationNumber()."</td>";
            echo "<td id='nom' style='". $style . $fill . "'>".$Member->getLastName()."</td>";
            echo "<td id='prenom' style='". $style . $fill . "'>".$Member->getFirstName()."</td>";
            echo "<td id='classement' style='". $style . $fill . "'>".$Member->getRanking()."</td>";
            echo "</tr>";
        }
        ?>
                  </tbody>
                </table>
            </div>
        </div>
        <?php
    }  
    
    
}

?>  