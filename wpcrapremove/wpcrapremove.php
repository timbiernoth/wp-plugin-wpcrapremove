<?php
/*
Plugin Name: wpcrapremove
Description: Removes some default settings for better security and performance.
Author: Tim Biernoth
Version: 0.1
*/

/**
 * Disable the emoji's
 */
function child_disable_emojis() {
 remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'wp_print_styles', 'print_emoji_styles' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' );
 remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
 remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
 remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
 add_filter( 'tiny_mce_plugins', 'child_disable_emojis_tinymce' );
 add_filter( 'wp_resource_hints', 'child_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'child_disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 */
function child_disable_emojis_tinymce( $plugins ) {
 if ( is_array( $plugins ) ) {
 return array_diff( $plugins, array( 'wpemoji' ) );
 } else {
 return array();
 }
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 */
function child_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
 if ( 'dns-prefetch' == $relation_type ) {
 /** This filter is documented in wp-includes/formatting.php */
 $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

$urls = array_diff( $urls, array( $emoji_svg_url ) );
 }

return $urls;
}

add_action('init', 'remheadlink');
function remheadlink() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'wp_shortlink_header', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
}

function disable_embeds_code_init() {
  // Remove the REST API endpoint.
  remove_action( 'rest_api_init', 'wp_oembed_register_route' );

  // Turn off oEmbed auto discovery.
  add_filter( 'embed_oembed_discover', '__return_false' );

  // Don't filter oEmbed results.
  remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

  // Remove oEmbed discovery links.
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

  // Remove oEmbed-specific JavaScript from the front-end and back-end.
  remove_action( 'wp_head', 'wp_oembed_add_host_js' );

  add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );

  // Remove all embeds rewrite rules.
  add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );

  // Remove filter of the oEmbed result before any HTTP requests are made.
  remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
}

add_action( 'init', 'disable_embeds_code_init', 9999 );

function disable_embeds_tiny_mce_plugin($plugins) {
  return array_diff( $plugins, array('wpembed') );
}

function disable_embeds_rewrites ($rules) {

foreach($rules as $rule => $rewrite) {

  if(false !== strpos($rewrite, 'embed=true')) {
    unset($rules[$rule]);
  }

}

return $rules;
}

add_filter( 'xmlrpc_enabled', '__return_false' );

// Alternatively use this to just remove the url or use it together with the above
add_filter('bloginfo_url', function($output, $property){
    return ($property == 'pingback_url') ? null : $output;
}, 11, 2);

// remove WP 4.9+ dns-prefetch nonsense
remove_action( 'wp_head', 'wp_resource_hints', 2 );

add_filter( 'pagelines_xfn', '__return_true' );
remove_action( 'wp_head',      'rest_output_link_wp_head'              );
remove_action( 'wp_head',      'wp_oembed_add_discovery_links'         );

add_filter('style_loader_tag', 'myplugin_remove_type_attr', 1, 2);
add_filter('script_loader_tag', 'myplugin_remove_type_attr', 1, 2);
function myplugin_remove_type_attr($tag, $handle) {
    return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
}

add_filter('rest_enabled', '_return_false');
add_filter('rest_jsonp_enabled', '_return_false');
