<?php 
include_once "../api/Aftt.php";;
include_once "../views/admin/challenge.php";

class ChallengeTest {
    
    public function __construct($func){
        
        $challenge = new ClubChallenge("1068");
        
        switch ($func){
            // Testing url handler.
            case 0 :   echo $challenge->computePoints();
            break;
            
            default: echo "No test defined\n";
            
        }
    }
    
}


new ChallengeTest(0);

?>