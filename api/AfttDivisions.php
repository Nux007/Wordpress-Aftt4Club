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



/**
 * Club divisions managment.
 *
 * @category   Divisions managment.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class AfttDivisions extends TabTApiCommon 
{
    private $_club_index;
    private $_exclusions;
    
    /**
     * AfttFivision constructor.
     * @param string $club_index
     * @param string $login
     * @param string $password
     */
    public function __construct($club_index, $exclusions, $login='', $password='')
    {
        parent::__construct($login, $password);
        $this->_exclusions = $exclusions;
        $this->_club_index = $club_index;
    }
    
    
    /**
     * Return the club indice.
     * @return string
     */
    public function getClubIndice()
    {
        return $this->_club_index;
    }
    
    
    
    /**
     * Return the current week if found,false otherwise.
     * @return string | boolean.
     */
    public function getCurrentWeek()
    {
        $div = $this->getDivisions();
        $week = "0";
        if(count($div) > 0) {
            $Response = $this->getApi()->GetMatches( array("Credentials" => $this->credentials, "Club" => $this->_club_index, "WithDetails" => "1") );
           
            foreach($Response->TeamMatchesEntries as $game){

                $details = $game->MatchDetails;
                if( $details->DetailsCreated == '1')
                    if((int)$week < (int)$game->WeekName)
                        $week = $game->WeekName;
            }
            
            return $week;
        }
        
        return false;
    }
    
    
    /**
     * Return available divisions as an array of Division.
     * @return Division[]
     */
    public function getDivisions()
    {   
        $Response = $this->getApi()->GetClubTeams( array("Credentials" => $this->credentials, "Club" => $this->getClubIndice(), "Season" => $this->getSeasonParam()) );
        $ddata = array();
        
        foreach ($Response->TeamEntries as $team) {
            if(isset($this->_exclusions) && count($this->_exclusions) > 0) {
                
                foreach($this->_exclusions as $exclusion) {
                    
                    if($exclusion["division_id"] != $team->DivisionId) {
                        $ddata[] = new Division($team->TeamId, $team->Team, $team->DivisionId, $team->DivisionName, $team->DivisionCategory, $team->MatchType);
                    }
                }
            }
            else {
                $ddata[] = new Division($team->TeamId, $team->Team, $team->DivisionId, $team->DivisionName, $team->DivisionCategory, $team->MatchType);
            }
        }
        
        return $ddata;
    }
    
    
    
    /**
     * Return the available divisions ranking.
     * @param string $divID
     * @return DivisionRanking[] | boolean
     */
    public function getDivisionRanking($divID)
    {
        $ResponseDivName = $this->getApi()->GetClubTeams( array("Credentials" => $this->credentials, "Club" => $this->getClubIndice(), "Season" => $this->getSeasonParam()) );
        foreach($ResponseDivName->TeamEntries as $team)
            
            if($team->DivisionId == $divID){
                $Response = $this->getApi()->GetDivisionRanking( array("Credentials" => $this->credentials, "DivisionId" => $divID) );
                $data = array();
                
                foreach ($Response->RankingEntries as $dRank) {     
                   
                    $data[] = new DivisionRanking($dRank->Position, $dRank->Team, $dRank->GamesPlayed, $dRank->GamesWon, $dRank->GamesLost, $dRank->GamesDraw, 
                                                  $dRank->IndividualMatchesWon, $dRank->IndividualMatchesLost, $dRank->IndividualSetsWon, $dRank->IndividualSetsLost, 
                                                  $dRank->Points, $dRank->TeamClub, $team->DivisionName
                              );
                    
                }
                
                return $data;
           }
        return false;
    }
    
    
    /**
     * Return the last found results for the provided division id.
     * @param string $divID
     */
    public function getDivisionResults($divID, $week)
    {
        $games = array();
        $Response = $this->getApi()->GetMatches( array(
            "Credentials" => $this->credentials, "WeekName" => $week, "DivisionId" => $divID, "ShowDivisionName" => "yes"
        ) );
            
        foreach($Response->TeamMatchesEntries as $game){
            $score = (isset($game->Score)) ? $game->Score : "-";
               
            $games[] = new DivisionGameResult(
                $game->HomeTeam, $game->AwayTeam, $score, $game->IsHomeForfeited, 
                $game->IsAwayForfeited, $game->HomeClub, $game->AwayClub, $game->DivisionName
            );
            
        }
        
        return $games;
    }
    
}

?>