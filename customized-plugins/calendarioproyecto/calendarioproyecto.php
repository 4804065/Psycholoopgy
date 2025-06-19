<?php
/*
Plugin Name: PLUGIN DE CALENDARIO DE CITAS
Description: PLUGIN DE CALENDARIO DE CITAS
Version:     1.0
Author:      Psycholoopgy
Author URI:  
License:     GPL2
*/


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!defined('ABSPATH')) exit;

define('RAI_RUTA',plugin_dir_path(__FILE__));
require_once(RAI_RUTA . 'includes/psycho-citas.php' );
require_once(RAI_RUTA . 'public/publico-citas.php' );

function cdtk_admin_menu(){	
    add_menu_page('Citas', 'Citas', 'administrator', 'psychocalen', 'psycho_citas','dashicons-edit-page'); 
}

add_action('admin_menu', 'cdtk_admin_menu');

add_shortcode( 'calendario_publico', 'shcalendario_publico' );

?>

