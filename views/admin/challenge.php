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
 * Create a view for club challenge computing each club member point.
 *
 * @category   Members managment.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class ClubChallenge 
{
    protected $members;
    private $_club_indice;
    private $_afttMembers;
    private $_classements = array("NC" => 1, "E6" => 2, "E4" => 3, "E2" => 4, "E0" => 5, "D6" => 6, "D4" => 7, "D2" => 8, "D0" => 9,
                                 "C6" => 10, "C4" => 11, "C2" => 12, "C0" => 13, "B6" => 14, "B4" => 15, "B2" => 16, "B0" => 17);    
    
    /**
     * ClubChallenge constructor.
     * @param string $club_indice
     */
    public function __construct($club_indice)
    {
        $this->_club_indice = $club_indice;
        $this->_afttMembers = new AfttClubMembers();
        
        $this->members = $this->afttMembers->getClubMembers($club_indice);
    }
    
    
    /**
     * Compute challenge points based on an array of lost and loose...
     * @param AfttMember
     * @return integer
     */
    public function computePoints($member)
    {
        /*
         * work in progress.
         */
        $member = array();
        $data_win = array("E6", "E4", "E6", "E2", "E0", "D6", "C6", "D4", "W0");
        $data_lost = array("E6", "E2");
        $member["classement"] = "E0";
        
        if(array_key_exists($member["classement"], $this->_classements)) {
            
            // Computing total won points for the given member.
            $total_won_points = 0;
            
            foreach($data_win as $won) {
                
                if($won == "WO" || $won == "W0") {
                    $total_won_points++;
                } else {
                    
                    if ($this->_classements[$won] > $this->_classements[$member["classement"]]) {
                        $total_won_points += $this->_classements[$won] - $this->_classements[$member["classement"]] + 1;
                    } else {
                        $total_won_points++;
                    }  
                }
            }
            
            // Computing total loose points for he given member.
            $total_loose_points = 0;
            
            foreach($data_lost as $lost) {
                
                if ($this->_classements[$lost] < $this->_classements[$member["classement"]]) {
                    $total_loose_points += $this->_classements[$member["classement"]] - $this->_classements[$lost]; 
                }
            }
            
            return $total_won_points - $total_loose_points;
        }
        
        return false;
    }
    
    
}

?>