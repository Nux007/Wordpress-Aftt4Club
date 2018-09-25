<?php 
/**
 Aftt4Club is a wordpress plugin that helps to manage you Table Tennis club.
 Copyright (C) 2018  Nux007
 
 This file is part of Aftt4Club wordpress plugin.
 
 Aftt4Club is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 (at your option) any later version.
 
 Aftt4Club is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Aftt4Club. If not, see <http://www.gnu.org/licenses/>.
 **/

include_once "../api/Aftt.php";
include_once "../api/AfttClubs.php";
include_once "../api/AfttMembers.php";
include_once "../lib/pdf/fpdf.php";

include_once "../views/common/listeDeForces.php";
include_once "../views/admin/listeDeForcesAdmin.php";
include_once "../views/front/listeDeForcesFront.php";

class LdfTest {
    
    public function __construct($func)
    {
        
        $ldf_admin = new ListeDeForcesAdmin("H207");
        $ldf_front = new ListeDeForcesFront("H207");
        
        switch ($func) {
            
            // Testing url handler.
            case 1 :   echo $ldf_admin->print();
                       break;
            
            // Testing pager retrieve.
            case 2 :   $ldf_admin->printPDF();
                       break;
            
            case 3:    $ldf_front->print();
                       break;
            
            default: echo "No test defined\n";
        }
    }
    
    
}


new LdfTest(1);

?>