<?php

/**
 * Common api calls and data.
 */
class TabTApiCommon {
    protected $credentials;
    private $tabtApi;
    private $wsdl_url = "https://resultats.aftt.be/api?WSDL";
    
    // Common TabTApiCommmon class constructor.
    public function __construct($login, $password){
        $this->credentials = array('Account'  => $login, 'Password' => $password);
        $this->tabtApi = null;
    }
    
    
    // Return Soap client if not null, new soap client otherwise.
    public function getApi(){
        if(null !== $this->tabtApi)
            return $this->tabtApi;
        return new SoapClient($this->wsdl_url);
    }
    
    
    // Return the current Season.
    public function getSeason(){
        $Response = $this->getApi()->GetSeasons(array("Credentials" => $this->credentials));
        return $Response->CurrentSeasonName;
    }
}
