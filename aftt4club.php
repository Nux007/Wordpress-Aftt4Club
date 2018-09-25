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
    $src_ldf = plugin_dir_url( __FILE__ ).'css/admin/listedeforces_1.css';
    $src_cfg = plugin_dir_url( __FILE__ ).'css/admin/forms.css';
    $src_bjs = plugin_dir_url( __FILE__ ).'js/utils.js';
    
    $current_screen = get_current_screen();
    
    // JS files available fro all pages.
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker');
    wp_register_script("aftt4club-js-utils", $src_bjs);
    wp_enqueue_script( 'aftt4club-js-utils', $src_bjs, array( 'wp-color-picker','jquery' ), false, true);
    
    // Slugs related cascading style sheets.
    if ( strpos($current_screen->base, 'aftt_liste_de_forces') !== false) {
        wp_register_style("aftt4club-ldf", $src_ldf);
        wp_enqueue_style( 'aftt4club-ldf', $src_ldf, array(), false, false);
    }
    
    wp_register_style("aftt4club-cfg", $src_cfg);
    wp_enqueue_style( 'aftt4club-cfg', $src_cfg, array(), false, false);
}


/**
 * Availables shortcodes.
 */
function shortcode_show_ldf() {
    include_once plugin_dir_path( __FILE__ )."./views/front/listeDeForcesFront.php";
    $ldf = new ListeDeForcesFront("H207");
    $ldf->print();
}


// Shortcodes.
//add_shortcode( 'Liste_de_forces', 'shortcode_show_ldf' );
add_shortcode( 'Liste_de_forces', 'shortcode_show_ldf' );
add_action('admin_enqueue_scripts', 'add_aftt4club_styles');

register_activation_hook( __FILE__, 'aftt4club_install' );
register_deactivation_hook( __FILE__, 'aftt4club_deactivation' );

include_once plugin_dir_path( __FILE__ )."./Aftt4ClubConfig.php";
