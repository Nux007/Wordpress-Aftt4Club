<?php 
include_once "../api/Aftt.php";
include_once "../api/AfttClubs.php";
include_once "../api/AfttMembers.php";
include_once "../lib/pdf/fpdf.php";

include_once "../views/admin/listeDeForces.php";

class LdfTest {
    
    public function __construct($func){
        
        $club = new AfttClub('', '');
        $club->init("H207");
        $ldf = new ListeDeForces($club);
        
        switch ($func){
            // Club members.
            case 0: $club->getMembers();
                    break;
            
            // Testing url handler.
            case 1 :   echo $ldf->printHTML();
                       break;
            
            // Testing pager retrieve.
            case 2 :   $ldf->printPDF();
                       break;
            
                       
            default: echo "No test defined\n";
            
        }
    }
    
}


new LdfTest(2);

?>