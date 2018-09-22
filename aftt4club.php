<?php

/*
Plugin Name:  Aftt4Club Plugin
Plugin URI:   https://github.com/Nux007/Wordpress-Aftt4Club
Description:  A scraper wordpress plugin that fetch tennis table clubs infos from aftt website. 
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

function aftt4club_setup_post_types() {
    register_post_type( 'liste_de_force', ['public' => 'true'] );
}


function aftt4club_install() {
    aftt4club_setup_post_types();
    flush_rewrite_rules();
}


function aftt4club_deactivation() {
    unregister_post_type( 'liste_de_force' );
    flush_rewrite_rules();
}


function add_aftt4club_styles(){
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


add_action( 'init', 'aftt4club_setup_post_types' );
add_action('admin_enqueue_scripts', 'add_aftt4club_styles');

register_activation_hook( __FILE__, 'aftt4club_install' );
register_deactivation_hook( __FILE__, 'aftt4club_deactivation' );

include_once plugin_dir_path( __FILE__ )."./Aftt4ClubConfig.php";
