=== WPGallery3 ===
Plugin Name:         WPGallery3
Version:             0.9.3
Author:              Josh Burkard
Author link:         http://www.josh-burkard.com/
Donate link:         https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AFTH4UJ258SY4
Tags:                Menalto Gallery3
Stable tag:          0.9.3
Requires at least:   3.4
Tested up to:        3.4.1
License:             GPLv2
Contributors:        jburkard

WPGallery3 displays your Menalto Gallery3 albums.

== Description ==

WPGallery3 displays your Menalto Gallery3 albums in a post or page. Unlike other Gallery3 plugins, tis plugin support album and picture permissions.

== Installation ==

= Installation =
1. Make sure you're running WordPress version 3.4 or better. I didn't tested this plugin with older versions.
2. Make sure you're running Menalto's Gallery3 version 3.0.1 or better. You can download it at http://gallery.menalto.com/. It is recomended, that your Gallery3 installation is on the same server (not in the same directory) like your WordPress installation.
3. Download the zip file and extract the contents.
4. Upload the 'wpgallery3' folder to your plugins directory (wp-content/plugins/).
5. Activate the plugin through the 'plugins' page in WP.
6. Set the Base- and Database Settings for Gallery3 at 'WP Gallery3->Settings' , etc...
7. Create a post or page and insert [wpgallery3] anywhere in the content.

== Frequently Asked Questions ==

= Can i run Gallery3 on different servers? =
Yes, but there are some extra configurations needed. Additional informations will follow.

== Screenshots ==
1. Gallery Page
2. Option Page

== Upgrade Notice ==
There are no upgrade notices available yet

== Options ==

The settings page allows you to change the this options:

= Gallery3 base URL =
The public url of your Gallery3 URL
ex: http://www.example.com/gallery3/

= Gallery3 PHP base Dir =
The PHP Base directory of your Gallery3 installation
ex: /home/www/web100/html/gallery/ 

= Database host =
The hostname where your Gallery3 database is
ex: localhost

= Database name =
The name of your Gallery3 database
ex: gallery3-db

= Database user / password =
the login credentials to access your Gallery3 database

= Table prefix =
The table prefix of your Gallery3 installation
ex: gal_

= create new WordPress-users automatically in Gallery3 =
If this option is set, all newly created and changed Wordpress users will be writen to Gallery3 database. This is used to set permissions for some pictures and albums to the users. The migration will contain this user-datas:
- Logon-Name
- Password (as MD5-Hash)
- DisplayName
- Email-Address

== Changelog ==

= 0.9.3 =
* Added Button to Post / Page-Editor

= 0.9.2 =
* Fixes some installation issues

= 0.9.1 =
* Fixes problems with case-sensitive tag [WPGallery3]
* Adds some special options: Colums, Rows, AlbumID, RootAlbumID

= 0.9.0 =
* First public beta release

= 0.0.1 =
* Initial release version.