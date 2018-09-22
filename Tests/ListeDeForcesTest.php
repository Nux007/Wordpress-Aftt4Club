<?php 
include_once "../data/Aftt.php";
include_once "../data/listeDeForces.php";

class LdfTest {
    
    public function __construct($func){
        
        $ldf = new ListeDeForces("1068");
        
        switch ($func){
            // Testing url handler.
            case 0 :   echo $ldf->printHTML();
                       break;
            
            // Testing pager retrieve.
            case 1 :   $ldf->printPDF();
                       break;
                       
            default: echo "No test defined\n";
            
        }
    }
    
}


new LdfTest(0);

?>