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

include_once plugin_dir_path( __FILE__ )."./api/Aftt.php";
include_once plugin_dir_path( __FILE__ )."./api/AfttClubs.php";
include_once plugin_dir_path( __FILE__ )."./api/AfttMembers.php";
include_once plugin_dir_path( __FILE__ )."./api/AfttChallenge.php";

include_once plugin_dir_path( __FILE__ )."./views/common/listeDeForces.php";
include_once plugin_dir_path( __FILE__ )."./views/admin/listeDeForcesAdmin.php";
include_once plugin_dir_path( __FILE__ )."./views/admin/challenge.php";
require_once plugin_dir_path( __FILE__ )."./lib/pdf/fpdf.php";


/**
 * Plugin entry point.
 *
 * @author     Nux007 <007.nux@gmail.com>
 * @link       https://github.com/Nux007/Wordpress-Aftt4Club
 * @copyright  2018
 * @since      Class available since Release 0.0.1
 */
class Aftt4ClubConfig 
{
    
	/**
	 * Aftt4Configuration and admin pages constructor
	 */
	public function __construct()
	{
	    // Menu.
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'aftt4club_register_settings'));
	}
	
	
	/**
	 * Add admin menu and sub menus.
	 */
	public function add_admin_menu()
	{
		add_menu_page('Plugin Aftt4Club', 'AFTT', 'manage_options', 'aftt4club_config', array($this, 'menu_html'));
		add_submenu_page('aftt4club_config', 'Configuration', 'Configuration', 'manage_options', 'aftt4club_config', array($this, 'menu_html'));
		add_submenu_page('aftt4club_config', 'Liste de forces', 'Liste de forces', 'manage_options', 'aftt_liste_de_forces', array($this, 'menu_html_liste_de_forces'));
		add_submenu_page('aftt4club_config', 'Challenge', 'Challenge', 'manage_options', 'aftt_club_members_challenge', array($this, 'menu_html_members_challenge'));
	}
	
	
	/**
	 * Registering all needed settings.
	 */
	public function aftt4club_register_settings()
	{
	    // api credentials
	    register_setting('aftt4club_settings', 'aftt4club_login');
	    register_setting('aftt4club_settings', 'aftt4club_password');
	    
	    // Club index
	    register_setting('aftt4club_settings', 'aftt4club_index');
	    
	    // "Liste de forces" colors scheme.
	    register_setting('aftt4club_settings', 'aftt4club_ldf_header_color');
	    register_setting('aftt4club_settings', 'aftt4club_ldf_th_color');
	    register_setting('aftt4club_settings', 'aftt4club_ldf_borders_color');
	    register_setting('aftt4club_settings', 'aftt4club_ldf_nt_child_even_color');
	    register_setting('aftt4club_settings', 'aftt4club_ldf_nt_child_odd_color');
	}
	
	
	/**
	 * Global configuration menu.
	 */
	public function menu_html()
	{
        echo "<h1>".get_admin_page_title()."</h1>";
		?>
		
		N'utilisez AUCUNE de ces options si le plugin a déjà été configuré une fois, la récupération des informations
		se fait automatiquement tous les lundis pendant la nuit <br /><br />
		
		<div class="wrap">
            <form method="post" action="options.php">
                <?php settings_fields('aftt4club_settings') ?>
                
                <!-- Global configuration. -->
                <fieldset class="aftt4cub_config">
                  <legend>Configuration globale</legend>
                  
                  <!-- TabT api credentials -->
                  <label for="aftt4club_login">Login ( results.aftt.be ):</label>
                  <input id="aftt4club_login" name="aftt4club_login" type="text" value="<?php echo get_option("aftt4club_login"); ?>" />
                  <br />
                  <label for="aftt4club_password">Mot de passe ( results.aftt.be ):</label>
                  <input id="aftt4club_password" name="aftt4club_password" type="password" value="<?php echo get_option("aftt4club_password"); ?>" />
                  
                  
                  <!-- Club indices -->
                  <br />
                  <label for="aftt4club_index">Indice de votre Club:</label>
                  <select name="aftt4club_index" id="aftt4club_index">
                  <?php 
                      // Getting all clubs indices.
                      $aftt = new AfttBasicInfos('', '');                  
                      
                      foreach($aftt->getClubs() as $club) {
                          $selected = get_option("aftt4club_index") == $club->getIndex() ? "selected='selected'" : "";
                          echo '<option value="' . $club->getIndex() . '" ' . $selected . '>' . $club->getIndex() . ' - ' . $club->getName() . '</option>';   
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
                    <input id="aftt4club_ldf_header_color" name="aftt4club_ldf_header_color" type="text" class="color-picker-field" value="<?php echo $header_color; ?>" />
                    
                    <label for="aftt4club_ldf_th_color">Couleur des titre des colones: </label>
                    <input id="aftt4club_ldf_th_color" name="aftt4club_ldf_th_color" type="text" class="color-picker-field" value="<?php echo $th_color; ?>" />
                    
                    <label for="aftt4club_ldf_borders_color">Couleur des bordures: </label>
                    <input id="aftt4club_ldf_borders_color" name="aftt4club_ldf_borders_color" type="text" class="color-picker-field" value="<?php echo $borders_color; ?>" />
                    
                    <label for="aftt4club_ldf_nt_child_even_color">Couleur de fond des lignes paires: </label>
                    <input id="aftt4club_ldf_nt_child_even_color" name="aftt4club_ldf_nt_child_even_color" type="text" class="color-picker-field" value="<?php echo $nt_child_even_color; ?>" />
                    
                    <label for="aftt4club_ldf_nt_child_odd_color">Couleur de fond des lignes imapres: </label>
                    <input id="aftt4club_ldf_nt_child_odd_color" name="aftt4club_ldf_nt_child_odd_color" type="text" class="color-picker-field" value="<?php echo $nt_child_odd_color; ?>" />
                    
                </fieldset>
                
                <!-- Form submit button. -->
                <?php submit_button(); ?>
            </form>
        </div><?php
    }
    
    
    /**
     * Liste de forces menu.
     */
    public function menu_html_liste_de_forces()
    {
        echo "<h1>" . get_admin_page_title() . "</h1>";
        
        if(get_option("aftt4club_index") !== False) {
            $ldf = new ListeDeForcesAdmin(get_option("aftt4club_index"));
            
            $ldf->setColorsMap(
                  (get_option('aftt4club_ldf_header_color') !== false) ? get_option('aftt4club_ldf_header_color') : "#9cfff4", 
                  (get_option('aftt4club_ldf_th_color') !== false) ? get_option('aftt4club_ldf_th_color') : "#9cfff4", 
                  (get_option('aftt4club_ldf_borders_color') !== false) ? get_option('aftt4club_ldf_borders_color') : "#f0f0f0", 
                  (get_option('aftt4club_ldf_nt_child_even_color') !== false) ? get_option('aftt4club_ldf_nt_child_even_color') : "#f0f0f0", 
                  (get_option('aftt4club_ldf_nt_child_odd_color') !== false) ? get_option('aftt4club_ldf_nt_child_odd_color') : "#FFFFFF"
            );
            
            echo $ldf->print();
            
        } else {
            echo "Veuillez entrer l'indice de votre club avant de consulter votre liste de forces.";
        }
    }
    
    
    
    /**
     * Club challenge menu.
     */
    public function menu_html_members_challenge()
    {
        echo "<h1>" . get_admin_page_title() . "</h1>";
        
        ?>
        <span>Le challenge est généré automatiquement, il est préférable d'attendre mardi milieu d'après midi depuis la mise en place de validation des résultats côté site AFTT !</span>
        <?php
        $challenge = new ClubChallenge();
	    echo $challenge->printHTML();
        
    }
    
    
}

new Aftt4ClubConfig();


