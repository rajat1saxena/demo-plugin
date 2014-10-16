<?php
/**
 * Plugin Name: Title changer and Profanity filter
 * Plugin URI: http://raynstudios.in
 * Description: This plugin prepends text to the title of your posts and censor bad words
 * Version: 0.1
 * Author: Rajat Saxena
 * Author URI: http://raynstudios.in
 * License: GPL2
 */

// blocking direct access
defined('ABSPATH') or die("No playing around");

class titleChanger{
	static function change_title($title){
	return "[Changed]".$title;
	}

	static function profanity_filter($content){
		$bad_words_dictionary = array("slut","motherfucker","fucker","fuck");
		$content = str_ireplace($bad_words_dictionary, "**censored**", $content);
		return $content;
	}
}

// Prepends the text to title of post
add_filter('title_save_pre',array('titleChanger','change_title'));

// Removes bad words from post
add_filter('content_save_pre',array('titleChanger','profanity_filter'));

// Adding settings page for this plugin(this will add under Settings category)
add_action('admin_menu','title_changer_menu');

// Adding settings page under Tools
add_action('admin_menu','title_changer_tools_menu');

// This function will be responsible for actually creating the options page
function title_changer_menu(){
	add_options_page('Title changer and Profanity filter options',
		'Title changer/Profanity filter',
		'manage_options',
		'title_changr_profanity_filtr',
		'title_profanity_options'
		);
}

// This function will be responsible for actually creating the options page under Tools menu
function title_changer_tools_menu(){

	// Add options page under Tools menu
	add_submenu_page('tools.php',
		'Title changer & Profanity filter[from Tools]',
		'Title changer/Profanity filter',
		'manage_options',
		'title_changer_profanity_filtr',
		'title_profanity_options'
		);
}

// This function will build the HTML of Options page of our plugin 
function title_profanity_options(){
	if(!current_user_can('manage_options')){
		wp_die(__('Insufficient permissions'));
	}

	echo '<div class="wrap">';
	echo '<h2>Title changer/Profanity filter Options</h2>';
	echo '</div>';
}

// Creation of Db table for the plugin
global $tpf_version;
$tpf_version = '1.0';

class DBHelper{
  // Function to create table
  function install_table(){
    global $wpdb;
    $tablename = $wpdb->prefix."profanity_filter";
    
    $installed_version = get_option('tpf_version');
    if(($installed_version) != $tpf_version){
      $charset_collate = '';

      if ( ! empty( $wpdb->charset ) ) {
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
      }

      if ( ! empty( $wpdb->collate ) ) {
        $charset_collate .= " COLLATE {$wpdb->collate}";
      }
      
      $sql = "create table $tablename (
        id int(5) not null auto_increment,
        filterword varchar2(20) not null,
        replacewith varchar2(20) not null,
        unique key id (id)
      ) $charset_collate;";
      
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sql );
      
      update_option('tpf_version',$tpf_version);
    }
  }
  
  // Function to check if plugin version has been updated
  function check_update(){
    
  }
}

// Create table while installing the plugin
register_activation_hook( __FILE__, array('DBHelper','install_table'));

// Check for table's version update as the activation_hook is not run while updating the plugin
add_action('plugin_loaded',array('DBHelper','install_table'));
?>