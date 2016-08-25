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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'danwa');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '3LMmr66l24:1f+WILJiW8;r^lcf5k+2Y{-XV|fAmpkp`}J-16|Uvrsq%m2aLaSPT');
define('SECURE_AUTH_KEY',  'ko|k+J[q]XiJvyM4qjmU4T+H(&VJ:Y)>ts9v#_k!f^0TBL&|KxGqxc`{91uEr[^2');
define('LOGGED_IN_KEY',    'u5x-OU6P5//U/@:[mp~`^k~I6G3 H+xZ}b<|+&@+/tB9{y{t:y*aSK]KOeKFeuQd');
define('NONCE_KEY',        'W$ehaC;bAY.M,WBnIbV||_ZRqbbc#:I;I,-+gl[f5/?V+S`5x#NEyqN~}wSd%r3z');
define('AUTH_SALT',        'NjZ9`@(TBC&6}*:#0?Ieb)Lcb4Ava8o.L|z`KD`Zi%7e2-z|,%B=Ro?b^ Q6f.CZ');
define('SECURE_AUTH_SALT', 'SYLEVo6R&P~`6+@nO``..!E/+&]d},6(%Q*iS_Rma&>]7M.QUMn3c=02l+@>nmH]');
define('LOGGED_IN_SALT',   '|Y !/u>`E8|K]]zM@r5*L-0<I,a.*=A{^b`k9dvs;e>YA@5yFc]hXZV `v3mN5/L');
define('NONCE_SALT',       '$/kOLS zA3c-$gN0b|Dp^ULY<Q?+-_HfHI)M+lI*yvNdbZ{&qa+wjD)uQ!=6LGh!');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
