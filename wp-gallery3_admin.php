<?php
	/*
		This file will display and execute the admin Settings page.
	*/
	if($_POST['WPGallery3_hidden'] == 'Y')
	{
		// If Settings where changed / Submit button was clicked
		
		$WPGallery3_baseurl = $_POST['WPGallery3_baseurl'];
		update_option( 'WPGallery3_baseurl' , $WPGallery3_baseurl );
		
		$WPGallery3_dbhost = $_POST['WPGallery3_dbhost'];
		update_option( 'WPGallery3_dbhost' , $WPGallery3_dbhost );
		
		$WPGallery3_dbname = $_POST['WPGallery3_dbname'];
		update_option( 'WPGallery3_dbname' , $WPGallery3_dbname );

		$WPGallery3_dbuser = $_POST['WPGallery3_dbuser'];
		update_option( 'WPGallery3_dbuser' , $WPGallery3_dbuser );

		if ($_POST['WPGallery3_dbpwd'])
		{
			// only if database password field is not empty
			$WPGallery3_dbpwd = $_POST['WPGallery3_dbpwd'];
			update_option( 'WPGallery3_dbpwd' , $WPGallery3_dbpwd );
		}

		$WPGallery3_tblprefix = $_POST['WPGallery3_tblprefix'];
		update_option( 'WPGallery3_tblprefix' , $WPGallery3_tblprefix );

		$WPGallery3_Columns = $_POST['WPGallery3_Columns'];
		update_option( 'WPGallery3_Columns' , $WPGallery3_Columns );
		
		$WPGallery3_Rows = $_POST['WPGallery3_Rows'];
		update_option( 'WPGallery3_Rows' , $WPGallery3_Rows );

		$WPGallery3_picsize = $_POST['WPGallery3_picsize'];
		update_option( 'WPGallery3_picsize' , $WPGallery3_picsize );
		
		$WPGallery3_thumbsize = $_POST['WPGallery3_thumbsize'];
		update_option( 'WPGallery3_thumbsize' , $WPGallery3_thumbsize );
		
		if ( $_POST['WPGallery3_autocreateuser'] )
		{
			$WPGallery3_autocreateuser = 'checked';
			update_option( 'WPGallery3_autocreateuser' , '1' );
		} else {
			$WPGallery3_autocreateuser = '';
			update_option( 'WPGallery3_autocreateuser' , '0' );
		}
		
		if ( $_POST['WPGallery3_showpictitle'] )
		{
			$WPGallery3_showpictitle = 'checked';
			update_option( 'WPGallery3_showpictitle' , '1' );
		} else {
			$WPGallery3_showpictitle = '';
			update_option( 'WPGallery3_showpictitle' , '0' );
		}
		
		if ( $_POST['WPGallery3_showalbumtitle'] )
		{
			$WPGallery3_showalbumtitle = 'checked';
			update_option( 'WPGallery3_showalbumtitle' , '1' );
		} else {
			$WPGallery3_showalbumtitle = '';
			update_option( 'WPGallery3_showalbumtitle' , '0' );
		}
		
		$WPGallery3_basedir = $_POST['WPGallery3_basedir'];
		update_option( 'WPGallery3_basedir' , $WPGallery3_basedir );
		?>
		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
		<?php
	} else {
		// Normal page display
		
		// Read options
		$WPGallery3_baseurl      = get_option ( 'WPGallery3_baseurl' );
		$WPGallery3_basedir      = get_option ( 'WPGallery3_basedir' );
		$WPGallery3_dbhost       = get_option ( 'WPGallery3_dbhost' );
		$WPGallery3_dbname       = get_option ( 'WPGallery3_dbname' );  
		$WPGallery3_dbuser       = get_option ( 'WPGallery3_dbuser' );  
		$WPGallery3_dbpwd        = get_option ( 'WPGallery3_dbpwd' );  
		$WPGallery3_tblprefix    = get_option ( 'WPGallery3_tblprefix' );
		$WPGallery3_Columns      = get_option ( 'WPGallery3_Columns' );
		$WPGallery3_Rows         = get_option ( 'WPGallery3_Rows' );
		$WPGallery3_picsize      = get_option ( 'WPGallery3_picsize' );
		$WPGallery3_thumbsize    = get_option ( 'WPGallery3_thumbsize' );
		
		if ( get_option ( 'WPGallery3_autocreateuser' ) == 1 )
		{
			$WPGallery3_autocreateuser = 'checked';
		} else {
			$WPGallery3_autocreateuser = '';
		}
		if ( get_option ( 'WPGallery3_showpictitle' ) == 1 )
		{
			$WPGallery3_showpictitle = 'checked';
		} else {
			$WPGallery3_showpictitle = '';
		}
		if ( get_option ( 'WPGallery3_showalbumtitle' ) == 1 )
		{
			$WPGallery3_showalbumtitle = 'checked';
		} else {
			$WPGallery3_showalbumtitle = '';
		}
		
		// Set default values if no value was set before
		if ( $WPGallery3Columns == '' )
		{
			$WPGallery3_Columns = 3;
		}
		if ( $WPGallery3_Rows == '' )
		{
			$WPGallery3_Rows = 4;
		}
	}
	// display form
