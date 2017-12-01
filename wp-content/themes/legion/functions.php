<?php
/*
 *  Author: Todd Motto | @toddmotto
 *  URL: html5blank.com | @html5blank
 *  Custom functions, support, custom post types and more.
 */

if (session_id() == '')
    session_start();

/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

$GLOBALS["shop_page_id"] = 0;

$pages = get_pages(array(
    'meta_value' => 'shop.php'
));
foreach ($pages as $page) {
    $GLOBALS["shop_page_id"] = $page->ID;
}

if (!isset($_SESSION["shop"]) OR $_SESSION["shop"] == null) {
    $_SESSION["shop"] = new shop();
}

require_once 'class/wp_bootstrap_navwalker.php';

// Database settings
define('DB_NAME_SOAP', 'auth');

define('API_KEY', 'x3bqkbc8n7e7hcmnkcq9p2cpkzb364rg');

// Soap settings
define('SOAP_IP', '127.0.0.1');
define('SOAP_PORT', '7878');
define('SOAP_USER', '12#1');
define('SOAP_PASS', 'Computer210496,');

$dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;
$user = DB_USER;
$password = DB_PASSWORD;

if (!isset($GLOBALS["dbh"]) OR $GLOBALS["dbh"] == null) {
    try {
        $GLOBALS["dbh"] = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        $GLOBALS["dbh"] = null;
        throw new Exception($e->getMessage());
    }
}

$req = $GLOBALS["dbh"]->query('SELECT * FROM `static_data_shop`');
while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
    define('RATIO_GOLD', $data["gold_amount"] / $data["real_money_amount"]);
    define('BUY_POINTS', $data["buy_points"] / $data["real_money_amount"]);
    define('VOTE_POINTS', $data["vote_points"] / $data["real_money_amount"]);
}

if (serverOnline()) {
    $req = $GLOBALS["dbh"]->query('SELECT * FROM `user_update_offline` ORDER BY id ASC');
    while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
        if ($data["add_user"] == 1) {
            wow_insert_user($data["user_id"], true);
        }
        if ($data["delete_user"] == 1) {
            wow_delete_user($data["user_id"], json_decode($data["value"]));
        }
        if ($data["update_user"] == 1) {
            wow_update_user($data["user_id"], null, true, json_decode($data["value"]), $idToDelete = $data["id"]);
        }
    }
}

if (!isset($content_width)) {
    $content_width = 900;
}

if (function_exists('add_theme_support')) {
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    load_theme_textdomain('html5blank', get_template_directory() . '/languages');
}

/*------------------------------------*\
	Functions
\*------------------------------------*/

// HTML5 Blank navigation
function header_left_nav($phone = false)
{
    if ($phone == true) {
        wp_nav_menu(
            array(
                'theme_location' => 'header-menu-left',
                'menu' => '',
                'container' => 'div',
                'container_class' => 'menu-{menu slug}-container',
                'container_id' => '',
                'menu_class' => 'menu',
                'menu_id' => '',
                'echo' => true,
                'fallback_cb' => 'wp_page_menu',
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => '',
                'items_wrap' => '<ul class="header-nav header-nav-left">%3$s</ul>',
                'depth' => 2,
                'walker' => new WP_Bootstrap_Navwalker()

            )
        );
    } else {
        wp_nav_menu(
            array(
                'theme_location' => 'header-menu-left',
                'menu' => '',
                'container' => 'div',
                'container_class' => 'menu-{menu slug}-container',
                'container_id' => '',
                'menu_class' => 'menu',
                'menu_id' => '',
                'echo' => true,
                'fallback_cb' => 'wp_page_menu',
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => '',
                'items_wrap' => '<ul class="nav navbar-nav">%3$s</ul>',
                'depth' => 2,
                'walker' => new wp_bootstrap_navwalker()

            )
        );
    }
}

function header_right_nav()
{
    wp_nav_menu(
        array(
            'theme_location' => 'header-menu-right',
            'menu' => '',
            'container' => 'div',
            'container_class' => 'menu-{menu slug}-container',
            'container_id' => '',
            'menu_class' => 'menu',
            'menu_id' => '',
            'echo' => true,
            'fallback_cb' => 'wp_page_menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul class="nav navbar-nav navbar-right">%3$s</ul>',
            'depth' => 0,
            'walker' => new WP_Bootstrap_Navwalker()
        )
    );
}

