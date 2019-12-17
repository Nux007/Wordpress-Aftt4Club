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

include_once plugin_dir_path( __FILE__ )."./lib/helpers/Members.php";
include_once plugin_dir_path( __FILE__ )."./lib/helpers/Divisions.php";
include_once plugin_dir_path( __FILE__ )."./lib/helpers/Utils.php";
require_once plugin_dir_path( __FILE__ )."./lib/pdf/fpdf.php";

include_once plugin_dir_path( __FILE__ )."./api/Aftt.php";
include_once plugin_dir_path( __FILE__ )."./api/AfttClubs.php";
include_once plugin_dir_path( __FILE__ )."./api/AfttChallenge.php";
include_once plugin_dir_path( __FILE__ )."./api/AfttDivisions.php";

include_once plugin_dir_path( __FILE__ )."./views/common/listeDeForces.php";
include_once plugin_dir_path( __FILE__ )."./views/common/challenge.php";
include_once plugin_dir_path( __FILE__ )."./views/common/ranking.php";

include_once plugin_dir_path( __FILE__ )."./views/admin/listeDeForcesAdmin.php";
include_once plugin_dir_path( __FILE__ )."./views/admin/rankingAdmin.php";
include_once plugin_dir_path( __FILE__ )."./views/admin/challengeAdmin.php";


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
    private $_colors;
    
	/**
	 * Aftt4Configuration and admin pages constructor
	 */
	public function __construct()
	{
	    // Menu.
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'aftt4club_register_settings'));
        
        // Colors.
        $this->_colors = new ColorMap();
        $this->_colors->setColorsMap(
            (get_option('aftt4club_ldf_header_color') !== false) ? get_option('aftt4club_ldf_header_color') : "#9cfff4",
            (get_option('aftt4club_ldf_th_color') !== false) ? get_option('aftt4club_ldf_th_color') : "#9cfff4",
            (get_option('aftt4club_ldf_borders_color') !== false) ? get_option('aftt4club_ldf_borders_color') : "#f0f0f0",
            (get_option('aftt4club_ldf_nt_child_even_color') !== false) ? get_option('aftt4club_ldf_nt_child_even_color') : "#f0f0f0",
            (get_option('aftt4club_ldf_nt_child_odd_color') !== false) ? get_option('aftt4club_ldf_nt_child_odd_color') : "#FFFFFF"
            );
	}
	
	
	/**
	 * Add admin menu and sub menus.
	 */
	public function add_admin_menu()
	{
		add_menu_page('Plugin Aftt4Club', 'AFTT', 'edit_posts', 'aftt4club_config', array($this, 'menu_html'));
		add_submenu_page('aftt4club_config', 'Configuration', 'Configuration', 'edit_posts', 'aftt4club_config', array($this, 'menu_html'));
		add_submenu_page('aftt4club_config', 'Liste de forces', 'Liste de forces', 'edit_posts', 'aftt_liste_de_forces', array($this, 'menu_html_liste_de_forces'));
		add_submenu_page('aftt4club_config', 'Classements et résultats', 'Classements et résultats', 'edit_posts', 'aftt_club_divisions_ranking', array($this, 'menu_html_divisions_ranking'));
		add_submenu_page('aftt4club_config', 'Challenge', 'Challenge', 'edit_posts', 'aftt_club_members_challenge', array($this, 'menu_html_members_challenge'));
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
	    
	    // Challenge exclusions.
	    register_setting('aftt4club_settings', 'aftt4club_challenge_exclusions', array());
	    register_setting('aftt4club_settings', 'aftt4club_divisions_exclusions', array());
	}
	
	
	/**
	 * Global configuration menu.
	 */
	public function menu_html()
	{
	    
	?>
        <h1>Aftt4Club - <?php echo get_admin_page_title(); ?></h1>
		
		<div class="wrap">
		
            <form method="post" action="options.php">
              <div>
                <h2><?php _e("Club selection", "aftt4club") ?></h2><hr>
              </div>
                <?php settings_fields('aftt4club_settings') ?>
                
                <!-- Global configuration. -->
                <table>
                    <!-- Login -->
                    <tr>
                      <td><label for="aftt4club_login"><?php _e("Login (http://results.aftt.be)", "aftt4club") ?><span> *</span>: </label></td>
                      <td><input name="aftt4club_login" id="aftt4club_login" value="<?php echo get_option("aftt4club_login"); ?>" type="text"></td>
                    </tr>
                
                    <!-- Password -->
                    <tr>
                      <td><label for="aftt4club_password"><?php _e("Password (http://results.aftt.be)", "aftt4club") ?><span> *</span>: </label></td>
                      <td><input name="aftt4club_password" id="aftt4club_login" value="<?php echo get_option("aftt4club_password"); ?>" type="password"></td>
                    </tr>
                    
                    <tr>
                      <td><label for="aftt4club_index"><?php _e("Club index", "aftt4club") ?><span> *</span>: </label></td>
                      <td><select name="aftt4club_index" id="aftt4club_index">
                          <?php 
                          // Getting all clubs indices.
                          $aftt = new AfttBasicInfos('', '');                  
                              
                          foreach($aftt->getClubs() as $club) {
                              $selected = get_option("aftt4club_index") == $club->getIndex() ? "selected='selected'" : "";
                              echo '<option value="' . $club->getIndex() . '" ' . $selected . '>' . $club->getIndex() . ' - ' . $club->getName() . '</option>';   
                          }
                          ?>
                      </select></td>
                    </tr>  
                </table>     
                
                
              <!-- Documents colors -->
              <div class="container">
                  <h2><?php _e("Documents colors configuration", "aftt4club") ?></h2><hr>
              </div>      
                <!-- Liste de forces layout -->
                <table>
                    <?php 
                    // Default values.
                    $header_color = (get_option('aftt4club_ldf_header_color') !== false) ? get_option('aftt4club_ldf_header_color') : "#9cfff4";
                    $th_color = (get_option('aftt4club_ldf_th_color') !== false) ? get_option('aftt4club_ldf_th_color') : "#9cfff4";
                    $borders_color = (get_option('aftt4club_ldf_borders_color') !== false) ? get_option('aftt4club_ldf_borders_color') : "#f0f0f0";
                    $nt_child_even_color = (get_option('aftt4club_ldf_nt_child_even_color') !== false) ? get_option('aftt4club_ldf_nt_child_even_color') : "#f0f0f0";
                    $nt_child_odd_color = (get_option('aftt4club_ldf_nt_child_odd_color') !== false) ? get_option('aftt4club_ldf_nt_child_odd_color') : "#FFFFFF";
                    ?>
                    <tr>
                        <td><label for="aftt4club_ldf_header_color"><?php _e("Documents title color", "aftt4club") ?></label></td>
                        <td><input id="aftt4club_ldf_header_color" name="aftt4club_ldf_header_color" type="text" class="color-picker-field" value="<?php echo $header_color; ?>" /></td>
                    </tr>
                    
                    <tr>
                        <td><label for="aftt4club_ldf_th_color"><?php _e("Columns title color", "aftt4club") ?></label></td>
                        <td><input id="aftt4club_ldf_th_color" name="aftt4club_ldf_th_color" type="text" class="color-picker-field" value="<?php echo $th_color; ?>" /></td>
                    </tr>
                    
                    <tr>
                        <td><label for="aftt4club_ldf_borders_color"><?php _e("Borders color", "aftt4club") ?></label></td>
                        <td><input id="aftt4club_ldf_borders_color" name="aftt4club_ldf_borders_color" type="text" class="color-picker-field" value="<?php echo $borders_color; ?>" /></td>
                    </tr>
                    
                    
                    <tr>
                        <td><label for="aftt4club_ldf_nt_child_even_color"><?php _e("Odd lines background color", "aftt4club") ?></label></td>
                        <td><input id="aftt4club_ldf_nt_child_even_color" name="aftt4club_ldf_nt_child_even_color" type="text" class="color-picker-field" value="<?php echo $nt_child_even_color; ?>" /></td>
                    </tr>
                    
                    <tr>
                        <td><label for="aftt4club_ldf_nt_child_odd_color"><?php _e("Even lines background color", "aftt4club") ?></label></td>
                        <td><input id="aftt4club_ldf_nt_child_odd_color" name="aftt4club_ldf_nt_child_odd_color" type="text" class="color-picker-field" value="<?php echo $nt_child_odd_color; ?>" /></td>
                    </tr>
                </table>
                
                <!-- Form submit button. -->
              <?php 
              
              //<!-- Form submit button. -->
              submit_button(); 
              
              ?>
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
            $ldf->setColorsMap($this->_colors->getColorsMap());
            echo $ldf->print();
            
        } 
        else {
            _e("Fill in your club index before accessing your LDF", "aftt4club");
        }
    }
    
    
    /**
     * Divisions ranking menu.
     */
    public function menu_html_divisions_ranking()
    {
        echo "<h1>" . get_admin_page_title() . "</h1>";
        
        if(get_option("aftt4club_index") !== False) {
            $ranking = new RankingAdmin(get_option("aftt4club_index"), get_option("aftt4club_divisions_exclusions"));
            $ranking->colorsMap["th"] = "#ffffff";
            $ranking->setColorsMap($this->_colors->getColorsMap());
            $ranking->printExclusionsForm(get_option("aftt4club_divisions_exclusions"));
	        echo $ranking->print();
        }
        else {
            _e("Fill in your club index before accessing your divisions rankings", "aftt4club");
        }
        
    }
     
    
    /**
     * Club members challenge menu.
     */
    public function menu_html_members_challenge()
    {
        echo "<h1>" . get_admin_page_title() . "</h1>";

        if(get_option("aftt4club_index") !== False) {
            $challenge = new ClubMembersChallengeAdmin(get_option("aftt4club_index"), get_option("aftt4club_login"), 
                                                       get_option("aftt4club_password"), get_option("aftt4club_challenge_exclusions")
                                                      );
            echo $challenge->printExclusionsForm(get_option("aftt4club_challenge_exclusions"));
	        echo $challenge->print();
        }
	    else {
	        _e("Fill in your club index before accessing your club members challenge", "aftt4club");
	    }
        
    }
    
    
}


new Aftt4ClubConfig();


