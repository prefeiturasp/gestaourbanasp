=== Better Related Posts ===
Contributors: nkuttler
Author URI: http://kuttler.eu/
Plugin URI: http://kuttler.eu/wordpress-plugin/better-related-posts-and-custom-post-types/
Donate link: http://kuttler.eu/donations/
Tags: admin, plugin, related post, related custom post types, related custom taxonomies, i18n, l10n, internationalized, localized, cache, caching, transients, php5, mysql5
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 0.4.3.4

Do you use custom post types and taxonomies? Do you want to list any related content, not just posts?

== Description ==

Custom post types are one the best features in WordPress. Since WordPress 3.0 they are much easier to use. Almost every theme I build for a client features at least one custom post type and usually a custom taxonomy as well.

But there is a problem. There is no plugin that lists related posts that are from a custom post type. After looking through the sourcecode of a few plugins I decided to implement my own related content plugin.

= Plugin Features =

 * Depends on PHP5 and MySQL5
 * Option to add related posts to the RSS feed
 * Use fulltext indexes for good performance
 * Does caching through post meta or transients
 * Incremental scoring for sites with many posts
 * Find related posts, pages and custom post types
 * Score relationships by various MySQL relevance scores or term relationships
 * Use tags, categories or custom taxonomies
 * Internationalized, OO, hopefully well documented and readable

