(function() {
	tinymce.create('tinymce.plugins.WPGallery3', {
		init : function(ed, url) {
			ed.addCommand('WPGallery3', function() {
				ed.windowManager.open({
					file : url + '/wp-gallery3_editor.php',
					width : 600 + parseInt(ed.getLang('wpgallery3.delta_width', 0)),
					height : 200 + parseInt(ed.getLang('wpgallery3.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});
			ed.addButton('wpgallery3', {title : 'WPGallery3', cmd : 'WPGallery3', image: url + '/wp-gallery3.png' });
		},
		getInfo : function() {
			return {
				longname : 'WPGallery3',
				author : 'Josh Burkard',
				authorurl : 'http://www.josh-burkard.ch',
				infourl : 'http://www.josh-burkard.ch',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});
	tinymce.PluginManager.add('wpgallery3', tinymce.plugins.WPGallery3);
})();
