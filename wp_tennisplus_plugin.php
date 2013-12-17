<?php
/*
Plugin Name: Tennisplus plugin
Plugin URI: http://www.theloz.net/tennisplusplgin
Description: Tennisplus administration and management
Version: 1.2
Author: Lorenzo Lombardi
Author URI: http://www.theloz.net
License: GPL2
*/
/*
Copyright 2013  Lorenzo Lombardi (email:info@theloz.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('WP_tplus_plugin')){
	class WP_tplus_plugin{
		/**
		 * Construct the plugin object
		 */
		public function __construct() {
        	// Initialize Settings
            //require_once(sprintf("%s/settings.php", dirname(__FILE__)));
            //$WP_tplus_plugin_Settings = new WP_tplus_plugin_Settings();
        	
        	// Register custom post types
            //require_once(sprintf("%s/post-types/post_type_template.php", dirname(__FILE__)));
            //$Post_Type_Template = new Post_Type_Template();
			
                        require_once(sprintf("%s/custom_tables/tables.class.php", dirname(__FILE__)));
                        require_once(sprintf("%s/custom_tables/tournaments_listtable.class.php", dirname(__FILE__)));
                        require_once(sprintf("%s/custom_tables/matches_listtable.class.php", dirname(__FILE__)));
                        require_once(sprintf("%s/custom_tables/places_listtable.class.php", dirname(__FILE__)));
                        require_once(sprintf("%s/custom_tables/points_listtable.class.php", dirname(__FILE__)));
		
                        require_once(sprintf("%s/admin/admin.class.php", dirname(__FILE__)));
                        require_once(sprintf("%s/shortcodes/shortcodes.class.php", dirname(__FILE__)));
                        
                        require_once(sprintf("%s/utils/province.php", dirname(__FILE__)));
			
		} // END public function __construct
	    
		/**
		 * Activate the plugin
		 */
		public static function activate() {
			$tb = new WP_tplus_tables;
			$tb->table_install();
			//force plugin update
			$tb->tplus_update_db_check();
			//add_action('plugins_loaded', 'tplus_update_db_check');
		} // END public static function activate
	
		/**
		 * Deactivate the plugin
		 */		
		public static function deactivate() {
			$tb = new WP_tplus_tables;
			$tb->table_uninstall();
		} // END public static function deactivate
                
                function pw_load_scripts($hook) {
                        //die($hook);
                        if( $hook == 'tennis-plus_page_tplus_tournamentsedit' ) 
                                wp_enqueue_script( 'custom-js', plugins_url( 'tennisplus_plugin/js/tplus.js' , dirname(__FILE__) ) );
                        if( $hook == 'admin_page_tplus_placesedit' ) 
                                wp_enqueue_script( 'custom-js', plugins_url( 'tennisplus_plugin/js/places.js' , dirname(__FILE__) ) );
                        
                }
                
	} // END class WP_tplus_plugin
} // END if(!class_exists('WP_tplus_plugin'))

if(class_exists('WP_tplus_plugin')){+
        
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_tplus_plugin', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_tplus_plugin', 'deactivate'));
	
	// instantiate the plugin class
	$wp_plugin_template = new WP_tplus_plugin();
        //insert the admin menu
	add_action('admin_menu', array('Admin_tplus','get_menu'));
        
        //loads additional files
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script( 'tp_datatables', plugins_url( 'tennisplus_plugin/js/jquery.dataTables.min.js' , dirname(__FILE__) ) );
        wp_enqueue_script( 'tp_frontend_scripts', plugins_url( 'tennisplus_plugin/js/tplus_fe.js' , dirname(__FILE__) ) );
        
        wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('tplus_ui_css', plugins_url( 'tennisplus_plugin/css/tplus.css'), dirname(__FILE__) );
        wp_enqueue_style('tp_datatables', plugins_url( 'tennisplus_plugin/css/jquery.dataTables.css'), dirname(__FILE__) );
        wp_enqueue_style('tp_datatables_themeroller', plugins_url( 'tennisplus_plugin/css/jquery.dataTables_themeroller.css'), dirname(__FILE__) );
        add_action('admin_enqueue_scripts', array('WP_tplus_plugin','pw_load_scripts'));
        
        //add shortcodes
        add_shortcode( 'tplus_matches', array('Shortcode_tplus', 'tplus_matches_shortcode_function' ) ); 
        add_shortcode( 'tplus_places', array('Shortcode_tplus', 'tplus_places_shortcode_function' ) ); 
        add_shortcode( 'tplus_tournaments', array('Shortcode_tplus', 'tplus_tournaments_shortcode_function' ) ); 

}
