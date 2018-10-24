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
 * Common TabT api calls used to fetch infos from afft website.
 *
 * All api calls made by this class are using the TabT api provided by G. Frenoy.
 * 
 * @category   TabT api calls
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @see        TabT-Api
 * @link       https://github.com/gfrenoy/TabT-API
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class TabTApiCommon 
{
    
    public $BASE_URL = "https://resultats.aftt.be";
    protected $credentials;
    private $_tabtApi;
    private $_wsdl_url = "https://resultats.aftt.be/api?WSDL";
        
    
    /**
     * Common TabTApiCommmon class constructor.
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->credentials = array('Account'  => $login, 'Password' => $password);
        $this->_tabtApi = null;
    }
    
    
    /**
     * Create and return the Soap client.
     * @return SoapClient
     */
    public function getApi()
    {
        if(null == $this->_tabtApi) {
            $this->_tabtApi = new SoapClient($this->_wsdl_url);
        }
        
        return $this->_tabtApi;
    }
    
    
    /**
     * Fetch current season and return it.
     * @return string 
     */
    public function getSeason()
    {
        $Response = $this->getApi()->GetSeasons(array("Credentials" => $this->credentials));
        return $Response->CurrentSeasonName;
    }
    
    
    /**
     * Fetch current season parameter for requests
     * @return string
     */
    public function getSeasonParam()
    {
        $Response = $this->getApi()->GetSeasons(array("Credentials" => $this->credentials));
        return $Response->CurrentSeason;
    }
    
}
