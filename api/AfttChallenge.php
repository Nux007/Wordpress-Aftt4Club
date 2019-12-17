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
 * Create a view for club challenge computing each club member point.
 *
 * @category   Members challenge managment.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
Class ClubMembersChallenge extends TabTApiCommon
{
    private $_divisions;    
    private $_matches = null;
    private $_week;
    private $_classements = array(
                              "NC" => 1, "E6" => 2, "E4" => 3, "E2" => 4, "E0" => 5, "D6" => 6, "D4" => 7, "D2" => 8, "D0" => 9,
                              "C6" => 10, "C4" => 11, "C2" => 12, "C0" => 13, "B6" => 14, "B4" => 15, "B2" => 16, "B0" => 17
                            ); 
    private $_club;
    
    /**
     * Challenge constructor.
     * @param string $club_divisions
     * @param AfttClub $club
     * @param string $login
     * @param string $password
     * @param array $exclusions
     */
    public function __construct($club_divisions, $club, $login, $password, $exclusions = array())
    {   
        parent::__construct($login, $password);
        $this->_club = $club;
        $this->_week = 0;
        $this->_divisions = $club_divisions;
        $this->_matches = null;
        $this->_matches = $this->_getMatches();

	    if($exclusions === "null") {
		    $exclusions = null;
	    }

	    if(!is_null($exclusions)) {
		    foreach ( $exclusions as $exclusion ) {
			    //echo "Excluded: " . $exclusion["unique_id"] . " Week: " . $exclusion["week"] . " Padded week: ".str_pad($exclusion["week"], 2, '0', STR_PAD_LEFT)."<br />";

			    if ( null !== $this->_matches[ $exclusion["unique_id"] ][ str_pad( $exclusion["week"], 2, '0', STR_PAD_LEFT ) ] ) {
				    unset( $this->_matches[ $exclusion["unique_id"] ][ str_pad( $exclusion["week"], 2, '0', STR_PAD_LEFT ) ] );
			    }
			    /*
				foreach($this->_matches as $id => $match) {

					if(intval($id) === intval($exclusion["unique_id"])){

						foreach($this->_matches[$id] as $week => $week_data) {

							if(intval($week) === intval($exclusion["week"])) {
								$this->_matches[$id][$week]["won"] = null;
								$this->_matches[$id][$week]["lost"] = null;
							}
						}


					}
				}

				*/
		    }
	    }
    }
    
    
    
    /**
     * Return raw matches.
     * @return array|string
     */
    public function getMatches() 
    {
        return $this->_matches;
    }
    
    
    
    /**
     * Return available crankings used for points computing.
     * @return number[]
     */
    public function getAvailablesRankings()
    {
        return array_keys($this->_classements);
    }
    
    
    
    /**
     * Retuyrn the last played week.
     * @return string
     */
    public function getLastWeek()
    {
        return $this->_week;
    }
    
    
    
    /**
     * Return the challenge data to render.
     * @return array
     */
    public function getChallengeData()
    {
        return $this->_rendering();
    }
    
    
    /**
     * Return true if the given player for the given week has won all its games.
     * @return boolean
     */
    public function isUnbeatenGames($user_unique_id, $target_week) 
    {
    	$week = isset($this->_matches[$user_unique_id][$target_week]) ? $this->_matches[$user_unique_id][$target_week] : array();
        $won = isset($week["won"]) ? $week["won"] : array();
        return ( count( $won ) == 4 ) ? true : false;
    }
    
    
    /**
     * Return true if not set were lost.
     * @return boolean
     */
    public function isUnbeatenGamesSets($user_unique_id, $target_week)
    {
        if(!isset($this->_matches[$user_unique_id][$target_week]))
            return false;
        
        if(!isset($this->_matches[$user_unique_id][$target_week]) || !isset($this->_matches[$user_unique_id][$target_week]["won"]))
            return false;        
        
        elseif(isset($this->_matches[$user_unique_id][$target_week]["lost"]) &&
               count($this->_matches[$user_unique_id][$target_week]["lost"]) > 0 )
            return false;
        
        elseif(count($this->_matches[$user_unique_id][$target_week]["won"]) == 4 && 
               isset($this->_matches[$user_unique_id][$target_week]["lost_sets"]))
            return false;
        
        return true;        
    }
    
    
    /**
     * Collect all needed challenge data.
     * @return array.
     */
    private function _rendering() 
    {
        // For each player
        $challenge_data = array();
        
        foreach($this->getMatches() as $id => $player) {
            
            $member = $this->_club->getMember($id);
            
            $won = array();
            $lost = array();
            
            // For each played week.
            foreach($player as $id_week => $week) {
                
                if(intval($this->_week) < intval($id_week))
                    $this->_week = $id_week;
                
                if(isset($week["won"])) {
                    $won = array_merge($week["won"], $won);
                }
                if(isset($week["lost"])) {
                    $lost = array_merge($week["lost"], $lost);
                }
            }
            
            $player_data = array();
            $player_data["player"]       = strtoupper($member->getLastName()) . " " . ucfirst(strtolower($member->getFirstName()));
            $player_data["player_id"]    = $id;
            $player_data["ranking"]      = $member->getRanking();
            $player_data["total_won"]    = count($won);
            $player_data["total_lost"]   = count($lost);
            $player_data["total_played"] = count($won) + count($lost);
            $player_data["average"]      = (count($won) == 0) ? 0.00 : round(((float)$player_data["total_won"] / (float)$player_data["total_played"]) * 100, 2, PHP_ROUND_HALF_EVEN);
            $player_data["points"]       = $this->_computePoints($won, $lost, $member->getRanking());
            $player_data["victories"]    = array_count_values($won);
            $player_data["looses"]       = array_count_values($lost);
            
            $challenge_data[] = $player_data;
        }
        
        
        $points    = array_column($challenge_data, 'points');
        $victories = array_column($challenge_data, 'total_won');
        $looses    = array_column($challenge_data, 'total_lost');
        
        array_multisort($points, SORT_DESC, $victories, SORT_DESC, $looses, SORT_ASC, $challenge_data);
        
        return $challenge_data; 
    }
    
    
    
    /**
     * Search for matches and fetch raw data.
     * @return array|string
     */
    private function _getMatches(){
        
        $results = array();
        foreach($this->_divisions as $division){
            
            $Response = $this->getApi()->GetMatches(array("Credentials" => $this->credentials, "Club" => $this->_club->getIndex(), "DivisionId" => $division->getDivisionId(), "WithDetails" => "1" ));  
            
            foreach($Response->TeamMatchesEntries as $TeamMatchEntry) {
                
                $mDetails = $TeamMatchEntry->MatchDetails;

                $home = false;
                
                
                if($mDetails->DetailsCreated == "1"){
                
                    $home = ($TeamMatchEntry->HomeClub == $this->_club->getIndex()) ? true : false;
                    $forfeited = ( $TeamMatchEntry->IsHomeForfeited == "1" || $TeamMatchEntry->IsAwayForfeited =="1" ) ? true : false;
                    
                    $match_sheet = $mDetails->IndividualMatchResults;
                    
                    if($forfeited) {
                       // echo "Week: " . $TeamMatchEntry->WeekName ." Home Team: " . $TeamMatchEntry->HomeTeam . "<strong> Players state: </strong></br>";
                        
                        $home_forfeited = 0;
                        $away_forfeited = 0;
                        
                        foreach($match_sheet as $match) {
                            
                            if(isset($match->IsHomeForfeited)) {
                                $home_forfeited++;    
                            }
                            
                            if(isset($match->IsAwayForfeited)) {
                                $away_forfeited++;
                            }
                        }     
                        
                        if(! ($home_forfeited >= 9 || $away_forfeited >= 9) ) {
                             $forfeited = false;
                        }
                        
                    }
                    
                    if($home && !$forfeited) {
                        foreach($mDetails->AwayPlayers->Players as $player) {
                            if(isset($player->IsForfeited)) {
                                foreach($mDetails->HomePlayers->Players as $player_add) {
                                    $results[$player_add->UniqueIndex][$TeamMatchEntry->WeekName]["won"][] = "WO";
                                }
                            }
                        }
                    }
                    elseif(!$forfeited) {
                        foreach($mDetails->HomePlayers->Players as $player) {
                            if(isset($player->IsForfeited)) {
                                foreach($mDetails->AwayPlayers->Players as $player_add) {
                                    $results[$player_add->UniqueIndex][$TeamMatchEntry->WeekName]["won"][] = "WO";
                                }
                            }
                        }
                    }
                            
                                         
                    // Getting individuals matches results.
                    foreach($match_sheet as $match){
                        //print_r($match);
                        /* Playing at home */
                        if($home && !$forfeited) {    
                            // Getting ranking.
                            $away_ranking = "WO";
                            foreach($mDetails->AwayPlayers->Players as $player) {
                                // We never play versus a forfeited player
                                if($player->UniqueIndex == $match->AwayPlayerUniqueIndex)
                                    $away_ranking = $player->Ranking;
                            }
                            
                            // Won game.
                            if(isset($match->HomeSetCount) && $match->HomeSetCount == "3") {
                                $results[$match->HomePlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"][] = $away_ranking;
                                if(intval($match->AwaySetCount) > 0)
                                    $results[$match->HomePlayerUniqueIndex][$TeamMatchEntry->WeekName]["lost_sets"] = true;
                            }
                            elseif(isset($match->IsAwayForfeited)) {
                                $setted = isset($results[$match->HomePlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"]);
                                if(!$setted || array_search("WO", $results[$match->HomePlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"]) === false)
                                    $results[$match->HomePlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"][] = "WO";
                            }
                            // Lost game.
                            elseif(isset($match->HomeSetCount))
                                $results[$match->HomePlayerUniqueIndex][$TeamMatchEntry->WeekName]["lost"][] = $away_ranking;                            
                        }
                        
                        /* Playing away */
                        elseif(!$forfeited) {
                            // Getting ranking.
                            $home_ranking = "WO";
                            foreach($mDetails->HomePlayers->Players as $player) {
                                if($player->UniqueIndex == $match->HomePlayerUniqueIndex)
                                    $home_ranking = $player->Ranking;
                            }
                                    
                            // Won game.
                            if(isset($match->AwaySetCount) && $match->AwaySetCount == "3"){
                                $results[$match->AwayPlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"][] = $home_ranking;
                                if(intval($match->HomeSetCount) > 0)
                                    $results[$match->AwayPlayerUniqueIndex][$TeamMatchEntry->WeekName]["lost_sets"] = true;
                            }
                            elseif(isset($match->IsHomeForfeited)) {
                                $setted = isset($results[$match->AwayPlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"]);
                                if(!$setted || array_search("WO", $results[$match->AwayPlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"]) === false)
                                    $results[$match->AwayPlayerUniqueIndex][$TeamMatchEntry->WeekName]["won"][] = "WO";
                            }
                            // Lost game.
                            elseif(isset($match->AwaySetCount))
                                $results[$match->AwayPlayerUniqueIndex][$TeamMatchEntry->WeekName]["lost"][] = $home_ranking;
                            
                        }
                    }
                
                }
            
            }
        }
        return $results;
    }
    
    
    /**
     * Compute challenge points based on an array of lost and loose...
     * @param AfttMember
     * @return integer
     */
    private function _computePoints($data_win, $data_lost, $player_ranking)
    {
           
            // Computing total won points for the given member.
            $total_won_points = 0;
            $total_loose_points = 0;
            
            foreach($data_win as $won) {
                
                if($won == "WO" || $won == "W0") {
                    $total_won_points++;
                } else {
                    
                    if ($this->_classements[$won] > $this->_classements[$player_ranking]) {
                        $total_won_points += $this->_classements[$won] - $this->_classements[$player_ranking] + 1;
                    } else {
                        $total_won_points++;
                    }
                }
            }
            
            // Computing total loose points for he given member.
            foreach($data_lost as $lost) {
                
                if ($this->_classements[$lost] < $this->_classements[$player_ranking]) {
                    $total_loose_points += $this->_classements[$player_ranking] - $this->_classements[$lost];
                }
            }
            
            return $total_won_points - $total_loose_points;
    }
    
}

?>