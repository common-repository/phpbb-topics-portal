=== phpBB Topics Portal ===
Contributors: macmiller
Donate link: tbd
Tags: phpBB, Forum, Recent Posts, Widget
Requires at least: 3.2.1
Tested up to: 3.3.1
Stable tag: 1.2
License:  GPLv2 or later

A widget that accesses your phpBB forum and displays recent posts on your Wordpress page.

== Description ==

The plugin is intended to create a number of links which you can display on your wordpress page.  Each link represents a recent post to the phpBB forum.  Clicking on a link will take you to the associated post within that topic on the phpBB forum.  There are a number of parameters which indicates how many items should be returned and the length description.

The returned item will look like:
* `POST TITLE(number of replies) in TOPIC TITLE on DATE TIME`

== Installation ==

1.  Copy to your plugin folder.
2.  Activate the plugin.
3.  Go to Widgets. 
4.  Drag the widget to the desired sidebar.
5.  Establish the required parameter values on the Widget Screen.

Widget Setup

* Title:  Type in the desired title.
* phpBB Config Location:  This is the file location of your phpBB Config file.  There is a Document Root Path display on the bottom of the screen to help you with this.  This file is in your phpBB Directory and is config.php.  The indicated location is the full file system location and not the URL.  
* phpBB URL Location:  This is the url location of your forum.  This field is optional and can normally left blank except for the case when the forum is a subdomain such as http://myforum.mywebsite.com .  If this field is left blank the forum URL will be taken from the Config Location.  The field must be entered with 'http://' at the beginning and no closing slash at the end for it to work right.
* Exclude Forum List:  A list of forums to be excluded.
* Return List Length:  The number of post links to be displayed.
* Topic Text Length:  The overall length of the returned topic title, excess characters are truncated.
* Date Format:  The date/time format as used in the php date function, [php date function and date syntax](http://www.php.net/manual/en/function.date.php/ "php date formatting") .
* Document Root Path:  For display only.  Indicates the root file path.  This will be helpful in setting up the phpBB Config Location.
* Parameter Validation Return Area:  For display only.  Indicates if there are any errors.

Defines in phpbb_topics_portal_Guts.php

There is only one optional define statement in this script, for the most part it can be left at its default value.  

* define("sqldb_MULT", 10);  ->  Because some of the returned links are discarded, the number of reads made on the database is uncertain.  This variable limits the read statements to 10 times the requested number (at the default setting of 10).

== Frequently Asked Questions ==

= I never worked with CSS before.  Can I make this plugin look nice on my blog? =

Probably.  I don't believe that any special 

= I don't know how to edit a php script.  Should I attempt to use this plugin? =

You will not need to edit any php scripts to install and use this plugin.


== Screenshots ==

1. The available widget with associated text as it appears under the Appearance -> Widgets page.
2. This is how the screen widgets parameters setting page appears when you click on the widgets page.
3.  This is the generated output for Recent Topics on the Wordpress page.

== Changelog ==

= 1.0 =
* 31-Oct-11/Initial release.
= 1.1 =
* 10-Jan-12/The URL is computed automatically from the file location, but this did not work for subdomains, such as http://myforum.myweb.com .  Added a new forum phpBB Forum URL to override the computed value, it can be used as needed.  In other words, for most installations you can just leave this blank but if you do use it make sure it is the correct URL for your forum.
= 1.11 =
* 11-Jan-12/Updated the readme only.
= 1.2 =
* 22-May-12/phpbb_topics_portal_Guts.php
Added a line after making the DB connection to put the connection in UTF8 mode (so other character sets are shown correctly)
Added back somehow deleted constant sqldb_MAX_READS.
Also defaulted the exclude list array to -1 if ommitted to avoid error messages and allow for no excludes.  


== Upgrade Notice ==

= 1.2 =
* any version up to 1.2.  No database changes, only replace changed files.