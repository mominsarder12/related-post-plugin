<?php
/*
* Plugin Name:       Custom Related Post
* Plugin URI:        https://developer.wordpress.org/reference/functions/wp_enqueue_style/
* Description:       This plugin is help you to make custom related post to choose you
* Version:           1.0.0
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Momin Sarder 
* Author URI:        https://author.example.com/
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Update URI:        https://example.com/my-plugin/
* Text Domain:       textdomain-crp
* Domain Path:       /languages
*/
add_action('admin_enqueue_scripts', 'rp_admin_scripts');

function rp_admin_scripts($hook)
{
    if ($hook == 'toplevel_page_related_post_settings' || $hook = "related-post-settings_page_layout-settings") {
        // Enqueue your JavaScript file
        wp_enqueue_script('rp-admin-script', plugin_dir_url(__FILE__) . 'inc/js/plugin-admin.js', array(), '1.0.0', true);
        wp_enqueue_style('rp-admin-style', plugin_dir_url(__FILE__) . 'inc/css/plugin-admin.css', array(), '1.0.0', 'all');
    }
}




/**
 * option page for the plugin
 */
require_once('inc/options-page.php');

/**
 * plugin enqueue scripts 
 */
add_action('wp_enqueue_scripts', 'crp_enqueue_scripts');
function crp_enqueue_scripts()

{
    $dir = plugin_dir_url(__FILE__);
    wp_enqueue_style('crp_main_style', $dir . 'style.css', array(), '1.0.0', 'all');
}




add_filter('the_content', 'show_custom_related_post');


//plugin main functionality
function show_custom_related_post($default)
{

    $rt_category = get_the_terms(get_the_ID(), 'category');

    $cat_id = [];
    if (is_array($rt_category) || is_object($rt_category)) {
        foreach ($rt_category as $cat) {
            $cat_id[] = $cat->term_id;
        }
    }

    //nassercery variables
    $rp_section_title = get_option('rp_section_title', 'Related Post');

    $rp_post_type = get_option('rp_post_type');
    if ('auto' == $rp_post_type) {
        $rp_post_type = get_post_type(get_the_ID());
    }
    $rp_number_of_post = get_option('rp_number_of_post');
    $ignore_sticky_post = get_option('rp_ignore_sticky_post');

    if ($ignore_sticky_post == "1") {
        $ignore_sticky_post = true;
    } else {
        $ignore_sticky_post = false;
    }
    $rp_post_order = esc_attr(get_option('rp_post_order'));
    $rp_post_order_by = esc_attr(get_option('rp_post_order_by'));
    $rp_show_thumbnail = esc_attr(get_option('rp_show_thumbnail'));

    $rp_thumbnail_width = get_option('rp_thumbnail_width');
    //$rp_thumbnail_width = 350;
    $rp_thumbnail_height = get_option('rp_thumbnail_height');
    //$rp_thumbnail_height = 100;
    $rp_thumbnail =  get_the_post_thumbnail(get_the_ID(), array($rp_thumbnail_width,  $rp_thumbnail_height), array('class' => 'crp_thumbnail'));
    $rp_display_columns = get_option('rp_display_columns');




    $related_post_query = new WP_Query(
        array(
            'post_type' => "$rp_post_type",
            'category__in' => $cat_id,
            'posts_per_page' => "$rp_number_of_post", // Corrected from 'post_per_page' to 'posts_per_page'
            'post_status' => 'publish',
            'ignore_sticky_posts' => $ignore_sticky_post,
            'post__not_in' => array(get_the_ID()), // Added 'array' around get_the_ID()
            'orderby' => $rp_post_order_by, // You can change this to another property like 'title', 'modified', etc.
            'order' => $rp_post_order, // 'ASC' for ascending order, 'DESC' for descending order
        )
    );

    if (is_single()) {
        $default .= '<h4 class="crp_section_title">' . esc_html__($rp_section_title, 'textdomain-crp') . '</h4>';
        $default .= '<div class="crp_main">';

        while ($related_post_query->have_posts()) :
            $related_post_query->the_post();
            $default .= '<div class="crp_single_area rp-col rp-col-'.$rp_display_columns.'"><div class="crp_item_wrapper">';
            if ($rp_show_thumbnail == 1) {
                $default .= '<div>' . $rp_thumbnail . '</div>';
            }
            $default .= '<div class="crp_post_title"><a href="' . esc_url(get_the_permalink()) . '">' . esc_html(get_the_title(), 'textdomain-crp') . '</a></div>';
            $default .= '</div></div>';

        endwhile;

        $default .= '</div>';
        $default .= "Section title = $rp_section_title" . "<br>";
        $default .= "post types = $rp_post_type" . "<br>";
        $default .= "ignore sticky posts =" . $ignore_sticky_post . "<br>";
        $default .= "number of post to display =" . $rp_number_of_post . "<br>";
        $default .= "order of post =" . $rp_post_order . "<br>";
        $default .= "Order by =" . $rp_post_order_by . "<br>";
        $default .= "Show Thumbnail =" . $rp_show_thumbnail . "<br>";
        $default .= "thumbnail Width =" . $rp_thumbnail_width . "<br>";
        $default .= "thumbnail height =" . $rp_thumbnail_height . "<br>";
        $default .= "column to display =" . $rp_display_columns . "<br>";

        return $default;
    }
    wp_reset_query();
}



function register_movie_post_type()
{
    $labels = array(
        'name'               => _x('Movies', 'post type general name', 'textdomain-crp'),
        'singular_name'      => _x('Movie', 'post type singular name', 'textdomain-crp'),
        'menu_name'          => _x('Movies', 'admin menu', 'textdomain-crp'),
        'name_admin_bar'     => _x('Movie', 'add new on admin bar', 'textdomain-crp'),
        'add_new'            => _x('Add New', 'movie', 'textdomain-crp'),
        'add_new_item'       => __('Add New Movie', 'textdomain-crp'),
        'new_item'           => __('New Movie', 'textdomain-crp'),
        'edit_item'          => __('Edit Movie', 'textdomain-crp'),
        'view_item'          => __('View Movie', 'textdomain-crp'),
        'all_items'          => __('All Movies', 'textdomain-crp'),
        'search_items'       => __('Search Movies', 'textdomain-crp'),
        'parent_item_colon'  => __('Parent Movies:', 'textdomain-crp'),
        'not_found'          => __('No movies found.', 'textdomain-crp'),
        'not_found_in_trash' => __('No movies found in Trash.', 'textdomain-crp'),
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.', 'textdomain-crp'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'movie'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
    );

    register_post_type('movie', $args);
}

add_action('init', 'register_movie_post_type');
