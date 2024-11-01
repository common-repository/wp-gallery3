<?php
	/*
	Plugin Name: WP Gallery3
	Plugin URI: http://www.josh-burkard.ch
	Description: a plugin to show Gallery3 Plugins with User Permissions
	Version: 0.9.3
	Author: Josh Burkard
	Author URI: http://www.josh-burkard.ch
	License: GPL2
	*/
	
	// Add actions
	add_action ( 'wp_login',                'WPGallery3_login_success' );
	add_action ( 'admin_menu',              'WPGallery3_admin_menu');
	add_action ( 'profile_update',          'WPGallery3_profile_update' );
	add_action ( 'personal_options_update', 'WPGallery3_save_user_profile' );
	add_action ( 'user_register',           'WPGallery3_user_register' );
	

	// Add filters
	add_filter ( 'the_content', 'WPGallery3_page_content'); 
	
//
//
//
function tcustom_addbuttons() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;

	if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "add_tcustom_tinymce_plugin");
		add_filter('mce_buttons', 'register_tcustom_button');
	}
}
function register_tcustom_button($buttons) {
	array_push($buttons, "|", "wpgallery3");
	return $buttons;
} 
function add_tcustom_tinymce_plugin($plugin_array) {
	$plugin_array['wpgallery3'] = WP_PLUGIN_URL.'/wp-gallery3/wp-gallery3_editor.js';
	return $plugin_array;
}
// init process for button control
add_action('init', 'tcustom_addbuttons');
//
//
//	
	function WPGallery3_user_register( $user_id )
	{
		// When a new user ist registered
		if ( get_option ( 'WPGallery3_autocreateuser' ) == 1 )
		{
			// if option AutoCreateUser is checked
			$user_info = get_userdata( $user_id );
			$password = '';
			
			WPGallery3_update_galuser( $user_info->user_login , $user_info->display_name , $password , $user_info->user_email );
		}
	}
	
	function prefix_add_my_stylesheet() 
	{
		// Load the styles.css to the plugin
        wp_register_style( 'prefix-style', plugins_url( 'style.css' , __FILE__) );
        wp_enqueue_style( 'prefix-style' );
    }

	function WPGallery3_login_success( $username )
	{
		// when a user logs in
		if ( get_option ( 'WPGallery3_autocreateuser' ) == 1 )
		{
			// if option AutoCreateUser is checked
			$user_info = get_userdatabylogin( $username );
			$pwd = $_POST['pwd'];
			WPGallery3_update_galuser( $user_info->user_login , $user_info->display_name , $pwd , $user_info->user_email );
		}
	}
	
	function WPGallery3_page_content( $content )
	{
		// Replace [wpgallery3] tag inside the page/post content with the Gallery3 code
		$posStart = stripos ( $content , '[WPGallery3');
        if (($posStart > 0))
		{
			$posEnd = stripos ( $content , ']', $posStart);
			// echo 'Start: ' . $posStart . '<br>End: ' . $posEnd . '<br>';
			$tag = substr($content, $posStart, ($posEnd - $posStart + 1));
			$params = get_params($tag);
			// if tag [wpgallery3] is in content, execute wp-gallery3_content.php and replace the tag with the gallery3 code.
			Include ( 'wp-gallery3_content.php' );
			// $content = str_ireplace( "[WPGallery3]" , $WPGallery3Content, $content);
			$content = str_ireplace( $tag , $WPGallery3Content, $content);
		}
		return $content; 
	} 
	
	function get_params($tag)
	{
		$ret[albumid] = 0;
		$ret[rootalbumid] = 1;
		
		$tag = trim($tag, "[");
		$tag = trim($tag, "]");
		$array1 = preg_split("/ /", $tag);
		for ($i = 0; $i < sizeof ($array1); $i++)
		{
			$pos = stripos($array1[$i], '=');
			if ($pos !== false)
			{
				if ($array1[$i] == '=')
				{
					// Before and after = ist a space between parameter and value
					$ret[ strtolower ( $array1[ ( $i - 1 ) ] ) ] = $array1[ ( $i + 1 ) ];
				} elseif ($pos == 0) {
					// Between parameter name and = is a space
					$ret[ strtolower( $array1[ ( $i - 1 ) ] ) ] = substr($array1[$i], 1);
				} elseif ($pos == (strlen($array1[$i]) - 1) ) {
					// beetween Value and = is a space
					
					$ret[ ( strtolower( substr($array1[$i], 0, -1) ) ) ] = $array1[ ($i + 1) ] ;
				} else {
					// there are no spaces between parameter name and value
					$array2 = preg_split('/=/', $array1[$i]);
					$ret[ strtolower($array2[0]) ] = $array2[1];
				}
			}
		}
		return $ret;
	}
	
	function WPGallery3_profile_update ( $user_id )
	{
		// when a user profile is updated
		if ( get_option ( 'WPGallery3_autocreateuser' ) == 1 )
		{
			// if option AutoCreateUser is checked
			$user_info = get_userdata( $user_id );
			$password = '';
			if ( $_POST['pwd'] )
			{
				// if password is transmitted by logon form
				$password = $_POST['pwd'];
			}
			if (($_POST['pass1']) AND ($password == ''))
			{
				// if password is transmitted by edit user form
				$password = $_POST['pass1'];
			}
			
			WPGallery3_update_galuser( $user_info->user_login , $user_info->display_name , $password , $user_info->user_email );
		}
	}
	
	function WPGallery3_update_galuser( $username , $displayname , $password , $email )
	{
		//
		// Writes User-Datas to the Gallery3 database
		//
		
		// Read Gallery3 database settings
		$WPGallery3_dbhost         = get_option ( 'WPGallery3_dbhost' );
		$WPGallery3_dbname         = get_option ( 'WPGallery3_dbname' );  
		$WPGallery3_dbuser         = get_option ( 'WPGallery3_dbuser' );  
		$WPGallery3_dbpwd          = get_option ( 'WPGallery3_dbpwd' );  
		$WPGallery3_tblprefix      = get_option ( 'WPGallery3_tblprefix' ); 
		
		// Open Gallery3 database as a new mySQL connection
		// the last true means that there is a new mySQL connection instead of the existing mySQL connection (which is for WordPress)
		$WPGallery3con = mysql_connect( $WPGallery3_dbhost , $WPGallery3_dbuser , $WPGallery3_dbpwd , true ); 
		if ( !$WPGallery3con )
		{
			die( 'Could not connect: ' . mysql_error( $WPGallery3con ) );
		}
		mysql_select_db( $WPGallery3_dbname, $WPGallery3con );
		
		// Check if user already exist in Gallery3 database
		$sqlGal = 'SELECT * FROM ' . $WPGallery3_tblprefix . 'users where name = "' . $username . '"';
		$rows = mysql_query( $sqlGal , $WPGallery3con );
		$num_rows = mysql_num_rows ( $rows );
		if ($num_rows == 0 )
		{
			// if user doesn't exist, create it
			$sqlInsert  = 'INSERT INTO ' . $WPGallery3_tblprefix . 'users (name, full_name, ';
			if ($password != '')
			{
				$sqlInsert .= 'password, ';
			}
			$sqlInsert .= 'email, admin, guest, locale) VALUES ';
			$sqlInsert .= '("' . $username . '", "' . $displayname . '", ';
			if ($password != '')
			{
				$sqlInsert .= 'MD5("' . $password . '"), ';
			}
			$sqlInsert .= '"' . $email . '", 0, 0, "en_US")';
			mysql_query ( $sqlInsert, $WPGallery3con );
			$GalUserID = mysql_insert_id ( $WPGallery3con );
			
			// Add group membership EveryOne (1) and Registered Users (2) to the newly created user
			$sqlInsert = 'INSERT INTO ' . $WPGallery3_tblprefix . 'groups_users (`group_id`, `user_id`) VALUES (1, ' . $GalUserID . '), (2, ' . $GalUserID . ');';
			mysql_query( $sqlInsert , $WPGallery3con );
		} else {
			// Update the user with the new datas
			$row = mysql_fetch_assoc($rows);
			$sqlUpdate  = 'UPDATE ' . $WPGallery3_tblprefix . 'users SET full_name = "' . $displayname . '", email = "' . $email . '"';
			if ($password != '')
			{
				$sqlUpdate .= ', password = MD5("' . $password . '")';
			}
			$sqlUpdate .= ' WHERE id = ' . $row['id'] . ';';
			mysql_query( $sqlUpdate , $WPGallery3con );
		}
		// close connection to Gallery3-database
		mysql_close( $WPGallery3con );
	}
	
	function WPGallery3_admin_menu()
	{
		//
		// Admin menu pages
		//
		
		// Load the wp-gallery3.css to the plugin
		wp_enqueue_style( 'wpgallery3', (plugins_url('',__FILE__) . '/wp-gallery3.css') );
		
		$capability = 'administrator';
		$menu_slug = 'WPGallery3';
		$picture = plugins_url('',__FILE__) . '/wp-gallery3.png';
		
		// Add admin menu page
		add_menu_page( 'WP Gallery3', 'WP Gallery3', $capability, $menu_slug, 'WPGallery3_page_overview', $picture ); 
		
		// Add admin menu sub-pages
		$parent_slug = $menu_slug;
		add_submenu_page( $parent_slug, 'Settings', 'Settings', $capability, 'WPGallery3_settings', 'WPGallery3_page_settings' ); 
		// add_submenu_page( $parent_slug, 'User Migration', 'User Migration', $capability, 'WPGallery3_usermigration', 'WPGallery3_page_usermigration' ); 
		add_submenu_page( $parent_slug, 'About', 'About', $capability, 'WPGallery3_about', 'WPGallery3_page_about' ); 
	}
	
	function WPGallery3_page_overview()
	{
		// add admin overview page
		if ( !current_user_can( 'manage_options' ) )
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		Include ('wp-gallery3_overview.php');
	}
	
	function WPGallery3_page_settings()
	{
		// add admin settings page
		if ( !current_user_can( 'manage_options' ) )
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		Include ('wp-gallery3_admin.php');
	}
	
	function WPGallery3_page_about()
	{
		// add admin about page
		if ( !current_user_can( 'manage_options' ) )
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		Include ('wp-gallery3_about.php');
	}
	
	/*function WPGallery3_page_usermigration()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		Include ('wp-gallery3_usermigration.php');
	}
	*/
	
	function getAlbumNames( $albumID, $rootID )
	{
		// read Album names from Gallery3 database
		global $WPGallery3_tblprefix, $WPGallery3con;
		
		$sqlGal = 'Select * FROM ' . $WPGallery3_tblprefix . 'items WHERE (id = ' . $albumID. ');' ;
		$rows = mysql_query($sqlGal, $WPGallery3con);
		$row = mysql_fetch_assoc($rows);
		$ret = '<a href="' . get_page_link() . '&albumid=' . $albumID . '">';
		if ($albumID != $rootID)
		{
			$ret .= $row['title'] ;
		} else {
			$ret .= 'Gallery';
		}
		$ret .= '</a>';
		if ($row['parent_id'] != $rootID)
		{
			// if album has a parent, append it to the name of the parent album
			$ret = getAlbumNames($row['parent_id'], $rootID) . ' / ' . $ret;
		} else {
			$ret = '<a href="' . get_page_link() . '&albumid=' . $rootID . '">Gallery</a> / ' . $ret;
		}
		return $ret;
	}
	
	/**
	* Returns current plugin version.
	* 
	* @return string Plugin version
	*/
	function plugin_get_version()
	{
		if ( ! function_exists( 'get_plugins' ) )
		{
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
		$plugin_file = basename( ( __FILE__ ) );
		return $plugin_folder[$plugin_file]['Version'];
	}
?>
