<?php
include_once "../data/Aftt.php";

class Aftt4ClubConfigTest {
	
	// Global configuration menu.
	public function print_menu_html(){
		        
        // Getting all clubs indices.
        $scraper = new AfttScraper();
        $clubs = $scraper->getClubsIndices();
        
		?>
        <form method="post" action="options.php">
            <label>Indice de votre Club:</label>
            <select name="aftt4club_club" id="aftt4club_club">
                <?php 
                foreach($clubs as $club)
                    echo '<option value="' . $club["index"] . '">' . $club["indice"] . '</option>';    
                ?>
            </select>
        </form><?php 
    }
	
}

$config = new Aftt4ClubConfigTest();
$config->print_menu_html();
?>