<?php 

include "../data/Aftt.php";

class AfttBasicScraperTest {
    
    public function __construct($func){
        
        $tester = new AfttScraper();
        
        foreach($func as $test)
            switch ($test){
                // Testing url handler.
                case 0 : $source = AfttUrlHandler::__fetchData("http://www.google.be");
                         file_put_contents("source_handler.txt", $source);
                         break;
                
                // Testing pager retrieve.
                case 1 : $res = $tester->getPagerLinks("/clubs");
                         foreach($res as $link)
                             echo $link . "<br />";
                         
                         echo "<br /><br />";
                          
                         $res2 = $tester->getPagerLinks("/divisions");
                         foreach($res2 as $link2)
                             echo $link2 . "<br />";    
                         break;
                
                // Testing club indices retrieve.
                case 2 : $data = $tester->getClubsIndices();
                         if($data == false)
                             echo "Nothing found\n";
                         var_dump($data);
                         break;
                
                // Testing getting club members.
                case 3 : print_r($tester->getClubMembers("1068"));
                         break;
                
                // Testing club full name and aftt index value.
                case 4 : $name = $tester->getClubNameAndIndice("1068");
                         echo $name["name"] . " - " . $name["indice"];
                         break;
                
                // Test getting current season.
                case 5 : $season = $tester->getCurrentSeason();
                         echo $season;
                         break;
                         
                // Test detting LDF as Aftt version.
                case 6 : $tester->getAfttLdf("1068");
                         break;
                         
                default: echo "No test defined\n";
                
            }
    }
    
}


new AfttBasicScraperTest([6]);


?>  