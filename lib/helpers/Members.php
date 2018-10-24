<?php 
/**
* Aftt4Club is a wordpress plugin that helps to manage you Table Tennis club. 
* Copyright (C) 2018  Nux007
*    
* This file is part of Aftt4Club wordpress plugin.
*    
* Aftt4Club is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* (at your option) any later version.
*    
* Aftt4Club is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*    
* You should have received a copy of the GNU General Public License
* along with Aftt4Club. If not, see <http://www.gnu.org/licenses/>.
**/

class Member 
{
    
    private $_position;
    private $_rankingIndex;
    private $_lastName;
    private $_firstName;
    private $_ranking;
    private $_affiliation;
    
    /**
     * AfttMember constructor.
     * @param string $position
     * @param string $rankingIndex
     * @param string $affiliation
     * @param string $name
     * @param string $fname
     * @param string $ranking
     */
    public function __construct($position, $rankingIndex, $affiliation, $name, $fname, $ranking)
    {
        $this->_position = $position;
        $this->_rankingIndex = $rankingIndex;
        $this->_affiliation = $affiliation;
        $this->_lastName = $name;
        $this->_firstName = $fname;
        $this->_ranking = $ranking;
    }
    
    
    /**
     * Return the ldf position.
     * @return string
     */
    public function getPosition()
    {
        return $this->_position;
    }
    
    
    /**
     * Return the ranking index.
     * @return string
     */
    public function getRankingIndex()
    {
        return $this->_rankingIndex;
    }
    
    
    /**
     * Return the last name.
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }
    
    
    /**
     * Return the first name.
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }
    
    
    /**
     * Return the member ranking.
     * @return string
     */
    public function getRanking()
    {
        return $this->_ranking;
    }
    
    
    /**
     * Return member affiliation number.
     * @return string
     */
    public function getAffiliationNumber() 
    {
        return $this->_affiliation;
    }
    
    
}


?>