= Other plugins I wrote =

 * [Better Lorem Ipsum Generator](http://kuttler.eu/wordpress-plugin/wordpress-lorem-ipsum-generator-plugin/)
 * [Better Related Posts](http://kuttler.eu/wordpress-plugin/wordpress-related-posts-plugin/)
 * [Custom Avatars For Comments](http://kuttler.eu/wordpress-plugin/custom-avatars-for-comments/)
 * [Better Tag Cloud](http://kuttler.eu/wordpress-plugin/a-better-tag-cloud-widget/)
 * [Theme Switch](http://kuttler.eu/wordpress-plugin/theme-switch-and-preview-plugin/)
 * [MU fast backend switch](http://kuttler.eu/wordpress-plugin/wpmu-switch-backend/)
 * [Visitor Movies for WordPress](http://kuttler.eu/wordpress-plugin/record-movies-of-visitors/)
 * [Zero Conf Mail](http://kuttler.eu/wordpress-plugin/zero-conf-mail/)
 * [Move WordPress Comments](http://kuttler.eu/wordpress-plugin/move-wordpress-comments/)
 * [Delete Pending Comments](http://kuttler.eu/wordpress-plugin/delete-pending-comments/)
 * [Snow and more](http://kuttler.eu/wordpress-plugin/snow-balloons-and-more/)

== Installation ==

If you have a big site keep in mind that installing this plugin will create a fulltext index for your posts, so the size of the wp_posts table can almost double.

1. Upload the plugin to your plugins directory
2. Enable the plugin
3. Enable automatic display of related posts

= How to place a related content list manually =

If you don't enable automatic display of related posts on the plugin options page (the very first setting) you'll have to  use the template tag `<?php the_related(); ?>` to insert a related content list into your theme. If you use this outside of the WordPress loop you have to pass the post ID as parameter.

= How to build a custom loop of related posts =

Here is a short example how to build your own loop of related posts. This way you can add excerpts, post thumbnails etc.

`<?php
    $scores = the_related_get_scores(); // pass the post ID if outside of the loop
    $posts = array_slice( array_keys( $scores ), 0, 5 ); // keep only the the five best results
    $args = array(
        'post__in'            => $posts,
        'posts_per_page'    => 5,
        'caller_get_posts'    => 1 // ignore sticky status
    );
    $my_query = new WP_Query( $args );
    if ( $my_query->have_posts() ) {
        while ( $my_query->have_posts() ) {
            $my_query->the_post();
            echo '<a href="' .  get_permalink( get_the_ID() ) . '">';
            the_title();
            echo '</a>';
            the_excerpt();
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'thumb' );
            }
            echo '<br>';
        }
    }
    else {
        echo "No posts found...";
    }
?>`

Please notice that the posts won't be ordered by score if you build a loop like this. To accomplish this you'll have to sort the posts in $my_query manually. I have a relevant example on [how to sort posts manually](http://www.nkuttler.de/paste/1l3/) on my site. I'll build something like this into the plugin in the future.

= How to evaluate different scoring methods =

Logged in admins can get the results of all scoring methods by using the template tag `<?php the_related_analyze(); ?>` in the loop of their theme. Example:

`<?php
    if ( current_user_can( 'manage_options' ) && function_exists( 'the_related_analyze' ) ) {
        the_related_analyze();
    }
?>`

= The scoring methods =

Content to content, title to content and title do title are simple MySQL fulltext searches.

Keywords to content and keywords to title do a fulltext search for a string that contains all terms a post has. If a post is in the category Fruits and tagged sweet a fulltext search for "Fruits sweet" will be performed.

Terms against taxonomies searches for posts that have the same terms as the current one. By default only posts that use the same taxonomy are found. It is however as well possible to find posts that use a different taxonomy, by searching this other taxonomy for terms of the same name. See the examples below.

= How to use different configurations at the same time =

It is possible to use different configurations of the plugin at the same time. Let's say you use the normal related content feature for your posts. But you also have a custom post type 'venue'. To get a custom list of related venues use this:

`<?php
the_related(
    get_the_ID(),
    array(
        'usept'    => array(
            'venue'    => true
        ),
        'storage_id'    => 'ventures-better-related-'
    )
);
?>`

It is imporant to define a storage ID, or the plugin will overwrite the scores for the standard posts with the scores for your custom configuration. The default storage ID is 'better-related-', so you should avoid that unless you changed the default.

To get a similar listing that includes related posts (from the built-in 'post' post type) use:

`<?php
the_related(
    get_the_ID(),
    array(
        'usept'    => array(
            'post'        => true,
            'venue'        => true
        ),
        'maxresults'    => 7,
        'storage_id'    => 'more-better-related-'
    )
);
?>`

Now an example that shows off all configuration options available:

`<?php
the_related(
    get_the_ID(),
    array(
        'usept'         => array(
            'post'      => true,
            'venue'     => true
        ),
        'usetax'        => array(
            'drinks'    => true,
            'food'      => true,
            'post_tag'  => true,
            'category'  => true
        ),
        'do_c2c'        => 1.5,
        'do_t2c'        => 2,
        'do_k2c'        => 2,
        'do_t2t'        => 1,
        'do_k2t'        => 2,
        'do_x2x'        => 4.3,
        'minscore'      => 25,
        'maxresults'    => 5,
        'log'           => true,
        'loglevel'      => 'taxquery',
        'storage_id'    => 'better-related-full-example-',
        'storage'       => 'transient',
        'cachetime'     => 1,
        'querylimit'    => 10000,
        'incremental'   => true,
        't_querylimit'  => 30000,
        'relatedtitle'  => 'Related venues and posts',
        'relatednone'   => 'No related venues or posts'
    )
);
?>`

Notes:

* Using the usetax parameter means that cross-taxonomy searches will be performed. It is not recommended to use this parameter at the moment, as it's usage will probably change in the future. However, if different taxonomies share terms you can play with this parameter.
* You should not log on live sites.
* The high querylimit in this example could slow down your site.
* The transient storage should only be used temporarily and for testing purposes.
* Enabling incremental scoring means that the relatedness scores for posts will be calculated in multiple steps. In the example above up to 30000 related posts will be found and the necessary queries will be spread across three page views, assuming there is no cache.

= Find posts related to a string =

The template tag `<?php get_the_related_for_string() ?>` returns related posts for a string. The tag `<?php the_related_for_string() ?>` prints a list of posts related to a string. Usage:

`<?php the_related_for_string( 'foo' ); ?>`

I use this on 404 pages currently, see the paste for my [dynamic WordPress 404 search form](http://www.nkuttler.de/paste/1kz/). In the future I will probably add a replacement for the built-in WordPress search to this plugin.

= mysqld configuration =

Generally speaking, the plugin should work out of the box. However, there are two settings you might want to change, see the [mysql full-text fine-tuning docs](http://dev.mysql.com/doc/refman/5.1/en/fulltext-fine-tuning.html).

<strong>ft_min_word_len</strong> controls the minumum length of words. This defaults to 4, which might be too small if you use acronyms like CSS, PHP etc. You can change or set that option to 3 in your mysqld config. You will have to re-build your fulltext indexes after doing this, use "REPAIR TABLE table_name QUICK;" where table_name is wp_posts on a default WordPress install.

The second option could be interesting if your site is not in english and you want to improve search results. Use the <strong>ft_stopword_file</strong> option to create your own stopwords file. MySQL uses english stopwords by default.

== Screenshots ==

1. The options page.

== Frequently Asked Questions ==

None yet.

== Changelog ==
= 0.4.3.4 ( 2012-12-13 ) =
 * 3.5 compatibility
= 0.4.3.3 ( 2010-11-18 ) =
 * Add check for MySQL version
 * Fix serious bug, score couldn't be calculated. Thanks dave.
 * Fix logging bug.
 * Bugfix, logging, thanks to [Alexander](http://www.crawdaddy.com/)
= 0.4.3 ( 2010-11-12 ) =
 * The output format of the_related_for_string() changed
 * Renamed get_the_scores_for_string() to get_score_for_string()
 * Bugfix, escape terms before db queries, thanks to [Alexander](http://www.crawdaddy.com/)
 * Bugfix, really create fulltext indexes on activation
= 0.4.2.5 ( 2010-11-09 ) =
 * Really fix the bug for posts related to a string
= 0.4.2.3 ( 2010-11-09 ) =
 * Bugfix for string search
 * Add mysqld docs
= 0.4.2.1 ( 2010-11-06 ) =
 * Add template tags to search for posts related to a string.
 * Fix config data loss bugs on plugin activation with booleans and strings.
 * Allow floats as method weights in the admin interface and sanitize booleans.
= 0.4.1.2 ( 2010-11-05 ) =
 * Preserve old options on activation
 * Turn score info off by default
 * Fix network-wide activation
= 0.4 ( 2010-11-01 ) =
 * A few small fixes, thanks to [mrmist](http://www.misthaven.org.uk/blog/)
 * First public release
= 0.3.8 ( 2010-10-30 ) =
 * Sanitize options
 * Misc bugfixes, improvements and cleanups
= 0.3.7 ( 2010-10-29 ) =
 * Update documentation mostly
= 0.3.6 ( 2010-10-27 ) =
 * strip_tags() doesn't make sense unless we keep a filtered copy of the content in the db
 * Various bugfixes, improvements and code cleanup
= 0.3.5 ( 2010-10-26 ) =
 * Add score aging
 * Add incremental scoring
 * Various improvements and bug fixes
= 0.3.4 ( 2010-10-23 ) =
 * Add storing score as post meta, thanks to [Ozh](http://planetozh.com/) for the suggestion
= 0.3.3 ( 2010-10-20 ) =
 * Fix bug with testing for fulltext indexes, and misc other updates
= 0.3.2 ( 2010-10-18 ) =
 * Create and test for fulltext indexes
 * Add analyze template tag
= 0.3 ( 2010-10-10 ) =
 * Different weight for each scoring option
 * Add query limit

== Upgrade Notice ==
= 0.4.3.3 ( 2010-11-18 ) =
 * Please upgrade immediately, this fixes a serious bug.
= 0.4.3 ( 2010-11-12 ) =
 * The output format of the_related_for_string() changed, update your theme if you use it!
 * Renamed get_the_scores_for_string() to get_score_for_string()
= 0.4.2 ( 2010-11-06 ) =
 * Add template tags to search for posts related to a string.
