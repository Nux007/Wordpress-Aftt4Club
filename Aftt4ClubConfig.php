<?php

include_once plugin_dir_path( __FILE__ )."./data/Aftt.php";
include_once plugin_dir_path( __FILE__ )."./data/listeDeForces.php";
require_once plugin_dir_path( __FILE__ )."./lib/pdf/fpdf.php";


class Aftt4ClubConfig {
    
	/**
	 * Configuration and admin pages
	 */
	 
	public function __construct(){
	    // Menu.
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'aftt4club_register_settings'));
	}
	
	public function add_admin_menu(){
		add_menu_page('Plugin Aftt4Club', 'AFTT', 'manage_options', 'aftt4club_config', array($this, 'menu_html'));
		add_submenu_page('aftt4club_config', 'Configuration', 'Configuration', 'manage_options', 'aftt4club_config', array($this, 'menu_html'));
		add_submenu_page('aftt4club_config', 'Liste de forces', 'Liste de forces', 'manage_options', 'aftt_liste_de_forces', array($this, 'menu_html_liste_de_forces'));
	}
	
	
	public function aftt4club_register_settings(){
	    register_setting('aftt4club_settings', 'aftt4club_index');
	    // "Liste de forces"
	    register_setting('aftt4club_settings', 'aftt4club_ldf_header_color');
	    register_setting('aftt4club_settings', 'aftt4club_ldf_th_color');
	    
	    register_setting('aftt4club_settings', 'aftt4club_ldf_borders_color');
	    
	    register_setting('aftt4club_settings', 'aftt4club_ldf_nt_child_even_color');
	    register_setting('aftt4club_settings', 'aftt4club_ldf_nt_child_odd_color');
	}
	
	
	// Global configuration menu.
	public function menu_html(){
        echo "<h1>".get_admin_page_title()."</h1>";
        
        // Getting all clubs indices.
        $scraper = new AfttScraper();
        $clubs = $scraper->getClubsIndices();
        
		?>
		
		N'utilisez AUCUNE de ces options si le plugin a déjà été configuré une fois, la récupération des informations
		se fait automatiquement tous les lundis pendant la nuit <br /><br />
		<div class="wrap">
            <form method="post" action="options.php">
                <?php settings_fields('aftt4club_settings') ?>
                
                <!-- Global configuration. -->
                <fieldset class="aftt4cub_config">
                  <legend>Configuration globale</legend>
                  
                  <label for="aftt4club_index">Indice de votre Club:</label>
                  <select name="aftt4club_index" id="aftt4club_index">
                    <?php 
                    foreach($clubs as $club){
                        $selected = get_option("aftt4club_index") == $club["index"] ? "selected='selected'" : "";
                        echo '<option value="' . $club["index"] . '" ' . $selected . '>' . $club["indice"] . ' - ' . $club["display_name"] . '</option>';   
                    }
                    ?>
                  </select>
                </fieldset>
                
                
                <!-- Liste de forces layout -->
                <fieldset class="aftt4cub_config" id="ldf_config">
                    <?php 
                    // Default values.
                    $header_color = (get_option('aftt4club_ldf_header_color') !== false) ? get_option('aftt4club_ldf_header_color') : "#9cfff4";
                    $th_color = (get_option('aftt4club_ldf_th_color') !== false) ? get_option('aftt4club_ldf_th_color') : "#9cfff4";
                    $borders_color = (get_option('aftt4club_ldf_borders_color') !== false) ? get_option('aftt4club_ldf_borders_color') : "#f0f0f0";
                    $nt_child_even_color = (get_option('aftt4club_ldf_nt_child_even_color') !== false) ? get_option('aftt4club_ldf_nt_child_even_color') : "#f0f0f0";
                    $nt_child_odd_color = (get_option('aftt4club_ldf_nt_child_odd_color') !== false) ? get_option('aftt4club_ldf_nt_child_odd_color') : "#FFFFFF";
                    ?>
                    <legend>Layout de la liste de forces</legend>
                    
                    <label for="aftt4club_ldf_header_color">Couleur du titre du document: </label>
                    <input id="aftt4club_ldf_header_color" name="aftt4club_ldf_header_color" type="text" class="color-picker-field" value="<?php echo $header_color; ?>"></input>
                    
                    <label for="aftt4club_ldf_th_color">Couleur des titre des colones: </label>
                    <input id="aftt4club_ldf_th_color" name="aftt4club_ldf_th_color" type="text" class="color-picker-field" value="<?php echo $th_color; ?>"></input>
                    
                    <label for="aftt4club_ldf_borders_color">Couleur des bordures: </label>
                    <input id="aftt4club_ldf_borders_color" name="aftt4club_ldf_borders_color" type="text" class="color-picker-field" value="<?php echo $borders_color; ?>"></input>
                    
                    <label for="aftt4club_ldf_nt_child_even_color">Couleur de fond des lignes paires: </label>
                    <input id="aftt4club_ldf_nt_child_even_color" name="aftt4club_ldf_nt_child_even_color" type="text" class="color-picker-field" value="<?php echo $nt_child_even_color; ?>"></input>
                    
                    <label for="aftt4club_ldf_nt_child_odd_color">Couleur de fond des lignes imapres: </label>
                    <input id="aftt4club_ldf_nt_child_odd_color" name="aftt4club_ldf_nt_child_odd_color" type="text" class="color-picker-field" value="<?php echo $nt_child_odd_color; ?>"></input>
                    
                </fieldset>
                
                <!-- Form submit button. -->
                <?php submit_button(); ?>
            </form>
        </div><?php
    }
    
    
    
    // Handle aftt club "Liste de forces" display and generation.
    public function menu_html_liste_de_forces(){
        echo "<h1>" . get_admin_page_title() . "</h1>";
        
        if(get_option("aftt4club_index") !== False) {
            $ldf = new ListeDeForces(get_option("aftt4club_index"));
            $ldf->setColorsMap((get_option('aftt4club_ldf_header_color') !== false) ? get_option('aftt4club_ldf_header_color') : "#9cfff4", 
                              (get_option('aftt4club_ldf_th_color') !== false) ? get_option('aftt4club_ldf_th_color') : "#9cfff4", 
                              (get_option('aftt4club_ldf_borders_color') !== false) ? get_option('aftt4club_ldf_borders_color') : "#f0f0f0", 
                              (get_option('aftt4club_ldf_nt_child_even_color') !== false) ? get_option('aftt4club_ldf_nt_child_even_color') : "#f0f0f0", 
                              (get_option('aftt4club_ldf_nt_child_odd_color') !== false) ? get_option('aftt4club_ldf_nt_child_odd_color') : "#FFFFFF");
            echo $ldf->printHTML(true);
        }
        else 
            echo "Veuillez entrer l'indice de votre club avant de consulter votre liste de forces.";
    }
	
}

new Aftt4ClubConfig();


