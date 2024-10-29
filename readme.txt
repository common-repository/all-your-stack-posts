=== All Your Stack Posts ===
Contributors: brasofilo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=JNJXKWBYM9JP6&lc=ES&item_name=All%20Your%20Stack%20Posts%20%3a%20Rodolfo%20Buaiz&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: stackexchange, questions, answers, print, pdf
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Grab Questions or Answers from a given user in a given Stack Exchange site and display them in a simple page ready to print (using your system capabilities).

== Description ==

Inspired by the Meta question [How can I download my content from a beta site?](http://meta.stackoverflow.com/q/194475/185667).

Intended as a mean to export all of a person's participation in a Stack Exchange site. When viewing the page, one can print or export as HTML/PDF using the browser and system capabilities.
The maximum number of posts per page is 100 and that's a SE API limitation. 

If the user's Answers are being viewed, the plugin will show 100 Questions per page with the user's Answer bellow it.

If the user's Questions are being viewed, the plugin will show 100 Questions per page with all the Answers given.

Check the twin plugin [Stack Posts Widget](http://wordpress.org/plugins/stack-exchange-posts-widget/).

Translations: Español.

== Installation ==

Extract the zip file and upload the contents to the `wp-content/plugins/` directory of your WordPress installation, and then activate the plugin from plugins page. 

= Uninstall =


The plugin doesn't saves any option in the database. When de-activating the plugin, it deletes its template from the active theme folder. There's no uninstall procedure.


== Frequently Asked Questions ==

* After installed and activated, the plugin creates a template in the theme folder.

* Create a new page and select the template "Stack Q&A's".

* The plugin meta box only appears when this template is selected.

* In the plugin's custom meta box, select the Site, User ID, Posts per page (max. 100) and Enable caching.

= Bugs, contributions and feature requests =

Don't hesitate to open a [support thread](http://wordpress.org/support/plugin/all-your-stack-posts).

== Screenshots ==

1. Plugin Meta Box

2. Showing Chuck Norris answers

== Changelog ==

= 1.0 =

* Initial Public Release


== Upgrade Notice ==

= 1.1 =

* Added plugin messages, updated donate link

= 1.0 =

* Initial Public Release

== Acknowledgments ==

* Copy plugin template to theme folder: [Page Template Example](https://github.com/tommcfarlin/page-template-example/), by Tom McFarlin.

* Some styling rules shameless plugged from: [StackTack](https://github.com/nathan-osman/StackTack-WordPress-Plugin), by Nathan Osman

* Stack Exchange API library: [StackPHP](http://stackapps.com/q/826/10590)

* Pagination scripts: [Zebra Pagination](http://stefangabos.ro/php-libraries/zebra-pagination/), by Stefan Gabos.

* Dropdown with icons: [Image dropdown](https://github.com/marghoobsuleman/ms-Dropdown), by Marghoob Suleman.

* [Plugin update checker](https://github.com/YahnisElsts/plugin-update-checker), by Yahnis Elsts.
