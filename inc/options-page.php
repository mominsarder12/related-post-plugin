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
    add_submenu_page('related_post_settings', 'Advanced Settings', 'Advanced settings', 'manage_options', 'advanced-settings', 'advanced_functions');

    //activate custom settings
    add_action('admin_init', 'rp_custom_settings');
}
add_action('admin_menu', 'related_post_options_page');

function plugin_init()
{

    $plugins_dir = plugin_dir_path(__FILE__);
    include_once($plugins_dir . 'admin-template/plugin-option.php');
}
function rp_custom_settings()
{
    //settings section
    add_settings_section('rp_general_section', 'General Options', 'general_section_callback', 'related_post_settings');

    //register settings
    register_setting('general_settings_group', 'rp_section_title');
    register_setting('general_settings_group', 'rp_post_type');
    register_setting('general_settings_group', 'rp_number_of_post');
    register_setting('general_settings_group', 'rp_ignore_sticky_post');
    register_setting('general_settings_group', 'rp_post_order');
    register_setting('general_settings_group', 'rp_post_order_by');

    //setting field
    add_settings_field('rp_section_title_id', 'Section Title', 'rp_section_title_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_post_type_id', 'Post Types Selection', 'rp_post_type_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_number_of_post_id', 'Number of Related Posts', 'rp_number_of_post_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_ignore_sticky_post_id', 'Ignore Sticky Post', 'rp_ignore_sticky_post_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_post_order_id', 'Post Order', 'rp_post_order_callback', 'related_post_settings', 'rp_general_section');
    add_settings_field('rp_post_order_by_id', 'Post Order By', 'rp_post_order_by_callback', 'related_post_settings', 'rp_general_section');
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
    $rp_number_of_post = esc_attr(get_option('rp_number_of_post', 2)); // Set default value to 2
    echo '<input type="number" name="rp_number_of_post" value="' . esc_html($rp_number_of_post, 'textdomain-crp') . '">';
}

//ignore sticky pot filed input
function rp_ignore_sticky_post_callback()
{
    // Get the current option value
    $ignore_sticky_post = get_option('rp_ignore_sticky_post', false);

    // Output the checkbox
    echo '<input type="checkbox" id="rp_ignore_sticky_post" name="rp_ignore_sticky_post" value="1" ' . checked(1, $ignore_sticky_post, false) . ' />';
}
//input field for post order 

function rp_post_order_callback()
{
    $rp_post_order = esc_attr(get_option('rp_post_order'));

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

    // Get the current selected option
    //$selected_option = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'date';
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
 * advanced functions which is second page of the plugin settings
 */
function advanced_functions()
{
    echo '<h1>Advanced Settings</h1>';
}
