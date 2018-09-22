<?php


/**
 * class defined for url jobs.
 * @author Nux007
 *
 */
class AfttUrlHandler {
    
    // Fetch data from provided url and pattern.
    final public static function __fetchData($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    
    // Proceed a preg_match all with provided PCRE regex.
    final public static function __preg_match_all($pattern, $source){
        $result = preg_match_all($pattern, $source, $matches, PREG_PATTERN_ORDER);
        unset($matches[0]);
        return array("success" => ($result !== false) && count($matches) > 0 ? True : False, "data" => $matches);
    }
    
}



/**
 * Perform basic AFTT scraper things like getting pager, ...
 * @author Nux007
 *
 */
class AfttBase {
    
    protected $base_url = "https://resultats.aftt.be";
    
    // Get pager from pages. Must be any url end point, no search pages.
    public function getPagerLinks($url) {
        // Getting pager location.
        $source = $this->get($url, 'id="pager"(.+?)\/tr');
        
        // Gettig pager links.
        $pattern_pagers= '.+?(\/index\.php\?.+?)[\\"\']';
        $result = AfttUrlHandler::__preg_match_all("#". $pattern_pagers. "#iums", $source["data"][1][0]);
        
        if($result["success"] == false)
            return false;

        // Getting first pager element.
        $result_fe = AfttUrlHandler::__preg_match_all("#span.+?table.+?>(.+?)<\/span#iums", $source["data"][1][0]);
        if($result_fe["success"] !== false){
            $fel = str_replace("&nbsp;", "", $result_fe["data"][1][0]);
            $fel = substr($result["data"][1][0], 0, strrpos($result["data"][1][0], "=") + 1) . $fel;
            $result["data"][1][] = $fel;
        }
        return array_unique($result["data"][1]);
    }
    
    
    // Get any page from AFTT website.
    public function get($url, $pattern){
        
        $url = $this->base_url . $url;
        $output = AfttUrlHandler::__fetchData($url);
        
        if(stripos("please try again later", $output) > 0)
            throw new Exception("Site web AFTT indisponible");
        
        $result = AfttUrlHandler::__preg_match_all("#". $pattern . "#iums", $output);
        return $result;
    }
    
}



/**
 * Scrape informations about TT clubs from AFTT website : Global scraper.
 * @author Nux007
 *
 */
class AfttScraper extends AfttBase {
	
	protected $url_clubs_indices = "/clubs";
	protected $url_clubs_indices_pager = "/index.php?menu=1&cur_page="; 
	
	// Get the current available club indices from the aftt website.
	public function getClubsIndices(){
		// Getting pages count.	
	    $pager = $this->getPagerLinks($this->url_clubs_indices);
		
		if(count($pager) <= 0)
		    return false;
		
		foreach($pager as $page){
		    $data = $this->get($page, 'selectable(?: selected)?"?\sid="?(.+?)"?>.+?field="Indice">(.+?)<\/td>.+?Modifier.+?">(.+?)<.+?gorie">(.+?)<\/');
		    if($data["success"] == false) 
		        continue;
		   
		    for($i = 0 ; $i < count($data["data"][1]) - 1; $i++){
		        $mixed = $data["data"];
		        $array_data[] = array("index" => $mixed[1][$i], "indice" => $mixed[2][$i], "display_name" => $mixed[3][$i], "region" => $mixed[4][$i]);
		    }
		}
		
		return (null !== $array_data && count($array_data)) > 0 ? $array_data : false;
	}
	
	
	// Get all club members from giben club index.
	public function getClubMembers($club_index){
	    
	    $pager = $this->getPagerLinks("/club/" . $club_index . "/members");
	    $pattern_members = 'selectable(?: selected)?"?\sid="?(.+?)"?>.+?Index">(.+?)<\/td.+?affiliation">(.+?)<\/.+?Nom">(.+?)<\/td.+?nom">(.+?)<\/td.+?Cl.">(.+?)<\/td';
	    
	    foreach($pager as $member_page){
	        $output = $this->get($member_page, $pattern_members);
	        
	        for($i = 0 ; $i < count($output["data"][1]) ; $i++){
	            $member = $output["data"];
	            $array_data[] = array("index_bd" => $member[1][$i], "index" => $member[2][$i], "numero_affiliation" => $member[3][$i], 
	                                  "nom" => $member[4][$i], "prenom" => $member[5][$i] , "classement" => $member[6][$i]);
	            
	        }
	    }
	    
	    return (null !== $array_data && count($array_data)) > 0 ? $array_data : false;
	}

	
	// Get club basic infos.
	public function getClubNameAndIndice($club_index){
	    $url = "/club/" . $club_index;
	    $pattern_name = 'interclubs_title">.+?style=.+?>(.+?)<a.+?affiliation.+?col2">(.+?)<\/td';
	    $output = $this->get($url, $pattern_name);
	    
	    return (null !== $output && count($output["data"])) > 0 ? array("name" => $output["data"][1][0], "indice" => $output["data"][2][0]) : false;
	}
	
	
	// Return the Att version of current "Liste de Forces" for provided club index.
	public function getAfttLdfLink($club_index){
	    $ldf_url = $this->base_url . "/index.php?menu=6&club_id=" . $club_index . "&pdf=1&compute_pdf=1";
	    return $ldf_url;
	}
	
	
	// Return the current TT season.
	public function getCurrentSeason(){
	    $pattern_season = 'actuelle.+?field="c2">(.+?)<';
	    $output = $this->get("", $pattern_season);
	    
	    return (null !== $output && count($output["data"])) > 0 ? $output["data"][1][0] : false;
	}
	
	
	// Return all available categories.
	public function getAvailableMembersCategories(){
	    
	}
	
}