function footer_nav()
{
    wp_nav_menu(
        array(
            'theme_location' => 'footer',
            'menu' => '',
            'container' => 'div',
            'container_class' => 'menu-{menu slug}-container',
            'container_id' => '',
            'menu_class' => 'menu',
            'menu_id' => '',
            'echo' => true,
            'fallback_cb' => 'wp_page_menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul class="overGreen">%3$s</ul>',
            'depth' => 0,
            'walker' => new WP_Bootstrap_Navwalker()
        )
    );
}

// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

        wp_register_script('conditionizr', get_template_directory_uri() . '/js/lib/conditionizr-4.3.0.min.js', array(), '4.3.0'); // Conditionizr
        wp_enqueue_script('conditionizr'); // Enqueue it!

        wp_register_script('modernizr', get_template_directory_uri() . '/js/lib/modernizr-2.7.1.min.js', array(), '2.7.1'); // Modernizr
        wp_enqueue_script('modernizr'); // Enqueue it!

        wp_register_script('html5blankscripts', get_template_directory_uri() . '/js/scripts.js', array('jquery'), '1.0.0', true); // Custom scripts
        wp_enqueue_script('html5blankscripts'); // Enqueue it!
    }
}

function points_extra_fields($user)
{ ?>

    <h3>Points Informations</h3>

    <table class="form-table">
        <tr>
            <th><label for="vote_points">Vote</label></th>
            <td>
                <input type="number" name="vote_points" id="vote_points"
                       value="<?php echo esc_attr(get_the_author_meta('vote_points', $user->ID)); ?>"
                       class="regular-text"/><br/>
                <span class="description">Amount of vote point</span>
            </td>
        </tr>
        <tr>
            <th><label for="buy_points">Buy</label></th>
            <td>
                <input type="number" name="buy_points" id="buy_points"
                       value="<?php echo esc_attr(get_the_author_meta('buy_points', $user->ID)); ?>"
                       class="regular-text"/><br/>
                <span class="description">Amount of buy point</span>
            </td>
        </tr>
    </table>
<?php }

function my_save_extra_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'vote_points', $_POST['vote_points']);
    update_user_meta($user_id, 'buy_points', $_POST['buy_points']);
}

// Load HTML5 Blank conditional scripts
function html5blank_conditional_scripts()
{
    if (is_page('pagenamehere')) {
        wp_register_script('scriptname', get_template_directory_uri() . '/js/scriptname.js', array('jquery'), '1.0.0'); // Conditional script(s)
        wp_enqueue_script('scriptname'); // Enqueue it!
    }
}

