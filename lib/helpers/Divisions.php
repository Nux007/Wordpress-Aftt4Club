<?php

/**
 * Club division helper.
 *
 * @category   Divisions helper.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class Division
{
    private $_team_id;
    private $_team;
    private $_division_id;
    private $_division_name;
    private $_division_category;
    private $_match_type;
    
    
    /**
     * Divisions helper construtor.
     * 
     * @param string $team_id
     * @param string $team
     * @param string $division_id
     * @param string $division_name
     * @param string $division_category
     * @param string $match_type
     */
    public function __construct($team_id, $team, $division_id, $division_name, $division_category, $match_type)
    {
        $this->_team_id = $team_id;
        $this->_team = $team;
        $this->_match_type = $match_type;
        $this->_division_name = $division_name;
        $this->_division_id = $division_id;
        $this->_division_category = $division_category;
    }
    
    
    /**
     * Return the team id;
     * @return string
     */
    public function getTeamId()
    {
        return $this->_team_id;
    }
    
    
    /**
     * Return the team
     * @return string
     */
    public function getTeam()
    {
        return $this->_team;
    }
    
    
    /**
     * Retrn the division ID.
     * @return string
     */
    public function getDivisionId()
    {
        return $this->_division_id;
    }
    
    
    /**
     * Return the division name.
     * @return string
     */
    public function getDivisionName()
    {
        return $this->_division_name;
    }
    
    
    /**
     * Return the division category.
     * @return string
     */
    public function getDivisionCategory()
    {
        return $this->_division_category;
    }
    
    
    /**
     * Return the match type.
     * @return string
     */
    public function getMatchType()
    {
        return $this->_match_type;
    }
    
    
}



/**
 * Club division ranking helper.
 *
 * @category   Divisions ranking helper.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class DivisionRanking
{
    private $_division_name;
    private $_position;
    private $_team;
    private $_gamesPlayed;
    private $_gamesWon;
    private $_gamesLost;
    private $_gamesDraw;
    private $_individualMatchesWon;
    private $_individualMatchesLost;
    private $_individualSetsWon;
    private $_individualSetsLost;
    private $_points;
    private $_teamClub;
    
    
    /**
     * DivisionRnking constructor.
     * 
     * @param string $position
     * @param string $team
     * @param string $gamesPlayed
     * @param string $gamesWon
     * @param string $gamesLost
     * @param string $gamesDraw
     * @param string $individualMatchesWon
     * @param string $individualMatchesLost
     * @param string $individualSetsWon
     * @param string $individualSetsLost
     * @param string $points
     * @param string $teamClub
     */
    public function __construct($position, $team, $gamesPlayed, $gamesWon, $gamesLost, $gamesDraw, $individualMatchesWon, 
                                $individualMatchesLost, $individualSetsWon, $individualSetsLost, $points, $teamClub, $divisionName)
    {
        $this->_gamesDraw = $gamesDraw;
        $this->_gamesLost = $gamesLost;
        $this->_gamesPlayed = $gamesPlayed;
        $this->_gamesWon = $gamesWon;
        $this->_individualMatchesLost = $individualMatchesLost;
        $this->_individualMatchesWon = $individualMatchesWon;
        $this->_individualSetsLost = $individualSetsLost;
        $this->_individualSetsWon = $individualSetsWon;
        $this->_points = $points;
        $this->_position = $position;
        $this->_team = $team;
        $this->_teamClub = $teamClub;
        $this->_division_name = $divisionName;
    }
    
    
    /**
     * Return "Match nul".
     * @return string
     */
    public function getGamesDraw()
    {
        return $this->_gamesDraw;
    }
    
    
    /**
     * Return lost games
     * @return string
     */
    public function getGamesLost()
    {
        return $this->_gamesLost;
    }
    
    
    /**
     * Return played games.
     * @return string
     */
    public function getGamesPlayed()
    {
        return $this->_gamesPlayed;
    }
    
    
    /**
     * Return games lost.
     * @return string
     */
    public function getGamesWon()
    {
        return $this->_gamesWon;
    }
    
    
    /**
     * Return individual lost matches.
     * @return string
     */
    public function getIndividualMatchesLost()
    {
        return $this->_individualMatchesLost;    
    }
    
    
    /**
     * Return individual won matches.
     * @return string
     */
    public function getIvidualMatchesWon()
    {
        return $this->_individualMatchesWon;
    }
    
    
    /**
     * Return individual set lost.
     * @return string
     */
    public function getIvidualSetsLost()
    {
        return $this->_individualSetsLost;
    }
    
    
    /**
     * Return individul sets won.
     * @return string
     */
    public function getIndividualSetsWon()
    {
        return $this->_individualSetsWon;
    }
    
    
    /**
     * Return current ranking points.
     * @return string
     */
    public function getPoints()
    {
        return $this->_points;
    }
    
    
    /**
     * Return current ranking  position.
     * @return string
     */
    public function getPosition()
    {
        return $this->_position;
    }
    
    
    /**
     * Return club Team name.
     * @return string
     */
    public function getTeam()
    {
        return $this->_team;
    }
    
    
    /**
     * Return the club owning the team.
     * @return string
     */
    public function getTeamClub()
    {
        return $this->_teamClub;
    }
    
    
    /**
     * Return the division name related to the ranking.
     * @return string
     */
    public function getDivisionName()
    {
        return $this->_division_name;
    }
    
}



/**
 * Club division game result helper.
 *
 * @category   Divisions game helper.
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class DivisionGameResult
{   
    private $_home;
    private $_away;
    private $_score;
    private $_home_forfeited;
    private $_away_forfeited;
    private $_home_club_indice;
    private $_away_club_indice;
    private $_game_division;
    
    
    /**
     * DivisionGameResult constructor.
     * @param string $homeTeam
     * @param string $awayTeam
     * @param string $score
     */
    public function __construct($homeTeam, $awayTeam, $score, $hforfeited, $aforfeited, $hindice, $aindice, $division_name)
    {
        $this->_home = $homeTeam;
        $this->_away = $awayTeam;
        $this->_score = $score;
        $this->_home_forfeited = $hforfeited;
        $this->_away_forfeited = $aforfeited;
        $this->_home_club_indice = $hindice;
        $this->_away_club_indice = $aindice;
        $this->_game_division = $division_name;
    }
    
    
    /**
     * Return the home team name.
     * @return string
     */
    public function getHomeTeam()
    {
        return $this->_home;
    }
    
    
    /**
     * Return the away team name.
     * @return string
     */
    public function getAwayTeam()
    {
        return $this->_away;
    }
    
    
    /**
     * Return the final game score.
     * @return string
     */
    public function getScore()
    {
        return $this->_score;
    }
    
    
    /**
     * Return true in case of home club forfeited.
     * @return boolean
     */
    public function isHomeForfeited()
    {
        return ((int)$this->_home_forfeited == 1) ? true : false;
    }
    
    
    /**
     * Return true in case of away club forfeited.
     * @return boolean 
     */
    public function isAwayForfeited()
    {
        return ((int)$this->_away_forfeited == 1) ? true : false;
    }
    
    
    /**
     * Return the home club indice.
     * @return string
     */
    public function getHomeClubIndice()
    {
        return $this->_home_club_indice;
    }
    
    
    /**
     * Return the away club indice.
     * @return string
     */
    public function getAwayClubIndice()
    {
        return $this->_away_club_indice;
    }
    
    
    /**
     * Return the game division name.
     * @return string
     */
    public function getDivisionName()
    {
        return $this->_game_division;
    }
}

?>