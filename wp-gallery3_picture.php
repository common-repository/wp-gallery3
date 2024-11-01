<?php
	/*
		This file will display a single picture
	*/
	
	// Add WordPress functions, but dont display anything
	define('WP_USE_THEMES', false);
	require('../../../wp-blog-header.php');
	
	// Read WordPress Options
	$WPGallery3_dbhost    = get_option ('WPGallery3_dbhost');
	$WPGallery3_dbname    = get_option ('WPGallery3_dbname');  
	$WPGallery3_dbuser    = get_option ('WPGallery3_dbuser');  
	$WPGallery3_dbpwd     = get_option ('WPGallery3_dbpwd');  
	$WPGallery3_tblprefix = get_option ('WPGallery3_tblprefix');  
	$WPGallery3_picsize   = get_option ('WPGallery3_picsize');  
	$WPGallery3_thumbsize = get_option ('WPGallery3_thumbsize');  
	$WPGallery3_basedir   = get_option ('WPGallery3_basedir');
	
	// Get Picture-ID from QueryString
	$picID = $_GET['picid'];
	
	// Open Gallery3 database as a new mySQL connection
	// the last true means that there is a new mySQL connection instead of the existing mySQL connection (which is for WordPress)
	$WPGallery3con = mysql_connect( $WPGallery3_dbhost , $WPGallery3_dbuser , $WPGallery3_dbpwd , true );
	if ( !$WPGallery3con )
	{
		die( 'Could not connect: ' . mysql_error( $WPGallery3con ) );
	}
	mysql_select_db( $WPGallery3_dbname , $WPGallery3con );
		
		// Get User ID
		$GalUserID = 1;
		if( !is_user_logged_in() )
		{
			// no user logged in
		} else {
			// User is loged in
			
			get_currentuserinfo();
			$sqlGal = 'SELECT * FROM ' . $WPGallery3_tblprefix . 'users WHERE name = "' . $current_user->user_login . '" LIMIT 1;';
			$rows = mysql_query( $sqlGal , $WPGallery3con );
			
			if ( Count( $rows ) == 0 )
			{
				// user doesn't exist in Gallery3
				// Guest-login
				$GalUserID = 1;
			} else {
				// user exist
				$row = mysql_fetch_assoc($rows);
				$GalUserID = $row['id'];
			}
		}
		
		// Get users group memberships
		$sqlGalMembership = 'SELECT * FROM ' . $WPGallery3_tblprefix . 'groups_users WHERE (user_id = ' . $GalUserID . ')';
		$rows = mysql_query( $sqlGalMembership , $WPGallery3con );
		$MembershipQuery = '(';
		while ( $row = mysql_fetch_assoc( $rows ) )
		{
			if ( $MembershipQuery != '(' )
			{
				$MembershipQuery .= ') OR (';
			}
			$MembershipQuery .= '(tblAccess.view_' . $row['group_id'] . ' = 1) OR (tblAccess.view_' . $row['group_id'] . ' IS NULL)' ;
		}
		$MembershipQuery .= ')';
		
		// Check if user has access
		$sqlGal = 'Select * FROM ' . $WPGallery3_tblprefix . 'access_intents WHERE ((ID = ' . $picID. ') AND ' . $MembershipQuery . ');' ;
		$rows = mysql_query( $sqlGal , $WPGallery3con );
		if ( Count( $rows ) == 0 )
		{
			// User has no access
			echo 'User has no access';
		} else {
			// User has access
			
			switch ($_GET['type'])
			{
				case 'thumb':
					$filename = $WPGallery3_basedir.'var/thumbs'.getFileName( $picID );
					break;
				case 'default':
					$filename = $WPGallery3_basedir.'var/albums'.getFileName( $picID );
					break;
				case 'fullsize':
					$filename = $WPGallery3_basedir.'var/albums'.getFileName( $picID );
					break;
			}
			// set output header type
			header ( "Content-type: image/jpeg" );
			
			// load image
			$image = new SimpleImage();
			$image->load( $filename );
			
			// resize image dependent from the requested type
			if ( $_GET['type'] == 'thumb' )
			{
				if ( $image->getWidth() > $image->getHeight() )
				{
					$image->resizeToWidth( $WPGallery3_thumbsize );
				} else {
					$image->resizeToHeight( $WPGallery3_thumbsize );
				}
			}
	
			if ( $_GET['type'] == 'default' )
			{
				if ( $image->getWidth() > $image->getHeight() )
				{
					$image->resizeToWidth( $WPGallery3_picsize );
				} else {
					$image->resizeToHeight( $WPGallery3_picsize );
				}
			}
			// output the image
			$image->output();
		}
	
	// close connection to Gallery3-database
	mysql_close($WPGallery3con);

function getFileName($picid)
{
	// get the real file name from Gallery3 database
	global $WPGallery3_tblprefix;
	global $WPGallery3con;
	$sqlGal = 'Select * FROM ' . $WPGallery3_tblprefix . 'items WHERE (ID = ' . $picid. ');' ;
	$rows = mysql_query($sqlGal, $WPGallery3con);
	$row = mysql_fetch_assoc($rows);
	$ret = $row['name'];
	if ($row['parent_id'] != 0)
	{
		// if filename has a parent folder, append it to the parent folders name
		$ret = getFileName($row['parent_id']) . '/' . $ret;
	}
	return $ret;
}

class SimpleImage
{
	// Class for load and resize images
	var $image;
	var $image_type;
 
	function load( $filename )
	{
		$image_info = getimagesize( $filename );
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$this->image = imagecreatefromjpeg( $filename );
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif( $filename );
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng( $filename );
		}
	}
	
	function save($filename , $image_type=IMAGETYPE_JPEG , $compression=75 , $permissions=null )
	{
		if( $image_type == IMAGETYPE_JPEG )
		{
			imagejpeg( $this->image , $filename , $compression );
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif( $this->image , $filename );
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng( $this->image , $filename );
		}
		if( $permissions != null )
		{
			chmod( $filename , $permissions );
		}
	}
	
	function output( $image_type=IMAGETYPE_JPEG )
	{
		if( $image_type == IMAGETYPE_JPEG )  
		{
			imagejpeg( $this->image );
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif( $this->image );
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng( $this->image );
		}
	}
	
	function getWidth()
	{
		return imagesx( $this->image );
	}
	
	function getHeight() 
	{
		return imagesy( $this->image );
	}
	
	function resizeToHeight( $height )
	{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize( $width , $height );
	}
	
	function resizeToWidth( $width )
	{
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize( $width , $height );
	}
 
	function scale($scale)
	{
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize( $width , $height );
	}
	
	function resize( $width ,$height )
	{
		$new_image = imagecreatetruecolor( $width , $height );
		imagecopyresampled( $new_image , $this->image , 0 , 0 , 0 , 0 , $width , $height , $this->getWidth() , $this->getHeight() );
		$this->image = $new_image;
	}
}
?>