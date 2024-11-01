<?php
?>
<div id="wpgallery3-donate">
	<p>
		<strong>
			WP Gallery3
		</strong>
		<br />
		Version <?php echo plugin_get_version() ; ?>
		<br />
		<br />
		To display Gallery3 albums in your post / pages add <strong>[wpgallery3]</strong> anywhere in your content.
		<br />
		<br />
		To manage your Gallery3 album visit
		<?php
			$WPGallery3_baseurl = get_option ('WPGallery3_baseurl');
			echo '<a href="' . $WPGallery3_baseurl . '" target="_blank">Gallery-Admin</a>';
		?>
	</p>
</div>
