<!DOCTYPE html>
<head>
	<title>Create WPGallery3 Shortcode</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript">
		var GalleryTiny = 
		{
			e: '',
			init: function(e) 
			{
				GalleryTiny.e = e;
				tinyMCEPopup.resizeToInnerSize();
			},
			insert: function createGalleryShortcode(e) 
			{
				//Create gallery Shortcode
				var albumid = $('#albumid').val();
				var rootalbumid = $('#rootalbumid').val();
				var columns = $('#columns').val();
				var rows = $('#rows').val();
				
				var output = '[WPGallery3';
				if(albumid) 
				{
					output += ' albumid=' + albumid;
				}
				if(rootalbumid) 
				{
					output += ' rootalbumid=' + rootalbumid;
				}
				if(columns) 
				{
					output += ' columns=' + columns;
				}
				if(rows) 
				{
					output += ' rows=' + rows;
				}
				
				output += ']';
		
				tinyMCEPopup.execCommand('mceReplaceContent', false, output);
				
				tinyMCEPopup.close();
				
			}
		}
		
		tinyMCEPopup.onInit.add(GalleryTiny.init, GalleryTiny);
	</script>
</head>
<body>
<?php
	/*
		This file will show the editor options
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

	$WPGallery3con = mysql_connect( $WPGallery3_dbhost , $WPGallery3_dbuser , $WPGallery3_dbpwd , true );
	if (!$WPGallery3con)
	{
		die('Could not connect: ' . mysql_error( $WPGallery3con ));
	}
	mysql_select_db( $WPGallery3_dbname , $WPGallery3con );
	
	$options =  getOptions( 0 , 0);
	
?>
<form id="GalleryShortcode">
	<TABLE>
		<TR>
			<TD>
				Root-Album:<?= $_POST['rootalbumid'] ?>
			</TD>
			<TD>
				<SELECT id="rootalbumid" onChange="document.forms['GalleryShortcode'].albumid.value = document.forms['GalleryShortcode'].rootalbumid.value;">
				<?php
					echo $options;
				?>
				</SELECT>
			</TD>
		</TR>
		<TR>
			<TD>
				Album:
			</TD>
			<TD>
				<SELECT id="albumid">
				<?php
					echo $options;
				?>
				</SELECT>
			</TD>
			<TD>
				The Album should be on the same or lower level then the Root Album!
			</TD>
		</TR>
		<TR>
			<TD>
				Columns:
			</TD>
			<TD>
				<INPUT TYPE="TEXT" id="columns">
			</TD>
			<TD>
				If not set, default value will be used
			</TD>
		</TR>
		<TR>
			<TD>
				Rows:
			</TD>
			<TD>
				<INPUT TYPE="TEXT" id="rows">
			</TD>
			<TD>
				If not set, default value will be used
			</TD>
		</TR>
	</TABLE>
	<P>
		<INPUT TYPE="BUTTON" VALUE="Insert Album" onclick="GalleryTiny.insert(GalleryTiny.e)">
	</P>
</form>
</body>
</HTML>

<?php
function getOptions($parentID, $level)
{
	global $WPGallery3_tblprefix, $WPGallery3con;
	
	$ret = '';
	$sqlGal = 'SELECT * FROM ' . $WPGallery3_tblprefix . 'items WHERE type = "album" AND parent_id = ' . $parentID . ' ORDER BY title ASC;';
	$rows = mysql_query( $sqlGal , $WPGallery3con );
	
	while ($row = mysql_fetch_assoc($rows))
	{
		$ret .= '<OPTION VALUE="' . $row['id'] . '">';
		for ($i = 1; $i <= ($level - 1); $i++)
		{
			$ret .= '&nbsp;&nbsp;&nbsp;';
		}
		if ($level > 0)
		{
			$ret .= '+-';
		}
		$ret .= $row['title'] . '</OPTION>';
		$ret .= getOptions( $row['id'] , $level + 1);
	}
	return $ret;
}
?>
