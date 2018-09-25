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
 * Crate a frontend view for club 'liste de forces'.
 *
 * @category   Frontend Members managment view.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class ListeDeForcesFront extends ListeDeForces
{
    
    /**
     * ListeDeForcesFront constructor
     * @param string $index
     */
    public function __construct($club_index)
    {
        $Club = new AfttClub();
        $Club->init($club_index);
        parent::__construct($Club);
    }
    
    
    /**
     * Print ldf as html.
     */
    public function print()
    {
        $this->setColorsMap(
            (get_option('aftt4club_ldf_header_color') !== false) ? get_option('aftt4club_ldf_header_color') : "#9cfff4",
            (get_option('aftt4club_ldf_th_color') !== false) ? get_option('aftt4club_ldf_th_color') : "#9cfff4",
            (get_option('aftt4club_ldf_borders_color') !== false) ? get_option('aftt4club_ldf_borders_color') : "#f0f0f0",
            (get_option('aftt4club_ldf_nt_child_even_color') !== false) ? get_option('aftt4club_ldf_nt_child_even_color') : "#f0f0f0",
            (get_option('aftt4club_ldf_nt_child_odd_color') !== false) ? get_option('aftt4club_ldf_nt_child_odd_color') : "#FFFFFF"
        );
        
        $this->printHTML($headers=false);
    }
}


?>