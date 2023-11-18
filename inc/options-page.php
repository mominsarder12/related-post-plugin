<?php

/**
 * plugin option pages for this plugin
 */
function related_post_options_page()
{
    /*
    The main page for the plugin which is mandatory for this plugin
    */
    add_menu_page('Related Post', 'Related Post Settings', 'manage_options', 'related_post_settings', 'plugin_init', 'dashicons-admin-generic', 120);


    /*
    General Sub page for the pluging
    */
    add_submenu_page('related_post_settings', 'General Settings', 'General Settings', 'manage_options', 'related_post_settings', 'plugin_init');

    /**
     * The second submenu  page for advanced settings
     */
    add_submenu_page('related_post_settings', 'Layout Settings', 'Layout settings', 'manage_options', 'layout-settings', 'layout_functions');

    //activate custom settings
    add_action('admin_init', 'rp_custom_settings');
}
add_action('admin_menu', 'related_post_options_page');

function plugin_init()
{

    $plugins_dir = plugin_dir_path(__FILE__);
    include_once($plugins_dir . 'admin-template/general-option.php');
}


function rp_custom_settings()

{

    /**
     * setting for General section
     * example of add settings section
     */
    // add_settings_section('id','title','callback','page');
    add_settings_section('rp_general_section', 'General Options', 'general_section_callback', 'related_post_settings');

    //register settings
    register_setting('general_settings_group', 'rp_section_title');
    register_setting('general_settings_group', 'rp_post_type');
    register_setting('general_settings_group', 'rp_number_of_post');
    register_setting('general_settings_group', 'rp_ignore_sticky_post');
    register_setting('general_settings_group', 'rp_post_order');
    register_setting('general_settings_group', 'rp_post_order_by');

    //setting field
    // add_settings_field('id','title','callback','page','section','args');
    add_settings_field('rp_section_title_id', 'Section Title', 'rp_section_title_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_post_type_id', 'Post Types Selection', 'rp_post_type_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_number_of_post_id', 'Number of Related Posts', 'rp_number_of_post_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_ignore_sticky_post_id', 'Ignore Sticky Post', 'rp_ignore_sticky_post_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_post_order_id', 'Post Order', 'rp_post_order_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_post_order_by_id', 'Post Order By', 'rp_post_order_by_callback', 'related_post_settings', 'rp_general_section');


    /**
     * setting for Display section
     * example of add settings section
     */
    add_settings_section('rp_layout_section', 'layout Options', 'layout_section_callback', 'layout-settings');


    /**
     * register setting for Display section
     * example of add register settings
     */
    register_setting('layout_settings_group', 'rp_show_thumbnail');
    register_setting('layout_settings_group', 'rp_thumbnail_size');
    register_setting('layout_settings_group', 'rp_thumbnail_width');
    register_setting('layout_settings_group', 'rp_thumbnail_height');
    register_setting('layout_settings_group', 'rp_display_columns');

    /**
     * add settings field for Display section
     * example of add settings field
     */
    add_settings_field('rp_show_thumbnail_id', 'Display Thumbnail', 'rp_show_thumbnail_callback', 'layout-settings', 'rp_layout_section');
    add_settings_field('rp_thumbnail_size_id', 'Thumbnail Size', 'rp_thumbnail_size_callback', 'layout-settings', 'rp_layout_section');
    add_settings_field('rp_display_columns_id', 'Columns to Display', 'rp_display_columns_callback', 'layout-settings', 'rp_layout_section');
}

//section callback function
function general_section_callback()
{
    echo '<p>Choose where your related posts will be sourced from. Select the specific post types (e.g., posts, pages, custom post types) to tailor the related content to your preferences.</p>';
}

/**
 * adding fields
 */

//input html for section title
function rp_section_title_callback()
{
    $rp_section_title = get_option('rp_section_title');
    echo '<input type="text" name="rp_section_title" value="' . $rp_section_title . '">';
}
//input html tag for selection post type
function rp_post_type_callback()
{
    // Get registered post types, excluding built-in types
    $post_types = get_post_types(array('_builtin' => false), 'objects');

    // Add the built-in 'post' post type to the list
    $post_types['post'] = get_post_type_object('post');

    //post types match
    $rp_post_type = get_option('rp_post_type');

    // Output the select dropdown
    echo '<select name="rp_post_type" id="rpt_id" style="width:250px;">';

    // Loop through each custom post type and 'post' post type and create an option
    foreach ($post_types as $post_type => $post_type_object) {
        $selected = ($post_type === $rp_post_type) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($post_type) . '" ' . $selected . '>' . esc_html($post_type_object->labels->singular_name) . '</option>';
    }

    // Add the 'Auto Select' option
    $auto_selected = ('auto' === $rp_post_type) ? 'selected="selected"' : '';
    echo '<option value="auto" ' . $auto_selected . '>' . esc_html('Auto Select', 'textdomain-crp') . '</option>';
    echo '</select>';
}

