<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

require_once 'wp-content/themes/legion/class/SOAPRegistration.php';
require_once 'wp-content/themes/legion/class/SOAPDeletion.php';
require_once 'wp-content/themes/legion/class/SOAPChangePassword.php';
require_once 'wp-content/themes/legion/class/SOAPOnline.php';
require_once 'wp-content/themes/legion/class/parent_item.php';
require_once 'wp-content/themes/legion/class/item.php';
require_once 'wp-content/themes/legion/class/item_classes.php';
require_once 'wp-content/themes/legion/class/item_set.php';
require_once 'wp-content/themes/legion/class/item_home.php';
require_once 'wp-content/themes/legion/class/item_home_level.php';
require_once 'wp-content/themes/legion/class/item_home_gold.php';
require_once 'wp-content/themes/legion/class/item_home_character.php';
require_once 'wp-content/themes/legion/class/item_home_manage_character.php';
require_once 'wp-content/themes/legion/class/item_home_profession.php';

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'website');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'legionPassword2104');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1:3307');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '{s P@beTH|5#~p;zySNz zRQ11>?Jp,z}U.l09da,ZRX!,VV=S)a#f@qCFff9=4,');
define('SECURE_AUTH_KEY', 'H_c[]B5b^0K;Q{b,MXZMahD*6QN|(14]Lz2ionz{ltyUYNA2wiOYFX Z/-/PX4j3');
define('LOGGED_IN_KEY', '$6,!9zExcRv-WSlJA=w>M&z~JLl|([f[ZYb*0cE*ZJE8 265Q?%~j/Ybz;}FFj*F');
define('NONCE_KEY', 'agN#05fgz;v,}ivGo#}&w~an3x{##W!kAxYuMC#zu;QxfZ!PH;AudTv)bXHGy%)~');
define('AUTH_SALT', '{dw%k3b]O;AhJF,nk<XjG>D4XXqq/xT}^cwN;Ep6pu83jm0 G&^@Jr^eN3jn=b(k');
define('SECURE_AUTH_SALT', '%Xsxg2FJ@.3/j8zr`BJr<dF`}Kl]hr>oh9iMmJ~*{9~efRn%0`L#I2]?*RN##TSN');
define('LOGGED_IN_SALT', 'wBu2W$-+E$s*DpFk4xBaB&de8BqrWh#Y,!R851#?8HaCY9#d> *M@k>N9TPONnbo');
define('NONCE_SALT', 'p}-,.IbSWS+;v)Q/0)xD@&z;P*hwL;_ffNt4DA_i!%<2{Quv+}NWr,uV4e#s_LT5');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
