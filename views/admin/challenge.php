<?php 

class ClubChallenge {
    
    private $club_indice;
    private $afttMembers;
    private $classements = array("NC" => 1, "E6" => 2, "E4" => 3, "E2" => 4, "E0" => 5, "D6" => 6, "D4" => 7, "D2" => 8, "D0" => 9,
                                 "C6" => 10, "C4" => 11, "C2" => 12, "C0" => 13, "B6" => 14, "B4" => 15, "B2" => 16, "B0" => 17);
    protected $members;
    
    public function __construct($club_indice){
        $this->club_indice = $club_indice;
        $this->afttMembers = new AfttClubMembers();
        $this->members = $this->afttMembers->getClubMembers($club_indice);
    }
    
    
    // Compute challenge points based on an array of lost and loose...
    public function computePoints(/*$member*/){
        $member = array();
        $data_win = array("E6", "E4", "E6", "E2", "E0", "D6", "C6", "D4", "W0");
        $data_lost = array("E6", "E2");
        $member["classement"] = "E0";
        
        if(array_key_exists($member["classement"], $this->classements)){
            // Total won points.
            $total_won_points = 0;
            foreach($data_win as $won){
                if($won == "WO" || $won == "W0")
                    $total_won_points++;
                else
                    if ($this->classements[$won] > $this->classements[$member["classement"]])
                        $total_won_points += $this->classements[$won] - $this->classements[$member["classement"]] + 1;
                    else 
                        $total_won_points++;
            }
            
            // Total loose points.
            $total_loose_points = 0;
            foreach($data_lost as $lost){
                if ($this->classements[$lost] < $this->classements[$member["classement"]]) {
                    $total_loose_points += $this->classements[$member["classement"]] - $this->classements[$lost]; 
                }
            }
            
            return $total_won_points - $total_loose_points;
        }
        
        return false;
    }
    
    
    // Prints the challenge form as html
    public function printHTML($bttons=false){
        
    }
}

?>