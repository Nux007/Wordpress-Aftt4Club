<?php 

/**
 * Clubs common data.
 */
class AfttBasicInfos extends TabTApiCommon {
    
    // Parent construct.
    public function __construct($login='', $password=''){
        parent::__construct($login, $password);
    }
    
    // Return available clubs.
    public function getClubs(){
        $Response = $this->getApi()->GetClubs(array("Credentials" => $this->credentials));
        $cdata = array();
        foreach ($Response->ClubEntries as $club){
            $clubObj = new AfttClub($this->credentials["Account"], $this->credentials["Password"]);
            $cdata[] = $clubObj->init($club->UniqueIndex, $club->LongName, $club->Category, $club->CategoryName);
        }
        
        return $cdata;
    }
    
}


/**
 * Club specific data.
 */
class AfttClub extends TabTApiCommon{
    
    public $index;
    public $name;
    public $category;
    public $category_name;
    
    // Basic club interface constructor.
    public function __construct($login=null, $password=null) {
        if(isset($login) && isset($password))
            parent::__construct($login, $password);
    }
    
    
    // Object init by users.
    public function init($index, $name=null, $category=null, $category_name=null){
        $this->index = $index;
        if(isset($name) && isset($category) && isset($category_name)){
            $this->name = $name;
            $this->category = $category;
            $this->category_name = $category_name;
        }
        else
            $this->__populateBasics();
            
        return $this;
    }
    
    
    // Populate object.
    private function __populateBasics(){
        $Response = $this->getApi()->GetClubs(array("Credentials" => $this->credentials, "Club" => $this->index));
        if($Response->ClubCount > 0){
            $this->name = $Response->ClubEntries->LongName;
            $this->category = $Response->ClubEntries->Category;
            $this->category_name = $Response->ClubEntries->CategoryName;
            return true;
        }
        return false;
    }
    
    
    // Return the club members as a list of AfttMember.
    public function getMembers(){
        $members = array();
        $Response = $this->getApi()->GetMembers(array("Credentials" => $this->credentials, "Club" => $this->index));
        if($Response->MemberCount > 0){
            foreach($Response->MemberEntries as $MemberEntry){
                $members[] = new AfttMember($MemberEntry->Position, $MemberEntry->RankingIndex, $MemberEntry->UniqueIndex,
                                            $MemberEntry->LastName, $MemberEntry->FirstName, $MemberEntry->Ranking);
               
            }
            
            return $members;
        }
        return false;
    }
    
    
    // Return the Att version of current "Liste de Forces" for provided club index.
    public function getAfttLdfLink(){
        $ldf_url = $this->base_url . "/index.php?menu=6&club_id=" . $this->club_indice . "&pdf=1&compute_pdf=1";
        return $ldf_url;
    }
}

?>