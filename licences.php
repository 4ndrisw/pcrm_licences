<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: licences
Description: Default module for defining licences
Version: 1.0.1
Requires at least: 2.3.*
*/

define('LICENCES_MODULE_NAME', 'licences');
define('LICENCE_ATTACHMENTS_FOLDER', 'uploads/licences/');

hooks()->add_filter('before_licence_updated', '_format_data_licence_feature');
hooks()->add_filter('before_licence_added', '_format_data_licence_feature');

//hooks()->add_action('after_cron_run', 'licences_notification');
hooks()->add_action('admin_init', 'licences_module_init_menu_items');
hooks()->add_action('admin_init', 'licences_permissions');
hooks()->add_action('clients_init', 'licences_clients_area_menu_items');

hooks()->add_action('staff_member_deleted', 'licences_staff_member_deleted');

hooks()->add_action('after_licence_updated', 'licence_create_assigned_qrcode');

hooks()->add_filter('migration_tables_to_replace_old_links', 'licences_migration_tables_to_replace_old_links');
hooks()->add_filter('global_search_result_query', 'licences_global_search_result_query', 10, 3);
hooks()->add_filter('global_search_result_output', 'licences_global_search_result_output', 10, 2);
hooks()->add_filter('get_dashboard_widgets', 'licences_add_dashboard_widget');
hooks()->add_filter('module_licences_action_links', 'module_licences_action_links');

hooks()->add_action('after_licence_added', 'add_licence_items');
hooks()->add_action('after_licence_copied', 'add_licence_items');

function licences_add_dashboard_widget($widgets)
{
    $widgets[] = [
        'path'      => 'licences/widgets/licence_proposed_this_week',
        'container' => 'left-8',
    ];
    $widgets[] = [
        'path'      => 'licences/widgets/completed_tasks_no_licence',
        'container' => 'left-8',
    ];

    return $widgets;
}


function licences_staff_member_deleted($data)
{
    $CI = &get_instance();
    $CI->db->where('staff_id', $data['id']);
    $CI->db->update(db_prefix() . 'licences', [
            'staff_id' => $data['transfer_data_to'],
        ]);
}

function licences_global_search_result_output($output, $data)
{
    if ($data['type'] == 'licences') {
        $output = '<a href="' . admin_url('licences/release/' . $data['result']['id']) . '">' . format_licence_number($data['result']['id']) . '</a>';
    }

    return $output;
}

function licences_global_search_result_query($result, $q, $limit)
{
    $CI = &get_instance();
    if (has_permission('licences', '', 'view')) {

        // licences
        $CI->db->select()
           ->from(db_prefix() . 'licences')
           ->like(db_prefix() . 'licences.formatted_number', $q)->limit($limit);
        
        $result[] = [
                'result'         => $CI->db->get()->result_array(),
                'type'           => 'licences',
                'search_heading' => _l('licences'),
            ];
        
        if(isset($result[0]['result'][0]['id'])){
            return $result;
        }

        // licences
        $CI->db->select()->from(db_prefix() . 'licences')->like(db_prefix() . 'clients.company', $q)->or_like(db_prefix() . 'licences.formatted_number', $q)->limit($limit);
        $CI->db->join(db_prefix() . 'clients',db_prefix() . 'licences.clientid='.db_prefix() .'clients.userid', 'left');
        $CI->db->order_by(db_prefix() . 'clients.company', 'ASC');

        $result[] = [
                'result'         => $CI->db->get()->result_array(),
                'type'           => 'licences',
                'search_heading' => _l('licences'),
            ];
    }

    return $result;
}

function licences_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
                'table' => db_prefix() . 'licences',
                'field' => 'description',
            ];

    return $tables;
}

function licences_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('licences', $capabilities, _l('licences'));
}


/**
* Register activation module hook
*/
register_activation_hook(LICENCES_MODULE_NAME, 'licences_module_activation_hook');

function licences_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register deactivation module hook
*/
register_deactivation_hook(LICENCES_MODULE_NAME, 'licences_module_deactivation_hook');

function licences_module_deactivation_hook()
{

     log_activity( 'Hello, world! . licences_module_deactivation_hook ' );
}

//hooks()->add_action('deactivate_' . $module . '_module', $function);

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(LICENCES_MODULE_NAME, [LICENCES_MODULE_NAME]);

/**
 * Init licences module menu items in setup in admin_init hook
 * @return null
 */
function licences_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app->add_quick_actions_link([
            'name'       => _l('licence'),
            'url'        => 'licences',
            'permission' => 'licences',
            'position'   => 57,
            ]);

    if (has_permission('licences', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('licences', [
                'slug'     => 'licences-tracking',
                'name'     => _l('licences'),
                'icon'     => 'fa fa-bookmark',
                'href'     => admin_url('licences'),
                'position' => 13,
        ]);
    }
    if (has_permission('licences', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('licences', [
                'slug'     => 'licences-tracking-table',
                'name'     => _l('licences'),
                'icon'     => 'fa fa-bookmark-o',
                'href'     => admin_url('licences'),
                'position' => 13,
        ]);
    }
}

function module_licences_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('settings?group=licences') . '">' . _l('settings') . '</a>';

    return $actions;
}

function licences_clients_area_menu_items()
{   
    // Show menu item only if client is logged in
    if (is_client_logged_in()) {
        add_theme_menu_item('licences', [
                    'name'     => _l('licences'),
                    'href'     => site_url('licences/list'),
                    'position' => 15,
        ]);
    }
}

/**
 * [perfex_dark_theme_settings_tab net menu item in setup->settings]
 * @return void
 */
function licences_settings_tab()
{
    $CI = &get_instance();
    $CI->app_tabs->add_settings_tab('licences', [
        'name'     => _l('settings_group_licences'),
        'view'     => 'licences/licences_settings',
        'position' => 51,
    ]);
}

$CI = &get_instance();
$CI->load->helper(LICENCES_MODULE_NAME . '/licences');

if(($CI->uri->segment(0)=='admin' && $CI->uri->segment(1)=='licences') || $CI->uri->segment(1)=='licences'){
    $CI->app_css->add(LICENCES_MODULE_NAME.'-css', base_url('modules/'.LICENCES_MODULE_NAME.'/assets/css/'.LICENCES_MODULE_NAME.'.css'));
    $CI->app_scripts->add(LICENCES_MODULE_NAME.'-js', base_url('modules/'.LICENCES_MODULE_NAME.'/assets/js/'.LICENCES_MODULE_NAME.'.js'));
}

