<?php
//ALLOW WP list table class
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

//Skye App settings pages
add_action("admin_menu", function () {
    add_menu_page(
        "Skye App",
        "Skye App",
        "manage_options",
        "skye-app",
        "skye_app_func",
        plugin_dir_url(__DIR__) . "assets/icons8_iphone_20px.png",
        60
    );
    // if (ENABLE_SLIDING_BANNER)
    // add_submenu_page( "skye-app", "App Banner Slides", "Banner Slides", "manage_options", "skye-app-banner-slides", "skye_app_banner_slides", 1);
    // if (ENABLE_SCROLLING_BANNER)
    //     add_submenu_page( "skye-app", "App Banner Scrolls", "Small Banner Scrolls", "manage_options", "skye-app-banner-scrolls", "skye_app_banner_scrolls", 2);


    add_submenu_page("skye-app", "Banners Slides", "Banner Slides", "manage_options", "skye-app-banner-slides-list", "skye_app_banner_slides_list", 1);
    add_submenu_page(null, "Edit Banner Slides", "Edit Banner Slides", "manage_options", "skye_edit_banner", "skye_app_banner_slides_edit", 2); //hidden for edit
    add_submenu_page("skye-app", "Delivery Drivers", "Delivery Drivers", "manage_options", "skye-app-delivery-drivers", "skye_app_delivery_drivers_func", 2);

});


function skye_app_func()
{
    include(plugin_dir_path(__FILE__) . 'pages/general.php');
}
function skye_app_banner_slides()
{
    include(plugin_dir_path(__FILE__) . 'pages/banner-slides.php');
}
function skye_app_banner_scrolls()
{
    include(plugin_dir_path(__FILE__) . 'pages/banner-scrolls.php');
}

function skye_app_banner_slides_list()
{
    include(plugin_dir_path(__FILE__) . 'pages/banners-slides-list.php');
}
function skye_app_banner_slides_edit()
{
    include(plugin_dir_path(__FILE__) . 'pages/edit-banner.php');
}
function skye_app_delivery_drivers_func()
{
    include(plugin_dir_path(__FILE__) . 'pages/delivery-drivers.php');
}




// Add Styles and Scripts to Settings page
add_action('admin_enqueue_scripts', 'skye_apps_scripts_func');
function skye_apps_scripts_func($hook)
{
    wp_enqueue_style("skye_app_css", plugin_dir_url(__DIR__) . "css/admin-style.css");
    wp_enqueue_script('skye_app_script', plugin_dir_url(__DIR__) . "js/admin-script.js");
}