// Load HTML5 Blank styles
function html5blank_styles()
{
    wp_register_style('normalize', get_template_directory_uri() . '/normalize.css', array(), '1.0', 'all');
    wp_enqueue_style('normalize'); // Enqueue it!

    wp_register_style('html5blank', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
    wp_enqueue_style('html5blank'); // Enqueue it!
}

// Register HTML5 Blank Navigation
function register_html5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu-left' => __('Header Menu Left', 'html5blank'), // Main Navigation
        'header-menu-right' => __('Header Menu Right', 'html5blank'), // Main Navigation
        'footer' => __('Footer', 'html5blank') // Footer Navigation
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar')) {
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget Area 2', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions($html)
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function html5blankcomments($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);

    if ('div' == $args['style']) {
        $tag = 'div';
        $add_below = 'comment';
    } else {
        $tag = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?><?php comment_class(empty($args['has_children']) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
    <?php if ('div' != $args['style']) : ?>
    <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
<?php endif; ?>
    <div class="comment-author vcard">
        <?php if ($args['avatar_size'] != 0) echo get_avatar($comment, $args['180']); ?>
        <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
    </div>
    <?php if ($comment->comment_approved == '0') : ?>
    <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
    <br/>
<?php endif; ?>

    <div class="comment-meta commentmetadata"><a
                href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>">
            <?php
            printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'), '  ', '');
        ?>
    </div>

    <?php comment_text() ?>

    <div class="reply">
        <?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </div>
    <?php if ('div' != $args['style']) : ?>
    </div>
<?php endif; ?>
<?php }

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'html5blank_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'html5blank_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination
add_action('show_user_profile', 'points_extra_fields');
add_action('edit_user_profile', 'points_extra_fields');
add_action('personal_options_update', 'my_save_extra_profile_fields');
add_action('edit_user_profile_update', 'my_save_extra_profile_fields');
add_action('user_register', 'wow_insert_user');//user create account => create user in wow game
add_action('delete_user', 'wow_delete_user');//user delete account => delete user in wow game
add_action('profile_update', 'wow_update_user', 10, 2);//user update account => update user in wow game

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'html5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode('html5_shortcode_demo', 'html5_shortcode_demo'); // You can place [html5_shortcode_demo] in Pages, Posts now.
add_shortcode('html5_shortcode_demo_2', 'html5_shortcode_demo_2'); // Place [html5_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [html5_shortcode_demo] [html5_shortcode_demo_2] Here's the page title! [/html5_shortcode_demo_2] [/html5_shortcode_demo]

/*------------------------------------*\
	ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function html5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function html5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}

function getUserWowIdWithUserData($userdata)
{
    $user = (array)$userdata;
    $data = (array)$user['data'];
    $user_email = $data["user_email"];
    $account = null;
    if (isset($GLOBALS['dbh'])) {
        $sth = $GLOBALS['dbh']->query("SELECT * FROM auth.account WHERE email='" . $user_email . "'");
        while ($user = $sth->fetch(PDO::FETCH_ASSOC)) {
            $account["username"] = $user["username"];
            $account["id"] = $user["id"];
        }
    }
    return $account;
}

function serverOnline()
{
    $serverStatus = new SOAPOnline();
    $serverStatus->isOnline();
    return $serverStatus->isOnline();
}

function wow_insert_user($user_id, $update_because_server_was_offline = false)
{
    if ($update_because_server_was_offline == false) {//the server wasn't offline when created this user
        if (isset($_POST['user_email'])) {
            $mail = $_POST['user_email'];
        } elseif (isset($_POST["email"])) {
            $mail = $_POST['email'];
        } else {
            $mail = null;
        }
        if (isset($_POST["username"])) {
            $username = $_POST["username"];
        } elseif (isset($_POST["user_login"])) {
            $username = $_POST["user_login"];
        } else {
            $username = null;
        }
        if (isset($_POST["password"])) {
            $password = $_POST["password"];
        } elseif (isset($_POST["pass1-text"])) {
            $password = $_POST["pass1-text"];
        } else {
            $password = null;
        }
    } else {// the server was offline when created this user
        $mail = null;
        $password = null;
        $username = null;
        $req = $GLOBALS["dbh"]->query('SELECT * FROM `user_update_offline` WHERE `user_id`=' . $user_id . ' AND `add_user`=1');
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $value = json_decode($data["value"]);
            $mail = $value->mail;
            $password = $value->password;
            $username = $value->username;
        }
    }
    if (serverOnline()) {
        new SOAPRegistration($mail, $password);
        $id_account = 0;
        $mail = get_userdata($user_id)->user_email;
        $req = $GLOBALS["dbh"]->query("SELECT * FROM auth.battlenet_accounts WHERE `email`='" . strtoupper($mail) . "'");
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $id_account = $data["id"];
        }
        update_user_meta($user_id, 'account_id', $id_account);
    } elseif ($update_because_server_was_offline == false) {
        $value["mail"] = $mail;
        $value["password"] = $password;
        $value["username"] = $username;
        $value = json_encode($value);
        $GLOBALS["dbh"]->query("INSERT INTO `user_update_offline`(`user_id`, `add_user`,`value`) VALUES (" . $user_id . ",1,'" . $value . "')");
    }
    if ($update_because_server_was_offline == true) {
        $GLOBALS["dbh"]->query("DELETE FROM `user_update_offline` WHERE `user_id`=" . $user_id . " AND `add_user`=1");
    }
    update_user_meta($user_id, 'vote_points', 0);
    update_user_meta($user_id, 'buy_points', 0);
    update_user_meta($user_id, 'real_password', $password);
}

function wow_update_user($user_id, $old_user_data, $update_because_server_was_offline = false, $previousData = null, $idToDelete = null)
{
    $mail = null;
    $oldMail = null;
    $newPassword = null;
    if ($update_because_server_was_offline == false) {
        $mail = get_userdata($user_id)->data->user_email;
        $oldMail = $old_user_data->data->user_email;
        if (isset($_POST['password_repeat'])) {//account page
            $newPassword = $_POST['password_repeat'];
        } elseif (isset($_POST['pass1-text'])) {//wp admin panel page
            $newPassword = $_POST['pass1-text'];
        } elseif (isset($_POST['password_2'])) {//reset password
            $newPassword = $_POST['password_2'];
        }
    } elseif ($previousData != null) {
        $mail = $previousData->mail;
        $oldMail = $previousData->oldMail;
        $newPassword = $previousData->newPassword;
    }
    if (serverOnline()) {
        if ($newPassword != null) {
            if (strlen($newPassword) > 16) {
                $old_password = get_user_meta($user_id, 'real_password')[0];
                wp_set_password($old_password, $user_id);
            } else {
                new SOAPChangePassword($mail, $newPassword);
                update_user_meta($user_id, 'real_password', $newPassword);
            }
        }
        if ($mail != $oldMail) {
            $GLOBALS["dbh"]->query("UPDATE auth.battlenet_accounts SET email='" . strtoupper($mail) . "' WHERE email='" . strtoupper($oldMail) . "'");
            new SOAPChangePassword($mail, get_user_meta($user_id, 'real_password')[0]);
        }
        if ($idToDelete != null) {
            $GLOBALS["dbh"]->query("DELETE FROM `user_update_offline` WHERE `id`=" . $idToDelete);
        }
    } else {
        $value["mail"] = $mail;
        $value["oldMail"] = $oldMail;
        $value["newPassword"] = $newPassword;
        $value = json_encode($value);
        $GLOBALS["dbh"]->query("INSERT INTO `user_update_offline`(`user_id`, `update_user`, `value`) VALUES (" . $user_id . ",1,'" . $value . "')");
    }
}

function wow_delete_user($user_id, $real_account = null)
{
    $account = getUserWowIdWithUserData(get_userdata($user_id));
    if ($real_account != null) {
        $account["username"] = $real_account->username;
        $account["id"] = $real_account->id;
    }
    if (serverOnline()) {
        if ($account != null) {
            new SOAPDeletion($account["username"]);
        }
        $tabAllTableBattlenet = ["battlenet_account_bans", "battlenet_account_heirlooms", "battlenet_account_mounts", "battlenet_account_toys", "battlenet_item_appearances", "battlenet_item_favorite_appearances", "battlenet_accounts"];
        foreach ($tabAllTableBattlenet as $table) {
            $req = "DELETE FROM auth." . $table . " WHERE ";
            if ($table == "battlenet_account_bans" OR $table == "battlenet_accounts") {
                $req = $req . "id=";
            } elseif ($table == "battlenet_account_heirlooms" OR $table == "battlenet_account_toys") {
                $req = $req . "accountId=";
            } elseif ($table == "battlenet_account_mounts" OR $table == "battlenet_item_appearances" OR $table == "battlenet_item_favorite_appearances") {
                $req = $req . "battlenetAccountId=";
            }
            $req = $req . $account["id"];
            $GLOBALS["dbh"]->query($req);
        }
        $GLOBALS["dbh"]->query("DELETE FROM `user_update_offline` WHERE `user_id`=" . $user_id . " AND `delete_user`=1");
    } else {
        $GLOBALS["dbh"]->query("INSERT INTO `user_update_offline`(`user_id`, `delete_user`, `value`) VALUES (" . $user_id . ",1,'" . json_encode($account) . "')");
    }
}

function getOnlinePlayer()
{
    $tab = array();
    $tab["blue"] = 0;
    $tab["red"] = 0;
    $tab["total"] = $tab["blue"] + $tab["red"];
    if ($tab["total"] == 0) {
        $tab["pBlue"] = 50;
        $tab["pRed"] = 50;
    }else{
        $tab["pBlue"] = intval(($tab["blue"] / $tab["total"]) * 100);
        $tab["pRed"] = 100 - $tab["pBlue"];
    }
    return $tab;
}

function getLadder()
{
    $tab = array();
    for ($i = 0; $i < 5; $i++) {
        $tab[$i]["name"] = "Name";
        $tab[$i]["win"] = "1000";
        $tab[$i]["losses"] = "10";
        $tab[$i]["ranking"] = "2009";
    }
    return $tab;
}

function getAllItemClasses()
{
    $sth = $GLOBALS['dbh']->query("SELECT * FROM website.item_classes ORDER BY name");
    $data = array();
    while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
        $result["subclasses"] = json_decode($result["subclasses"]);
        array_push($data, $result);
    }
    return $data;
}

function getAllItemSetClasses()
{
    $sth = $GLOBALS['dbh']->query("SELECT * FROM website.item_set ORDER BY id");
    $data = array();
    while ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
        array_push($data, json_decode($result["allowableClasses"])[0]);
    }
    return array_unique($data);
}

function isWowAdmin()
{
    if (is_super_admin(get_current_user_id())) {
        return true;
    }
    return false;
}

function formatNumber($number)
{
    return number_format($number, 0, ',', ' ');
}

?>
