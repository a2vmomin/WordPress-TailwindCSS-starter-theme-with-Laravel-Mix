<?php
/*-----------------------------------------------------------------------------------*/
/* Wordpress header clean up
/*-----------------------------------------------------------------------------------*/
function header_clean_up()
{


    // CSS Links Changes
    function cleanup_qstring($src)
    {
        $parts = explode('?ver', $src);
        return $parts[0];
    }
    add_filter('script_loader_src', 'cleanup_qstring', 15, 1);
    add_filter('style_loader_src', 'cleanup_qstring', 15, 1);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    add_filter('show_admin_bar', '__return_false');

    function wpplugins_remove_recentcomments()
    {
        global $wp_widget_factory;
        remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
    }
    add_action('widgets_init', 'wpplugins_remove_recentcomments');

    add_filter('xmlrpc_enabled', function (): bool {
        return false;
    });

    /**
     * Remove unnecessary attributes from style tags
     */
    add_filter('style_loader_tag', function (string $tag, string $handle): string {
        // Remove ID attribute
        $tag = str_replace("id='${handle}-css'", '', $tag);

        // Remove type attribute
        $tag = str_replace(" type='text/css'", '', $tag);

        // Change ' to " in attributes:
        $tag = str_replace('\'', '"', $tag);

        // Remove trailing slash
        $tag = str_replace(' />', '>', $tag);

        // Remove double spaces
        return str_replace('  ', '', $tag);
    }, 10, 2);

    /**
     * Alter dns-prefetch links in <head>
     */
    add_filter('wp_resource_hints', function (array $urls, string $relation): array {
        // If the relation is different than dns-prefetch, leave the URLs intact
        if ($relation !== 'dns-prefetch') {
            return $urls;
        }

        // Remove s.w.org entry
        $urls = array_filter($urls, function (string $url): bool {
            return strpos($url, 's.w.org') === false;
        });

        // List of domains to prefetch:
        $dnsPrefetchUrls = [
            'fonts.googleapis.com', // Google fonts,
            'any.other.website.url.you.need...'
        ];
        // return array_merge($urls, $dnsPrefetchUrls);
        return $urls;
    }, 10, 2);

    // // Emojis
    // remove_action('wp_head', 'print_emoji_detection_script', 7);
    // remove_action('wp_print_styles', 'print_emoji_styles');


}
add_action('after_setup_theme', 'header_clean_up');


/*-----------------------------------------------------------------------------------*/
/* Disable the emoji's
/*-----------------------------------------------------------------------------------*/
function disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'disable_emojis');

/**/
function crunchify_print_scripts_styles()
{
    // Print all loaded Scripts
    global $wp_scripts;
    foreach ($wp_scripts->queue as $script) :
        echo $script . '  **  ';
    endforeach;

    // Print all loaded Styles (CSS)
    global $wp_styles;
    foreach ($wp_styles->queue as $style) :
        echo $style . '  ||  ';
    endforeach;
}

//add_action( 'wp_print_scripts', 'crunchify_print_scripts_styles' );
/**/

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param array $plugins 
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch($urls, $relation_type)
{
    if ('dns-prefetch' == $relation_type) {
        /** This filter is documented in wp-includes/formatting.php */
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

        $urls = array_diff($urls, array($emoji_svg_url));
    }

    return $urls;
}

/*-----------------------------------------------------------------------------------*/
/* Disable gutenberg style in Front
/*-----------------------------------------------------------------------------------*/
function wps_deregister_styles()
{
    // wp_dequeue_style('wp-block-library');
    // wp_dequeue_style('wpml-tm-admin-bar');
}
// add_action('wp_print_styles', 'wps_deregister_styles', 100);

function my_deregister_scripts()
{
    wp_dequeue_script('wp-embed');
}
add_action('wp_footer', 'my_deregister_scripts');