function skye_app_validate_banner($item)
{
    $messages = array();

    if (empty($item['image'])) $messages[] = __('Image is required', 'cltd_example');
    if (empty($item['title'])) $messages[] = __('Title is required', 'cltd_example');
    // if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'cltd_example');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}
function skye_app_banners_form_meta_box_handler($item)
{
?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table" id="skye-app-banner-form">
        <tbody>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="image"><?php _e('Image', 'cltd_example') ?></label>
                </th>
                <td>

                    <input name="image" id="image" type="hidden" value="<?php echo esc_attr($item['image']) ?>" required>
                    <?php if ($item['id'] == 0 || empty($item['image'])) { ?>
                        <a href="javascript:void(0);" id="skye-app-change-image" style="cursor: pointer; display: inline-block;"></a>
                        <button type="button" id="skye-app-select-image" class="button">Select image</button>
                        <button type="button" id="skye-app-remove-image" class="button button-primary" style="display: none;">Remove image</button>
                    <?php } else { ?>
                        <?php
                        $banner_image = wp_get_attachment_image_src($item['image'], null);
                        if (!$banner_image)
                            $banner_image = plugin_dir_url(__DIR__) . "assets/icons8_image_500px.png";
                        ?>
                        <a href="#" id="skye-app-change-image" style="cursor: pointer; display: inline-block;"><img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="<?php echo  $banner_image[0]; ?>" /></a>
                        <button type="button" id="skye-app-remove-image" class="button button-primary">Remove image</button>
                        <button type="button" id="skye-app-select-image" class="button" style="display: none;">Select image</button>
                    <?php } ?>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="title"><?php _e('Title', 'cltd_example') ?></label>
                </th>
                <td>
                    <input id="title" name="title" type="text" style="width: 95%" value="<?php echo esc_attr($item['title']) ?>" size="50" class="code" placeholder="<?php _e('Title', 'cltd_example') ?>" required>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="description"><?php _e('Description', 'cltd_example') ?></label>
                </th>
                <td>
                    <textarea id="d" name="description" style="width: 95%" size="50" class="code" placeholder="<?php _e('Description', 'cltd_example') ?>" required><?php echo esc_attr($item['description']) ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

/**
 * Custom_Table_Example_List_Table class that will display our custom table
 * records in nice table
 */
class Skye_App_Banners_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'banner',
            'plural' => 'banners',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_id($item)
    {
        return '<em>' . $item['ID'] . '</em>';
    }
    function column_title($item)
    {
        return '<strong>' . $item['title'] . '</strong>';
    }
    function column_description($item)
    {
        return '<em>' . $item['description'] . '</em>';
    }
    function column_on_click_to($item)
    {
        return '<em>' . $item['on_click_to'] . '</em>';
    }
    function column_url($item)
    {
        return '<em>' . $item['url'] . '</em>';
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_image($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=skye_edit_banner&id=%s">%s</a>', $item['ID'], __('Edit', 'cltd_example')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['ID'], __('Delete', 'cltd_example')),
        );

        $banner_image = wp_get_attachment_image_src($item['image'], null);
        if (!$banner_image)
            $banner_image = plugin_dir_url(__DIR__) . "assets/icons8_image_500px.png";

        return sprintf(
            '%s %s',
            "<img src='" . $banner_image[0] . "' style='width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;'>",
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['ID']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'image' => __('Image', 'cltd_example'),
            'title' => __('Title', 'cltd_example'),
            'description' => __('Description', 'cltd_example'),
            // 'on_click_to' => __('On Click to', 'cltd_example'),
            // 'url' => __('URL', 'cltd_example'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'title' => array('title', true),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'skye_app_banners'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'skye_app_banners'; // do not forget about tables prefix

        $per_page = 10; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'ID';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}


//FOR DELIVERY API
add_filter('manage_edit-shop_order_columns', 'skye_add_driver_order_column_to_admin_table');

function skye_add_driver_order_column_to_admin_table($columns)
{
    $reordered_columns = array();
    foreach ($columns as $key => $column) {
        $reordered_columns[$key] = $column;
        if ($key ==  'order_status') {
            // Inserting after "Status" column
            $reordered_columns['skye_driver'] = __('Driver', 'skye_domain');
        }
    }
    return $reordered_columns;
}
add_action('manage_shop_order_posts_custom_column', 'skye_add_driver_order_column_to_admin_table_content');

function skye_add_driver_order_column_to_admin_table_content($column)
{

    global $post;

    if ('skye_driver' === $column) {

        $order = wc_get_order($post->ID);
        $driver_id = $order->get_meta("skye_order_driver");
        if ($driver_id) {
            $user = get_user_by('ID', $driver_id);
            if ($user) {
                echo "<a href='user-edit.php?user_id=" . $user->ID . "'><b>" . $user->display_name . "</b></a>";
            }
        }
    }
}

// Adding Meta container admin shop_order pages
add_action('add_meta_boxes', 'skye_add_driver_meta_boxes');
if (!function_exists('skye_add_driver_meta_boxes')) {
    function skye_add_driver_meta_boxes()
    {
        add_meta_box('skye_other_fields', __('Driver', 'woocommerce'), 'skye_add_driver_fields_for_packaging', 'shop_order', 'side', 'high');
    }
}

// Adding Meta field in the meta container admin shop_order pages
if (!function_exists('skye_add_driver_fields_for_packaging')) {
    function skye_add_driver_fields_for_packaging()
    {
        global $post;

        $meta_field_data = get_post_meta($post->ID, 'skye_order_driver', true) ? get_post_meta($post->ID, 'skye_order_driver', true) : '';
        $sk_drivers = get_users(array('role' => 'skye_delivery_driver'));
    ?>
        <input type="hidden" name="skye_order_meta_field_nonce" value="<?php echo wp_create_nonce(); ?>">

            <label>Driver</label> <br>
            <select name="skye_order_drivers_input" id="skye_order_drivers_input" style="width: 100%;">
                <option value="">Assign a Driver</option>
                <?php foreach($sk_drivers as $driver) { ?>
                    <option value="<?php echo $driver->ID; ?>" <?php echo ($driver->ID == $meta_field_data) ? "selected" : ""; ?>><?php echo $driver->display_name; ?></option>
                <?php } ?>
            </select>
        
<?php
    }
}

// Save the data of the Meta field
add_action('save_post', 'skye_save_wc_order_other_fields', 10, 1);
if (!function_exists('skye_save_wc_order_other_fields')) {

    function skye_save_wc_order_other_fields($post_id)
    {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if (!isset($_POST['skye_order_meta_field_nonce'])) {
            return $post_id;
        }
        $nonce = $_REQUEST['skye_order_meta_field_nonce'];

        //Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce)) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {

            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        // Sanitize user input  and update the meta field in the database.
        $driver_id = $_POST['skye_order_drivers_input'];
        if (empty($driver_id)) {
            delete_post_meta( $post_id, 'skye_order_driver');
        } else {
            update_post_meta($post_id, 'skye_order_driver', $driver_id);
            //notify driver
            $driver = get_user_by( 'ID', $driver_id);
            if ($driver) {
                if ($driver->user_email) {
                    $email = $driver->user_email;
                    $message = "<h3>You have new order to deliver</h3>
                    <h4>Order: #" . $post_id . "</h4>
                    <p>Open your delivery app to view details.</p>";
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    $subject = get_bloginfo( 'name') . ' - You have new order to deliver';
                    wp_mail( $email, $subject, $message, $headers);
                }
            }
        }
    }
}
