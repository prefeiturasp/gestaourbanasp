=== Facebook Album ===
Contributors: mglaman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTT2K9UJZJ55S
Tags: facebook, albums, pictures
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Put your Facebook page albums on your WordPress.

== Description ==

Facebook Album allows your organization to save time by linking your Facebook albums to your WordPress site. No longer will you need to use an image gallery system and upload the same photo multiple times. Now you can streamline the process and let Facebook take care of your pictures.

All you need to do is copy the URL the the Facebook album, and paste it in the short code, or in the widget text.

Visit Settings -> Facebook Album from your WordPress dashboard to customize the plugin. From there you can reverse the album display, disable the included lightbox script, and more.

= Shortcode Example: = 
[fbalbum url=https://www.facebook.com/media/set/?set=a.376995711728.190761.20531316728]

[fbalbum url=https://www.facebook.com/media/set/?set=a.376995711728.190761.20531316728 limit=10]

If you have questions, suggestions, or problems please email the developer at nmd.matt@gmail.com

== Installation ==

Upload the Facebook Album plugin to your WordPress plugin folder, and then just activate it! Use the shortcode in posts and pages.

 == Frequently Asked Questions ==

= My album is never found? =
Double check when you paste your Facebook page's album URL. If you paste within the rich text editor, it will make it a link and wrap it with HTML! This will break the shortcode. Your best bet is to also paste in the Text mode.
 
= I've done everything right and it says error loading album or album not found =
The script uses two ways to pull an API response, if neither are working please contact me so I can help troubleshoot the error
 
= Can I change the thumbnails displayed from the shortcode? =
Yes, find Facebook Albums under the Settings area of your dashboard.

= How do I use Facebook Albums =
Simply use the [fbalbum url=] shortcode. Not sure how it will look? Copy the example in the description.

= I want different thumb sizes for my pictures =
By defauly Facebook stores images in 9 sizes, one being original, and two a pretty similar in miniture thumbnails. I incorporated the basic thumbnails to try and keep the Facebook appeal to the plugin.

== Changelog ==

= 1.0.8 =
* Clean up code
* Bug fixes
* Widgets will now respect reverse album order option

= 1.0.7.1 =
* Misc bug fixes. 
* Loading graphic and close graphic should always load properly. 
* Widgets now use Lightbox as well. 
* Added titles to widgets

= 1.0.7 =
Can specify different albums for each page, if no URL specified uses default within the widget setting.

= 1.0.6a =
Added gallery support, fixed Lightbox images. Users can now go left to right to browse album

= 1.0.6 =
Allows you to add limit= to shorten amount of photos.

= 1.0.4 =
Pulls all pictures from album at one time. Uses Lightbox2 to display pictures within site instead of linking to Facebook, also displays captions within Lightbox.

= 1.0.3 =
Added options page under Settings menu to allow image thumbnail to be set.

= 1.0.2a =
Remove default URL, reports it was overriding input.

= 1.0.2 =
Added ability to change thumbnail sizes and quantity for the widget.

= 1.0.1 =
 First release!
 
== Screenshots ==

1. The widget form
2. Page using the shortcode