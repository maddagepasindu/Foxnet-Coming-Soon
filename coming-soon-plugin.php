<?php
/**
 * Plugin Name: Foxnet Coming Soon
 * Description: Selects a "Coming Soon" page to be displayed site-wide except for the admin area.
 * Version: 1.0
 * Author: Pasindu Perera
 * Author URI: https://foxnetdesigners.com
 */

// Plugin activation hook
register_activation_hook(__FILE__, 'coming_soon_plugin_activate');

function coming_soon_plugin_activate()
{
    // Set default options upon activation
    if (!get_option('coming_soon_page_id')) {
        update_option('coming_soon_page_id', 0);
    }
}

// Plugin settings page
add_action('admin_menu', 'coming_soon_plugin_add_admin_menu');
add_action('admin_init', 'coming_soon_plugin_settings_init');

function coming_soon_plugin_add_admin_menu()
{
    add_options_page('Coming Soon Plugin Settings', 'Coming Soon Plugin', 'manage_options', 'coming-soon-plugin', 'coming_soon_plugin_options_page');
}

function coming_soon_plugin_settings_init()
{
    register_setting('coming_soon_plugin_settings', 'coming_soon_page_id');
}

function coming_soon_plugin_options_page()
{
    if (isset($_POST['coming_soon_page_id'])) {
        update_option('coming_soon_page_id', $_POST['coming_soon_page_id']);
    }

    $coming_soon_page_id = get_option('coming_soon_page_id');
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish'
    );
    $pages = get_posts($args);
    ?>
    <div class="wrap">
        <h1>Coming Soon Plugin Settings</h1>

        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select "Coming Soon" page:</th>
                    <td>
                        <select name="coming_soon_page_id">
                            <option value="0">Select a page</option>
                            <?php foreach ($pages as $page) : ?>
                                <option value="<?php echo $page->ID; ?>" <?php selected($coming_soon_page_id, $page->ID); ?>><?php echo $page->post_title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function show_coming_soon_page() {
    if (!current_user_can('manage_options') && get_option('coming_soon_page_id')) {
        $coming_soon_page_id = get_option('coming_soon_page_id');
        if ($coming_soon_page_id && !is_page($coming_soon_page_id)) {
			
            // Hide the header and footer
            remove_all_actions('header');
            remove_all_actions('footer');
			
            // Redirect to the "Coming Soon" page
            wp_redirect(get_permalink($coming_soon_page_id));
            exit;
        }
    }
}

add_action('template_redirect', 'show_coming_soon_page');
