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
		This plugin was created by:
		<br />
		<strong>
			Josh Burkard
		</strong>
		<br />
		<br />
		You can get more informations under <a href="http://www.josh-burkard.ch" target="_blank">http://www.josh-burkard.ch</a>
	</p>
</div>
<div id="wpgallery3-donate">
<p><?php _e('If you like the WP Gallery3 plugin, i would appreciate a small donation to support my work. You can additionally add an idea to make the WP Gallery3 plugin even better. Just click the button below. Thank you!', 'WPGallery3_trdom') ?></p>
<?php 
	
	PrintPayPalButton();
	// WPFB_Admin::PrintFlattrButton();
	?>
</div>

<?php
function PrintPayPalButton()
{
		$lang = 'en_US';
		$supported_langs = array('en_US', 'de_DE', 'fr_FR', 'es_ES', 'it_IT', 'ja_JP', 'pl_PL', 'nl_NL');
		
		/*
		 * fr_FR/FR
		 * https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/FR/i/btn/btn_donateCC_LG.gif
		 * https://www.paypalobjects.com/WEBSCR-640-20110401-1/de_DE/DE/i/btn/btn_donateCC_LG.gif
		 * https://www.paypalobjects.com/WEBSCR-640-20110401-1/es_ES/ES/i/btn/btn_donateCC_LG.gif
		 * https://www.paypalobjects.com/WEBSCR-640-20110401-1/it_IT/i/btn/btn_donateCC_LG.gif
		 */
		
		// find out current language for the donate btn
		if(defined('WPLANG') && WPLANG && WPLANG != '' && strpos(WPLANG, '_') > 0) {
			if(in_array(WPLANG, $supported_langs))
				$lang = WPLANG;
			else {
				$l = strtolower(substr(WPLANG, 0, strpos(WPLANG, '_')));
				if(!empty($l)) {
					foreach($supported_langs as $sl) {
						$pos = strpos($sl,$l);
						if($pos !== false && $pos == 0) {
							$lang = $sl;
						}
					}
				}
			}
		}
?>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="6MHUAC3MB8U5G">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>


<?php 
}
?>