//number of post field input
function rp_number_of_post_callback()
{
    $rp_number_of_post = esc_attr(get_option('rp_number_of_post')); // Set default value to 2
    echo '<input type="number" name="rp_number_of_post" value="' . esc_html($rp_number_of_post, 'textdomain-crp') . '">';
}

//ignore sticky pot filed input
function rp_ignore_sticky_post_callback()
{
    // Get the current option value
    $ignore_sticky_post = get_option('rp_ignore_sticky_post', false);

    // Output the checkbox
    echo '<label for="ignore-sticky"><input type="checkbox" id="rp_ignore_sticky_post" name="rp_ignore_sticky_post" value="1" ' . checked(1, $ignore_sticky_post, false) . ' />Ignore Sticky Post</label>';
}
//input field for post order 

function rp_post_order_callback()
{
    $rp_post_order = get_option('rp_post_order');
    echo '<select name="rp_post_order" style="width:250px;">';

    // Add the 'ASC' option
    $selected_asc = ($rp_post_order === 'ASC') ? 'selected="selected"' : '';
    echo '<option value="ASC" ' . $selected_asc . '>' . esc_html('ASC', 'textdomain-crp') . '</option>';

    // Add the 'DESC' option
    $selected_desc = ($rp_post_order === 'DESC') ? 'selected="selected"' : '';
    echo '<option value="DESC" ' . $selected_desc . '>' . esc_html('DESC', 'textdomain-crp') . '</option>';
    echo '</select>';
}

function rp_post_order_by_callback()
{
    $orderby_options = array(
        'date'           => esc_html__('Date', 'textdomain-crp'),
        'modified'           => esc_html__('Modified', 'textdomain-crp'),
        'title'          => esc_html__('Title', 'textdomain-crp'),
        'comment_count'  => esc_html__('Comment Count', 'textdomain-crp'),
        'rand'           => esc_html__('Random', 'textdomain-crp'),
        // Add more options as needed
    );

    // Get the current selected 
    $selected_option = esc_attr(get_option('rp_post_order_by'));

    // Output the dropdown
    echo '<select name="rp_post_order_by">';
    foreach ($orderby_options as $value => $label) {
        $selected = selected($value, $selected_option, false);
        echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
    }

    echo '</select>';
}


/**
 * layout section start
 */

/**
 * advanced functions which is second page of the plugin settings
 */
function layout_functions()
{
    echo '<h1>Layout Settings</h1>';
    $plugins_dir = plugin_dir_path(__FILE__);
    include_once($plugins_dir . 'admin-template/layout-option.php');
}

function display_section_callback()
{
    echo '<p>Choose the layout, Were and how display of your Related Posts</p>';
}
function rp_show_thumbnail_callback()
{
    // Get the current option value
    $rp_show_thumbnail = get_option('rp_show_thumbnail');

    // Output the checkbox
    echo '<label for="show_thumbnail"><input type="checkbox" id="rp_show_thumbnail" name="rp_show_thumbnail" value="1" ' . checked(1, $rp_show_thumbnail, false) . ' />Show Thumbnail</label>';
}
function layout_section_callback()
{
    echo '<p>choose your layout as you want</p>';
}

function rp_thumbnail_size_callback()
{
    // Get the current option value
    //$rp_thumbnail_size = get_option('rp_thumbnail_size');
    $rp_thumbnail_width = get_option('rp_thumbnail_width');
    $rp_thumbnail_height = get_option('rp_thumbnail_height');

    // Output the checkbox
    echo '<div class="block-div"><label for="rp_thumbnail_width" class="label-block">Thumbnail Width</label><input type="number" id="rp_thumbnail_width" name="rp_thumbnail_width" value="' . $rp_thumbnail_width . '"><p class="rp_admin_description">the input field takes the value in PX</p></div><div class="block-div"><label for="rp_thumbnail_height" class="label-block">Thumbnail Height</label><input type="number" id="rp_thumbnail_height" name="rp_thumbnail_height" value="' . $rp_thumbnail_height . '"><p class="rp_admin_description">the input field takes the value in PX</p></div>';
}

function rp_display_columns_callback()
{
    $rp_display_columns = get_option('rp_display_columns');
    echo '<select name="rp_display_columns" style="width:250px;">';

    for ($columns = 1; $columns <= 4; $columns++) {
        $selected_option = ($rp_display_columns == $columns) ? 'selected="selected"' : '';
        echo '<option value="' . $columns . '" ' . $selected_option . '>' . $columns . ' Column' . ($columns > 1 ? 's' : '') . '</option>';
    }

    echo '</select>';
}