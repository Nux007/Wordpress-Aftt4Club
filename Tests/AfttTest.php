<?php 

include "../api/Aftt.php";
include "../api/AfttClubs.php";


class AfttBasicInfosTest {
    
    public function __construct($func){
        
        foreach($func as $test)
            switch ($test){
                
                // Testing club indices retrieve.
                case 0 : $tabt = new AfttBasicInfos('', '');
                         foreach($tabt->getClubs() as $club) {
                             echo "Indice: " . $club->index . "<br />";
                             echo "Nom: " . $club->name . "<br />";
                             echo "Categorie: " . $club->category . "<br />";
                             echo "Category name: " . $club->category_name . "<br /><br />";
                         }
                         break;
                         // Test getting current season.
                case 1 : $tabt = new AfttBasicInfos('', '');
                         $season = $tabt->getSeason();
                         echo $season;
                         break;
                
                /*
                // Testing getting club members.
                case 3 : print_r($tester->getClubMembers("1068"));
                         break;
                         
                // Test detting LDF as Aftt version.
                case 6 : $tester->getAfttLdf("1068");
                         break;
                */         
                default: echo "No test defined\n";
                
            }
    }
    
}


new AfttBasicInfosTest([1, 0]);


?>  