?>
		<div class="wrap">
		<?php    echo "<h2>" . __( 'WP Gallery 3 Options', 'WPGallery3_trdom' ) . "</h2>"; ?>

		<form name="WPGallery3_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="WPGallery3_hidden" value="Y">
			<?php    echo "<h4>" . __( 'Gallery Base Settings', 'WPGallery3_trdom' ) . "</h4>"; ?>
			<table>
				<tr>
					<td style="width: 150px;">
						<?php _e("Gallery3 base URL: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_baseurl" style="width: 300px;" value="<?php echo $WPGallery3_baseurl; ?>" size="20">
					</td>
					<td>
						<?php _e(" ex: http://www.example.com/gallery3/" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("Gallery3 PHP base Dir: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_basedir" style="width: 300px;" value="<?php echo $WPGallery3_basedir; ?>" size="20">
					</td>
					<td>
						<?php _e(" ex: /home/www/web100/html/gallery/" ); ?>
					</td>
				</tr>
			</table>
			<?php    echo "<h4>" . __( 'Database Settings', 'WPGallery3_trdom' ) . "</h4>"; ?>
			<table>
				<tr>
					<td style="width: 150px;">
						<?php _e("Database host: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_dbhost" style="width: 300px;" value="<?php echo $WPGallery3_dbhost; ?>" size="20">
					</td>
					<td>
						<?php _e(" ex: localhost" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("Database name: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_dbname" style="width: 300px;" value="<?php echo $WPGallery3_dbname; ?>" size="20">
					</td>
					<td>
						<?php _e(" ex: gallery3-db" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("Database user: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_dbuser" style="width: 300px;" value="<?php echo $WPGallery3_dbuser; ?>" size="20">
					</td>
					<td>
						<?php _e(" ex: root" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("Database password: " ); ?>
					</td>
					<td>
						<input type="password" name="WPGallery3_dbpwd" style="width: 300px;" value="" size="20">
					</td>
					<td>
						<?php _e(" ex: secretpassword - " ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("Table prefix: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_tblprefix" style="width: 300px;" value="<?php echo $WPGallery3_tblprefix; ?>" size="20">
					</td>
					<td>
						<?php _e(" ex: gal_" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("create new WordPress-users automatically in Gallery3: " ); ?>
					</td>
					<td>
						<input type="checkbox" name="WPGallery3_autocreateuser" <?php echo $WPGallery3_autocreateuser; ?>>
					</td>
					<td>
						
					</td>
				</tr>
			</table>
			<?php    echo "<h4>" . __( 'Design', 'WPGallery3_trdom' ) . "</h4>"; ?>
			<table>
				<tr>
					<td style="width: 150px;">
						<?php _e("Columns: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_Columns" style="width: 150px;" value="<?php echo $WPGallery3_Columns; ?>" size="20">
					</td>
					<td>
						<?php _e("Columns" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("Rows: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_Rows" style="width: 150px;" value="<?php echo $WPGallery3_Rows; ?>" size="20">
					</td>
					<td>
						<?php _e("Rows" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("max. picture size: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_picsize" style="width: 150px;" value="<?php echo $WPGallery3_picsize; ?>" size="20">
					</td>
					<td>
						<?php _e("px" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("max. thumbnail size: " ); ?>
					</td>
					<td>
						<input type="text" name="WPGallery3_thumbsize" style="width: 150px;" value="<?php echo $WPGallery3_thumbsize; ?>" size="20">
					</td>
					<td>
						<?php _e("px" ); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("Show title for pictures: " ); ?>
					</td>
					<td>
						<input type="checkbox" name="WPGallery3_showpictitle" <?php echo $WPGallery3_showpictitle; ?>>
					</td>
					<td>
						
					</td>
				</tr>
				<tr>
					<td style="width: 150px;">
						<?php _e("show title for albums: " ); ?>
					</td>
					<td>
						<input type="checkbox" name="WPGallery3_showalbumtitle" <?php echo $WPGallery3_showalbumtitle; ?>>
					</td>
					<td>
						
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Settings', 'WPGallery3_trdom' ) ?>" />
			</p>
		</form>
	</div>