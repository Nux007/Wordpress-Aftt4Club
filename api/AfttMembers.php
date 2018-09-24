<?php 


class AfttMember {

    public $position;
    public $rankingIndex;
    public $lastName;
    public $firstName;
    public $ranking;
    public $affiliation;
    
   
    public function __construct($position, $rankingIndex, $affiliation, $name, $fname, $ranking){
        $this->position = $position;
        $this->rankingIndex = $rankingIndex;
        $this->affiliation = $affiliation;
        $this->lastName = $name;
        $this->firstName = $fname;
        $this->ranking = $ranking;
    }
}


?>