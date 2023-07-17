<?php
/**
 * @since             1.0.0
 * @package           Woof_Filter_Policy
 *
 * @wordpress-plugin
 * Plugin Name:       Woof filter policy
 * Plugin URI:        https://http://sintoms.techin.by/plugins/woof_filter_policy/
 * Description:       Расширение плагина WooCommerce Product Filter (WOOF) для вывода набора фильтров и параметров в зависимости от текущей страницы категорий.
 * Version:           1.0.0
 * Author:            Techin
 * Author URI:        https://techin.by/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woof-filter-policy
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Update it as you release new versions.
 */
define('WOOF_FILTER_POLICY_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woof-filter-policy-activator.php
 */
add_filter('option_woof_settings', 'ft_option_woof_settings');

function ft_option_woof_settings($value)
{
    $results = get_woof_results();

    $category_filters = [];

    $allowed_params = [];

    if (is_array($results) && !empty($results)) {
        foreach ($results as $result) {
            $category_id = $result->category_id;
            $category_slug = $result->category_slug;
            $filter_name = $result->filter_name;
            $param_id = $result->param_id;

            if (!empty($category_slug) && !empty($filter_name)) {
                if (!isset($category_filters[$category_slug])) {
                    $category_filters[$category_slug] = array();
                }
                $category_filters[$category_slug][] = $filter_name;

                $allowed_params[$filter_name][] = $param_id;
            }
        }
    }

    $cats = get_terms('product_cat', array('fields' => 'slugs'));

    foreach ($value['tax'] as $filter => $filter_data) {
        $is_allowed = false;

        foreach ($category_filters as $category_slug => $filters) {
            if (get_queried_object()->post_name === $category_slug && in_array($category_slug, $cats) && in_array($filter, $filters)) {
                $is_allowed = true;
                break;
            }
        }

        if (!$is_allowed) {
            unset($value['tax'][$filter]);
        }
    }

    return $value;
}

add_filter('woof_terms_filter', 'custom_woof_terms_filter', 10, 2);
function custom_woof_terms_filter($terms, $current_category_slug) {
    $results = get_woof_results();

    $filtered_terms = [];
    foreach ($terms as $term) {
        $term_id = $term['term_id'];

        // Проверяем наличие параметра с id в $results
        $exists = false;
        foreach ($results as $result) {

            if ($result->param_id == $term_id && $current_category_slug === $result->category_slug) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            $filtered_terms[] = $term;
        }
    }

    return $filtered_terms;
}

function debug($value) {
    echo '<pre>' . print_r($value, 1) . '</pre>';
    die();
}

function get_woof_results() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'woof_allowed_filter';
    $table2_name = $wpdb->prefix . 'woof_allowed_filter_fparams';

    $results = $wpdb->get_results("
        SELECT
            f.category_id,
            f.filter_id,
            t.slug AS category_slug,
            CONCAT('pa_', a.attribute_name) AS filter_name,
            p.param_id
        FROM
            $table_name AS f
        INNER JOIN
            {$wpdb->prefix}terms AS t ON t.term_id = f.category_id
        INNER JOIN
            {$wpdb->prefix}woocommerce_attribute_taxonomies AS a ON a.attribute_id = f.filter_id
        INNER JOIN
            $table2_name AS p ON p.fpolicy_id = f.ID
    ");

    return $results;
}

function activate_woof_filter_policy()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-woof-filter-policy-activator.php';
    Woof_Filter_Policy_Activator::activate();
    create_plugin_tables();

    global $wpdb;
    $table1_name = $wpdb->prefix . 'woof_allowed_filter';
    $table2_name = $wpdb->prefix . 'woof_allowed_filter_fparams';

    $filterData = array(
        array(
            'category_id' => 69,
            'filter_id' => 5,
            'params' => array(181, 182),
        ),
        array(
            'category_id' => 69,
            'filter_id' => 9,
            'params' => array(168, 268),
        ),
        array(
            'category_id' => 70,
            'filter_id' => 9,
            'params' => array(170),
        ),
        array(
            'category_id' => 70,
            'filter_id' => 11,
            'params' => array(202, 203),
        ),
        array(
            'category_id' => 71,
            'filter_id' => 5,
            'params' => array(177, 178, 181),
        ),
        array(
            'category_id' => 71,
            'filter_id' => 39,
            'params' => array(1764, 1758),
        ),
        array(
            'category_id' => 71,
            'filter_id' => 40,
            'params' => array(1766, 1767),
        ),
    );

    foreach ($filterData as $filter) {
        $wpdb->insert($table1_name, array(
            'category_id' => $filter['category_id'],
            'filter_id' => $filter['filter_id'],
        ));

        $fpolicy_id = $wpdb->insert_id;

        foreach ($filter['params'] as $param_id) {
            $wpdb->insert($table2_name, array(
                'fpolicy_id' => $fpolicy_id,
                'param_id' => $param_id,
            ));
        }
    }

}

function deactivate_woof_filter_policy()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-woof-filter-policy-deactivator.php';
    Woof_Filter_Policy_Deactivator::deactivate();
    delete_plugin_tables();
    remove_filter('option_woof_settings', 'ft_option_woof_settings');
}

// Функция для создания таблиц
function create_plugin_tables()
{
    global $wpdb;
    $table1_name = $wpdb->prefix . 'woof_allowed_filter';
    $table2_name = $wpdb->prefix . 'woof_allowed_filter_fparams';

    // Создаем первую таблицу
    $table1_sql = "CREATE TABLE IF NOT EXISTS $table1_name (
        ID INT(11) NOT NULL AUTO_INCREMENT,
        category_id INT(11) NOT NULL,
        filter_id INT(11) NOT NULL,
        PRIMARY KEY (ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $wpdb->query($table1_sql);

    // Создаем вторую таблицу с внешним ключом
    $table2_sql = "CREATE TABLE IF NOT EXISTS $table2_name (
        ID INT(11) NOT NULL AUTO_INCREMENT,
        fpolicy_id INT(11) NOT NULL,
        param_id INT(11) NOT NULL,
        PRIMARY KEY (ID),
        CONSTRAINT fk_woof_filter_policy FOREIGN KEY (fpolicy_id) REFERENCES $table1_name(ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $wpdb->query($table2_sql);
}

// Функция для удаления таблиц
function delete_plugin_tables()
{
    global $wpdb;
    $table1_name = $wpdb->prefix . 'woof_allowed_filter';
    $table2_name = $wpdb->prefix . 'woof_allowed_filter_fparams';

    // Удаляем вторую таблицу
    $table2_sql = "DROP TABLE $table2_name;";
    $wpdb->query($table2_sql);

    // Удаляем первую таблицу
    $table1_sql = "DROP TABLE $table1_name;";
    $wpdb->query($table1_sql);
}

register_activation_hook(__FILE__, 'activate_woof_filter_policy');
register_deactivation_hook(__FILE__, 'deactivate_woof_filter_policy');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-woof-filter-policy.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woof_filter_policy()
{
    $plugin = new Woof_Filter_Policy();
    $plugin->run();
}

run_woof_filter_policy();
