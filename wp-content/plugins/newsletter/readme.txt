=== Newsletter ===
Tags: newsletter,email,subscription,mass mail,list build,email marketing,direct mailing
Requires at least: 3.3.0
Tested up to: 3.5.1
Stable tag: 3.2.7
Donate link: http://www.satollo.net/donations

Add a real newsletter to your blog. In seconds. For free. With unlimited emails and subscribers.

== Description ==

This plug-in adds a real newsletter system to your WordPress blog. Perfect for list building, 
you can create cool emails with visual editor, send and
track them.

Unlimited subscribers, unlimited e-mails.

Key features:

* **unlimited subscribers** (the database is your, why should I limit you?) with statistics
* **unlimited emails** with tracking
* subscription widget, page or via custom form
* integrated with **WordPress user registration**
* single and double opt-in plus privacy acceptance checkbox (as per European laws)
* subscriber preferences to fine target your campaigns
* **SMTP** ready (Gmail, SendGrid, ...)
* html and text version messages
* configurable themes
* every message and label **fully translatable** from administrative panels (no .po/.mo file to edit)
* diagnostic panel for **easy system tests**
* **extensible** with specific modules (Facebook, Reports, Feed by Mail, Follow Up)

Visit the [Newsletter official page](http://www.satollo.net/plugins/newsletter) to know more.

Thank you, Stefano Lissa (Satollo).

== Installation ==

1. Put the plug-in folder into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Optional: go to the options page and configure the plugin

== Frequently Asked Questions ==

See the [Newsletter FAQ](http://www.satollo.net/plugins/newsletter/newsletter-faq) or the
[Newsletter Forum](http://www.satollo.net/forums) to ask for help.

For documentation start from [Newsletter official page](http://www.satollo.net/plugins/newsletter).

Thank you, Stefano Lissa (Satollo).

== Screen shots ==

No screen shots are available at this time.

== Changelog ==

= 3.2.7 =

* Added a controls for the Reports module version 1.0.4
* Changed opening tracking and removed 1x1 GIF
* Added support for popup on subscription form
* Fixed the link to the reports module

= 3.2.6 =

* Fixed the forced preferences on subscription panel

= 3.2.5 =

* Fixed the home_url and blog_url replacements
* Added the cleans up of tags used in href attributes
* Fixed the cleans up of URL tags
* Added module version checking support
* Added the welcome email option to disable it
* Fixed the new subscriber notification missing under some specific conditions

= 3.2.4 =

* Added target _blank on theme links so they open on a new windows for the online version
* Changed to the plugins_url() function
* Added clean up of url tags on composer

= 3.2.3 =

* Added schedule list on Diagnostic panel
* Removed the enable/disable resubscription option
* Added a check for the delivery engine shutdown on some particular situations
* Revisited the WordPress registration integration
* Revisited the WordPress user import and moved on subscriber massive action panel
* Added links to new documentation chapter
* Removed a survived reference to an old table
* Reactivated the replacement of the {blog_url} tag
* Fixed the tracking code injection
* Fixed a default query generation for compatibility with 2.5 version
* Fixed the tag replacements when using the old forms

= 3.2.2 =

* Fixed the subscription options change problem during the upgrade
* English corrections by Rita Vaccaro
* Added the Feed by Mail demo module
* Added support for the Facebook module

= 3.2.1 =

* Fixed fatal error with old form formats

= 3.2.0 =

* Added hint() method to NewsletterControls
* Fixed the Newsletter::replace_date() to replace even the {date} tag without a format
* Added NewsletterModule::format_time_delta()
* Added NewsletterModule::format_scheduler_time
* Improved the diagnostic panel
* Fixed an error on subscription with old forms
* Fixed the unsubscription with old formats
* Fixed the confirmation for multiple calls
* Fixed user saving on new installation (column missing for followup module)
* Added compatibility code with domain remaping plugin
* Added a setting to let unsubscribed users to subscribe again
* Added the re-subscription option

= 3.1.9 =

* Added the NEWSLETTER_MAX_EXECUTION_TIME
* Added the NEWSLETTER_CRON_INTERVAL
* Improved the delivery engine performances
* Improved the newsletter list panel
* Change the subscription in case of unsubscribed, bounced or confirmed address with a configurable error message
* Some CSS review
* Fixed the unsubscription procedure with a check on user status
* Added Pint theme

= 3.1.7 =

* Added better support for Follow Up for Newsletter
* Fixed integration with Feed by Mail for Newsletter
* Fixed a bug on profile save
* Fixed a message about log folder on diagnostic panel
* Fixed the sex field on user creation

= 3.1.6 =

* Fixed the subscription form absent on some configurations

= 3.1.5 =

* Content locking deactivated if a user is logged in
* Added a button to create a newsletter dedicated page
* Added top message is the newsletter dedicated page is not configured
* Fixed the subscription process with the old "na" action
* Added a new option with wp registration integration
* Added the opt-in mode to wp registration integration

= 3.1.4 =

* Fixed a bug on post/page preview

= 3.1.3 =

* Added support for SendGrid Module
* Fixed a fatal error on new installations on emails.php

= 3.1.2 =

* Fixed the access control for editors
* Improved to the log system to block it when the log folder cannot be created
* Moved all menu voices to the new format
* Improved the diagnostic panel
* Added ability to send and email to not confirmed subscribers
* Fixed a problem with internal module versions

= 3.1.1 =

* Fixed the copy and delete buttons on newsletter list
* Removed the old trigger button on newsletter list
* Fixed the edit button on old user search
* Improved the module version checking
* Added the "unconfirm" button on massive subscriber management panel

= 3.1.0 =

* Added link to change preferences/sex from emails
* Added tag reference on email composer
* Added "negative" preference selection on email targeting
* Improved the subscription during WordPress user registration
* Fixed the preference saving from profile page
* Fixed the default value for the gender field to "n"
* Added loading of the Feed by Mail module
* Added loading of the Follow Up module
* Added loading of the MailJet module
* Changed the administrative page header
* Changed the subscriber list and search panel
* Improved the locked content feature
* Fixed the good bye email not using the standard email template
* Changed the diagnostics panel with module versions checking
* Fixed some code on NewsletterModule

= 3.0.9 =

* Fixed an important bug

= 3.0.8 =

* Fixed the charset on some pages and previews for umlaut characters

= 3.0.7 =

* Fixed a warning in WP 3.5
* Fixed the visual editor on/off on composer panel

= 3.0.6 =

* Added file permissions check on diagnostic panel
* Fixed the default value for "sex" on email at database level
* Fixed the checking of required surname
* Fixed a warning on subscription panel
* Improved the subscription management for bounced or unsubscribed addresses
* Removed the simple theme of tinymce to reduce the number of files
* Added neutral style for subscription form

= 3.0.5 =

* Added styling for widget
* Fixed the widget html
* Fixed the reset button on subscription panels
* Fixed the language initialization on first installation
* Fixed save button on profile page (now it can be an image)
* Fixed email listing showing the planned status

= 3.0.4 =

* Fixed the alternative email template for subscription messages
* Added user statistics by referrer (field nr passed during subscription)
* Added user statistics by http referer (one r missing according to the http protocol)
* Fixed the preview for themes without textual version
* Fixed the subscription redirect for blogs without permalink
* Fixed the "sex" column on database so email configuration is correctly stored
* Fixed the wp user integration

= 3.0.3 =

* Fixed documentation on subscription panel and on subscription/page.php file
* Fixed the statistics module URL rewriting
* Fixed a "echo" on module.php datetime method
* Fixed the multi-delete on newsletter list
* Fixed eval() usage on add_menu_page and add_admin_page function
* Fixed a number of ob_end_clean() called wht not required and interfering with other output buffering
* Fixed the editor access level

= 3.0.2 =

* Documented how to customize the subscription/email.php file (see inside the file) for subscription messages
* Fixed the confirmation message lost (only for who do not already save the subscription options...)

= 3.0.1 =

* Fixed an extra character on head when including the form css
* Fixed the double privacy check on subscription widget
* Fixed the charset of subscription/page.php
* Fixed the theme preview with wp_nonce_url
* Added compatibility code for forms directly coded inside the subscription message
* Added link to composer when the javascript redirect fails on creation of a new newsletter
* Fixed the old email list and conversion

= 3.0.0 =

* Release

= 2.6.2 =

* Added the user massive management panel

= 2.5.3.3 =

* Updated to 20 lists instead of 9
* Max lists can be set on wp-config.php with define('NEWSLETTER_LIST_MAX', [number])
* Default preferences ocnfigurable on subscription panel

= 2.5.3.2 =

* fixed the profile fields generation on subscription form

= 2.5.3.1 =

* fixed javascript email check
* fixed rewrite of link that are anchors
* possible patch to increase concurrency detection while sending
* fixed warning message on email composer panel

= 2.5.3 =

* changed the confirmation and cancellation URLs to a direct call to Newsletter Pro to avoid double emails
* mail opening now tracked
* fixed the add api
* feed by mail settings added: categories and max posts
* feed by mail themes change to use the new settings
* unsubscribed users are marked as unsubscribed and not removed
* api now respect follow up and feed by mail subscription options
* fixed the profile form to add the user id and token
* subscribers' panel changed
* optimizations
* main url fixed everywhere
* small changes to the email composer
* small changes to the blank theme

= 2.5.2.3 =

* subscribers panel now show the profile data
* search can be ordered by profile data
* result limit on search can be specified
* {unlock_url} fixed (it was not pointing to the right configured url)

= 2.5.2.2 =

* fixed the concurrent email sending problem
* added WordPress media gallery integration inside email composer

= 2.5.2.1 =

* added the add_user method
* fixed the API (was not working) and added multilist on API (thankyou betting-tips-uk.com)
* fixed privacy check box on widget

= 2.5.2 =

* added compatibility with lite cache
* fixed the list checkboxes on user edit panel
* removed the 100 users limit on search panel
* category an max posts selection on email composer

= 2.5.1.5 =

* improved the url tag replacement for some particular blog installation
* fixed the unsubscription administrator notification
* replaced sex with gender in notification emails
* fixed the confirm/unconfirm button on user list
* fixed some labels
* subscription form table HTML

= 2.5.1.4 =

* added {date} tag and {date_'format'} tag, where 'format' can be any of the PHP date formats
* added {blog_description} tag
* fixed the feed reset button
* added one day back button to the feed
* updated custom forms documentation
* fixed the trigger button on emails panel
* changed both feed by mail themes (check them if you create your own theme)
* fixed the custom profile field generation (important!)
* fixed documentation about custom forms

Version 2.5.1.3
- fix the feed email test id (not important, it only generates PHP error logs)
- feed by mail send now now force the sending if in a non sending day
- changed the way feed by mail themes extract the posts: solves the sticky posts problem
- added the feed last check time reset button
- fixed the confirm and cancel buttons on user list
- fixed the welcome email when using a custom thank you page
- added images to theme 1
- added button to trigger the delivery engine
- fixed the widget mail check
- reintroduced style.css for themes
- updated theme documentation
- added CDATA on JavaScript
- fixed theme 1 which was not adding the images
- added theme 3

Version 2.5.1.2
- fixed the old profile fields saving

Version 2.5.1.1
- new fr_FR file
- fixed test of SMTP configuration which was sending to test address 2 instead of test address 1
- bounced voice remove on search filter
- added action "of" which return only the subscription form and fire a subcription of type "os"
- added action "os" that subscribe the user and show only the welcome/confirmation required message
- fixed issue with main page url configuration

Version 2.5.1
- Fixed the widget that was not using the extended fields
- Fixed the widget that was not using the lists
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the widget form
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the main subscription form
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the profile form
- Added the classes "newsletter-email", "newsletter-firstname", "newsletter-surname" to the respective fields on every form
- Removed email theme option on subscription panel (was not used)
- Fixed the welcome email on double opt in process
- Subscription notifications to admin only for confirmed subscription
- Fixed subscription process panel for double opt in (layout problems)
- Improved subscription process panel


Version 2.5.0.1
- Fix unsubscription process not working

Version 2.5.0
- Official first release

= SVN =

Actually I'm using SVN in a wrong way (deliberately). Usually development with SNV
should be done in this way:

* the trunk is where the latest (eventually not working code) is available
* the tags should contains some folders with public releases (stable or beta or alpha)
* the branches should contains some folders representing stable releases which are there to be eventually fixed

For example, when I released the version 3.0 of this plugin, I should have created
a 3.0 folder inside the branches and fixed it when bug were reported. From time to
time from that branch I should have created a tag, for example 3.0.4.

Actually, to make this tag available it should have been reported on the readme.txt
committed on the trunk.

To make it easier, I keep in the trunk the 3.0 branch and I fix it committing the patches
and leaving the official stable tag on readme.txt set to "trunk". That helps me
in quick fixing the plugin without creating tags.

On branches I have the 3.1 branch where I'm develping new features and when ready to be
committed I'll merge them on trunk, updating the trunk.
