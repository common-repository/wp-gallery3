<?php
	/*
	This file create the Gallery3 code for replace the [wpgallery3] tag in content
	*/

global $current_user;
global $WPGallery3_tblprefix;
global $WPGallery3con;


if ( $WPGallery3Content == '' )
{
	// if $WPGallery3Content isn't empty, it was already created and isn't needed to recreate
	
	// load the styles.css to the plugin
	wp_register_style( 'prefix-style' , plugins_url('style.css', __FILE__) );
	wp_enqueue_style( 'prefix-style' );
	
	

	// get albumid, if none is set albumid is 1 by default
	$albumid = 1;
	
	// echo 'Params: ' . $params;
	if ( $_GET['albumid'] ) {
		$albumid = $_GET['albumid'];		
	} elseif ( $params[albumid] ) {
		$albumid = $params[albumid];
	}
	
	// get options from WordPress
	$WPGallery3_BaseUrl        = get_option ( 'WPGallery3_baseurl' );
	$WPGallery3_Columns        = get_option ( 'WPGallery3_Columns' );
	$WPGallery3_Rows           = get_option ( 'WPGallery3_Rows' );
	$WPGallery3_dbhost         = get_option ( 'WPGallery3_dbhost' );
	$WPGallery3_dbname         = get_option ( 'WPGallery3_dbname' );  
	$WPGallery3_dbuser         = get_option ( 'WPGallery3_dbuser' );  
	$WPGallery3_dbpwd          = get_option ( 'WPGallery3_dbpwd' );  
	$WPGallery3_tblprefix      = get_option ( 'WPGallery3_tblprefix' );  
	$WPGallery3_showpictitle   = get_option ( 'WPGallery3_showpictitle' );
	$WPGallery3_showalbumtitle = get_option ( 'WPGallery3_showalbumtitle' );
	if ( $params[rows] )
	{
		$WPGallery3_Rows = $params[rows];
	}
	if ( $params[columns] )
	{
		$WPGallery3_Columns = $params[columns];
	}
	
	
	$WPGallery3Content    = '';
	// Open Gallery3 database as a new mySQL connection
	// the last true means that there is a new mySQL connection instead of the existing mySQL connection (which is for WordPress)
	$WPGallery3con = mysql_connect( $WPGallery3_dbhost , $WPGallery3_dbuser , $WPGallery3_dbpwd , true );
	if (!$WPGallery3con)
	{
		die('Could not connect: ' . mysql_error( $WPGallery3con ));
	}
	mysql_select_db( $WPGallery3_dbname , $WPGallery3con );
	
		// Guest-login
		$GalUserID = 1;
		if( !is_user_logged_in() )
		{
			// no user logged in
		} else {
			// User is loged in
			
			get_currentuserinfo();
			// Get GalleryUserID
			$sqlGal = 'SELECT * FROM ' . $WPGallery3_tblprefix . 'users WHERE name = "' . $current_user->user_login . '" LIMIT 1;';
			$rows = mysql_query( $sqlGal , $WPGallery3con );
			if ( Count( $rows ) == 0 )
			{
				// user doesn't exist in Gallery3
				// Guest-login
				$GalUserID = 1;
			} else {
				// user exist
				$row = mysql_fetch_assoc( $rows );
				$GalUserID = $row['id'];
			}
		}

		// Read Group-Memberships and generate QueryString for Permission-Query
		$sqlGalMembership = 'SELECT * FROM ' . $WPGallery3_tblprefix . 'groups_users WHERE ( user_id = ' . $GalUserID . ' )';
		$rows = mysql_query( $sqlGalMembership , $WPGallery3con );
		$MembershipQuery = '(';
		while ($row = mysql_fetch_assoc($rows))
		{
			if ($MembershipQuery != '(')
			{
				$MembershipQuery .= ') OR (';
			}
			$MembershipQuery .= '(tblAccess.view_' . $row['group_id'] . ' = 1) OR (tblAccess.view_' . $row['group_id'] . ' IS NULL)' ;
		}
		$MembershipQuery .= ')';
		
		// Check if user has access
		$sqlGal = 'Select * FROM ' . $WPGallery3_tblprefix . 'access_intents WHERE ((ID = ' . $albumid. ') AND ' . $MembershipQuery . ');' ;
		$rows = mysql_query( $sqlGal , $WPGallery3con );
		if ( Count( $rows ) == 0 )
		{
			// User has no access
		} else {
			// User has access
			
			$sqlGal = 'SELECT * FROM ' . $WPGallery3_tblprefix . 'items WHERE (id = ' . $albumid . ')';
			$rows = mysql_query( $sqlGal , $WPGallery3con );
			$row = mysql_fetch_assoc($rows);
			// Get Album Names and Parent names
			if ($albumid != $params[rootalbumid])
			{
				$albumNames = $row['title'];
				// if ($row['parent_id'] != 0) 
				if ($row['parent_id'] != $params[rootalbumid])
				{
					$albumNames = getAlbumNames( $row['parent_id'], $params[rootalbumid] ) . ' / ' . $albumNames;
				} else {
					$albumNames = '<a href="' . get_page_link() . '&albumid=' . $row['parent_id'] . '">Gallery</a> / ' . $albumNames;
				}
			} else {
				$albumNames = 'Gallery';
			}
			$WPGallery3Content .= $albumNames;
			
			// Get Items
			$sqlGal  = 'SELECT * FROM (SELECT ID AS MainID, parent_id, sort_column, sort_order FROM ' . $WPGallery3_tblprefix . 'items WHERE (parent_id = ' . $albumid . ')) tblMain ';
			$sqlGal .= 'INNER JOIN (SELECT * FROM ' . $WPGallery3_tblprefix . 'access_intents ) tblAccess ON (tblMain.MainID = tblAccess.item_id) ';
			$sqlGal .= 'Where ' . $MembershipQuery . ' ';
			
			$rows = mysql_query( $sqlGal , $WPGallery3con );

			// Get Page Navigation
			$pageid = 1;
			if ( $_GET['pageid'] )
			{
				$pageid = $_GET['pageid'];
			}
			
			$numrows = mysql_num_rows( $rows );
			$sort = 'ORDER BY ' . $row['sort_column'] . ' ' . $row['sort_order'];
			if ( ( $row['sort_column'] != 'title' ) AND ( $row['sort_column'] != 'id' ) )
			{
				$sortfields = ', ' . $row['sort_column'];
			} else {
				$sortfields = '';
			}
			$lastpageid = intval ( $numrows / ( $WPGallery3_Columns * $WPGallery3_Rows ) ) + 1;
			if ( $lastpageid > 1 )
			{
				$PageNavigations  = '<table><tr class = "wpgal"><td style="text-align: left;">';
				if ( $pageid > 1 )
				{
					$PageNavigations .= '<a href="' . get_page_link() . '&albumid=' . $albumid . '&pageid=1">First</a>&nbsp;';
					$PageNavigations .= '<a href="' . get_page_link() . '&albumid=' . $albumid . '&pageid=' . ( $pageid - 1 ) . '">Back</a>&nbsp;';
				}
				$PageNavigations .= '</td><td style="text-align: right;">';
				if ( $pageid < $lastpageid )
				{
					$PageNavigations .= '<a href="' . get_page_link() . '&albumid=' . $albumid . '&pageid=' . ( $pageid + 1 ) . '">Next</a>&nbsp;';
					$PageNavigations .= '<a href="' . get_page_link() . '&albumid=' . $albumid . '&pageid=' . $lastpageid . '">Last</a>&nbsp;';
				}
				$PageNavigations .= '</td></tr></table>';
				$WPGallery3Content .= $PageNavigations;
			}
			
			// List Items
			$startid = 0;
			if ( $_GET['pageid'] )
			{
				$startid = $WPGallery3_Columns * $WPGallery3_Rows * ( $pageid - 1 );
			}
			$sqlGal  = 'SELECT * FROM (SELECT ID AS MainID, title as MainTitle' . $sortfields . ', type as MainType, relative_path_cache as MainFile, album_cover_item_id FROM ' . $WPGallery3_tblprefix . 'items WHERE (parent_id = ' . $albumid . ')) tblMain ';
			$sqlGal .= 'INNER JOIN (SELECT * FROM ' . $WPGallery3_tblprefix . 'access_intents ) tblAccess ON (tblMain.MainID = tblAccess.item_id) ';
			$sqlGal .= 'Where ' . $MembershipQuery . ' ';
			$sqlGal .= $sort . ' ';
			$sqlGal .= 'LIMIT ' . $startid . ', ' . $WPGallery3_Columns * $WPGallery3_Rows . ';';
			
			$WPGallery3Content .= '<table>';
			$i = 0;
			$rows = mysql_query( $sqlGal , $WPGallery3con );
			while ( $row = mysql_fetch_assoc( $rows ) )
			{
				$i++;
				if ( $i == 1 )
				{
					$WPGallery3Content .= '<tr class = "wpgal">';
				}
				if ( $row['MainType'] == 'photo' )
				{
					$WPGallery3Content .= '<td class = "wpgallery3photooff" onmouseover="className=\'wpgallery3photoon\'" onmouseout="className=\'wpgallery3photooff\'">';
					$WPGallery3Content .= '<a rel="lightbox" href="' . plugin_dir_url( __FILE__ ) . 'wpg-allery3_picture.php?type=default&picid=' . $row['MainID'] .'&.jpg">';
					$WPGallery3Content .= '<img src="' . plugin_dir_url( __FILE__ ) . 'wp-gallery3_picture.php?picid=' . $row['MainID'] . '&type=thumb">';
					if ( $WPGallery3_showpictitle == 1 )
					{
						$WPGallery3Content .= '<br/>' . $row['MainTitle'];
					}
				} elseif ( $row['MainType'] == 'album' ) {
					$WPGallery3Content .= '<td class="wpgal3albumoff" onmouseover="className=\'wpgal3albumon\'" onmouseout="className=\'wpgal3albumoff\'" width="' . intval( 100 / $WPGallery3_Columns ) . '%">';
					$WPGallery3Content .= '<a href="' . get_page_link() . '&albumid=' . $row['MainID'] . '">';
					$WPGallery3Content .= '<img src="' . plugin_dir_url( __FILE__ ) . 'wp-gallery3_picture.php?picid=' . $row['album_cover_item_id'] . '&type=thumb">';
					if ($WPGallery3_showalbumtitle == 1)
					{
						$WPGallery3Content .= '<br/>';
						$WPGallery3Content .= $row['MainTitle'];
					}
				}
				$WPGallery3Content .= '</a>';
				$WPGallery3Content .= '</td>';
				if ( $i == $WPGallery3_Columns )
				{
					$WPGallery3Content .= '</tr>';
					$i = 0;
				}
			}
			if ( $i == 1 )
			{
				$WPGallery3Content .= '<td>&nbsp;</td>';
			}
			if ( $i == 2 )
			{
				$WPGallery3Content .= '<td>&nbsp;</td>';
				$WPGallery3Content .= '<td>&nbsp;</td>';
			}
			$WPGallery3Content .= '</table>';
		}
	// close connection to Gallery3-database
	mysql_close($WPGallery3con);
}
?>