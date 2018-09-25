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

include "../api/Aftt.php";
include "../api/AfttClubs.php";
include "../api/AfttMembers.php";


class AfttBasicInfosTest 
{
    
    public function __construct($func)
    {
        
        foreach($func as $test) {
            
            switch ($test) {
                
                // Testing club indices retrieve.
                case 0 : $tabt = new AfttBasicInfos('', '');
                         foreach($tabt->getClubs() as $club) {
                             echo "Indice: " . $club->getIndex() . "<br />";
                             echo "Nom: " . $club->getName() . "<br />";
                             echo "Categorie: " . $club->getCategory() . "<br />";
                             echo "Category name: " . $club->getCategoryName() . "<br /><br />";
                         }
                         break;
                
                // Test getting current season.
                case 1 : $tabt = new AfttBasicInfos('', '');
                         $season = $tabt->getSeason();
                         echo $season;
                         break;
                
                // Testing getting club members.
                case 2 : $club = new AfttClub('', '');
                         $club->init("H207");
                         print_r($club->getMembers());
                         break;
                         
                         
                default: echo "No test defined\n";
                
            }
        }
    }
    
    
}


new AfttBasicInfosTest([2]);


?>  