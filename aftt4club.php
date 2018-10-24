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

/*
Plugin Name:  Aftt4Club Plugin
Plugin URI:   https://github.com/Nux007/Wordpress-Aftt4Club
Description:  A wordpress plugin using TabT api that fetch tennis table clubs infos. 
Version:      0.0.1
Author:       Nux007
Author URI:   https://github.com/Nux007
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Domain Path:  /languages
*/


/**
 * Installation/Activation/deactivation/deletion processes
 * Import css and javascript...
 **/


function aftt4club_install() 
{
    flush_rewrite_rules();
}


function aftt4club_deactivation() 
{
    flush_rewrite_rules();
}


/**
 * Styles definition.
 */
function add_aftt4club_styles()
{
    $src_stl = plugin_dir_url( __FILE__ ).'css/shortcodes.css';
    $src_bjs = plugin_dir_url( __FILE__ ).'js/utils.js';
    
    $current_screen = get_current_screen();
    
    // JS files available fro all pages.
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker');
    wp_register_script("aftt4club-js-utils", $src_bjs);
    wp_enqueue_script( 'aftt4club-js-utils', $src_bjs, array( 'wp-color-picker','jquery' ), false, true);
    
    // Slugs related cascading style sheets.
    $available_on = array("aftt_liste_de_forces", "aftt_club_divisions_ranking", "aftt_club_members_challenge");
    
    foreach($available_on as $page)
        if ( strpos($current_screen->base, $page) !== false) {
            wp_register_style("aftt4club-style", $src_stl);
            wp_enqueue_style( 'aftt4club-style', $src_stl, array(), false, false);
        }
}


/**
 * Availables shortcodes.
 */
function shortcode_show_ldf() {
    include_once plugin_dir_path( __FILE__ )."./views/front/listeDeForcesFront.php";
    $ldf = new ListeDeForcesFront(get_option("aftt4club_index"));
    $ldf->print();
}


function shortcode_show_challenge() {
    wp_register_style("aftt4club-style", plugin_dir_url( __FILE__ ).'css/shortcodes.css');
    wp_enqueue_style( 'aftt4club-style', plugin_dir_url( __FILE__ ).'css/shortcodes.css', array(), false, false);
    include_once plugin_dir_path(__FILE__) . "./views/front/challengeFront.php";
    $challenge = new ClubMembersChallengeFront(get_option("aftt4club_index"), get_option("aftt4club_login"),
                                               get_option("aftt4club_password"), get_option("aftt4club_challenge_exclusions")
                                               );
    $challenge->print();
}


function shortcode_show_divisions_ranking_and_results()
{
    wp_register_style("aftt4club-style", plugin_dir_url( __FILE__ ).'css/shortcodes.css');
    wp_enqueue_style( 'aftt4club-style', plugin_dir_url( __FILE__ ).'css/shortcodes.css', array(), false, false);
    include_once plugin_dir_path(__FILE__) . "views/front/rankingFront.php";
    $ranking = new RankingFront(get_option("aftt4club_index"), get_option("aftt4club_divisions_exclusions"));
    $ranking->print();
}


// Shortcodes.
add_shortcode( 'Challenge', 'shortcode_show_challenge' );
add_shortcode( 'Divisions_classements_resultats', 'shortcode_show_divisions_ranking_and_results' );
add_shortcode( 'Liste_de_forces', 'shortcode_show_ldf' );

add_action('admin_enqueue_scripts', 'add_aftt4club_styles');

register_activation_hook( __FILE__, 'aftt4club_install' );
register_deactivation_hook( __FILE__, 'aftt4club_deactivation' );

// Languages.
function aftt4club_load_plugin_textdomain() {
    load_plugin_textdomain( 'aftt4club', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'aftt4club_load_plugin_textdomain' );

include_once plugin_dir_path( __FILE__ )."./Aftt4ClubConfig.php";
