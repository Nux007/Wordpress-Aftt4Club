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

/**
 * Common TabT api calls used to fetch infos from afft website.
 *
 * All api calls made by this class are using the TabT api provided by G. Frenoy.
 * 
 * @category   Data handling
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @see        TabT-Api
 * @link       https://github.com/gfrenoy/TabT-API
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class AfttBasicInfos extends TabTApiCommon 
{
    
    
    /**
     * AfttBasicInfos constructor.
     * @param string $login
     * @param string $password
     */
    public function __construct($login='', $password='')
    {
        parent::__construct($login, $password);
    }
    
    
    /**
     * Fetch avlable clubs and return them.
     * @return AfttClub[]
     */ 
    public function getClubs()
    {
        $Response = $this->getApi()->GetClubs( array("Credentials" => $this->credentials) );
        $cdata = array();
        
        foreach ($Response->ClubEntries as $club) {
            
            $clubObj = new AfttClub($this->credentials["Account"], $this->credentials["Password"]);
            $cdata[] = $clubObj->init($club->UniqueIndex, $club->LongName, $club->Category, $club->CategoryName);
        }
        
        return $cdata;
    }
    
}


/**
 * This class handle a Club object that is used to contain/fetch all club related data
 *
 * @category   Data handling
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @see        TabT-Api
 * @link       https://github.com/gfrenoy/TabT-API
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class AfttClub extends TabTApiCommon
{
    
    private $_index;
    private $_name;
    private $_category;
    private $_category_name;
    
    
    /**
     * AfttClub class constructor.
     * @param string $login
     * @param string $password
     */
    public function __construct($login=null, $password=null) 
    {
        if(isset($login) && isset($password)) {
            parent::__construct($login, $password);
        }
    }
    
    
    /**
     * Object initialization from function call.
     * @param string $index
     * @param string $name
     * @param string $category
     * @param string $category_name
     * @return AfttClub
     */
    public function init($index, $name=null, $category=null, $category_name=null)
    {
        $this->_index = $index;
        
        if(isset($name) && isset($category) && isset($category_name)) {
            // If manual init...
            $this->_name = $name;
            $this->_category = $category;
            $this->_category_name = $category_name;
        } else {
            parent::__construct('', '');
            // else, we only have the club index, so fetching infos.
            $this->_populateBasics();
        }
            
        return $this;
    }
    
    
    /**
     * Return the club index.
     * @return string
     */
    public function getIndex()
    {
        return $this->_index;
    }
    
    
    /**
     * Return the club name ( TabT club->LongName )
     * @return string
     */
    public function getName() 
    {
        return $this->_name;
    }
    
    
    /**
     * Return the club category.
     */
    public function getCategory() 
    {
        return $this->_category;
    }
    
    
    /**
     * Return the club category name.
     * @return string
     */
    public function getCategoryName() 
    {
        return $this->_category_name;
    }
    
    
    /**
     * Return the club members as a list of AfttMember.
     * @return AfttMember[]|boolean
     */
    public function getMembers()
    {
        $members = array();
        $Response = $this->getApi()->GetMembers(array("Credentials" => $this->credentials, "Club" => $this->getIndex()));
        
        if($Response->MemberCount > 0) {
            
            foreach($Response->MemberEntries as $MemberEntry) {
                $members[] = new AfttMember(
                                 $MemberEntry->Position, $MemberEntry->RankingIndex, $MemberEntry->UniqueIndex,
                                 $MemberEntry->LastName, $MemberEntry->FirstName, $MemberEntry->Ranking
                             );
            }
            
            return $members;
        }
        
        return false;
    }
    
    
    /**
     * Return the link to Aftt version of current "Liste de Forces" for current club.
     * @return string
     */
    public function getAfttLdfLink()
    {
        return $this->BASE_URL . "/club/" . $this->getIndex() . "/members/pdf";
    }
    
    
    /**
     * Populate object.
     * @return boolean
     */
    private function _populateBasics()
    {
        $Response = $this->getApi()->GetClubs(array("Credentials" => $this->credentials, "Club" => $this->getIndex()));
        
        if($Response->ClubCount > 0) {
            
            $this->_name = $Response->ClubEntries->LongName;
            $this->_category = $Response->ClubEntries->Category;
            $this->_category_name = $Response->ClubEntries->CategoryName;
            return true;
        }
        
        return false;
    }
    
    
}

?>