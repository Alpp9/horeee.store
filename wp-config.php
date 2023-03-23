<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/home/hord8138/public_html/wp-content/plugins/wp-super-cache/' );
define( 'DB_NAME', 'hord8138_wp253' );

/** Database username */
define( 'DB_USER', 'hord8138_wp253' );

/** Database password */
define( 'DB_PASSWORD', 'S)Ydp77]82' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'aw2djjtff0dqufskpvimahku3aglimx3hutpocihfyhu79mdfu2o9rg0a1xoayya' );
define( 'SECURE_AUTH_KEY',  'cfeh1alstdh3tubskwedhm2jewhwu0dlavelmzkfdau6qwlpzujauq4iska9uwfw' );
define( 'LOGGED_IN_KEY',    'vlx9pabpmb3wqnm73rzob9ivvwme6wut5tbchrilbingrv9qxg9zzwwhdoxcw2vl' );
define( 'NONCE_KEY',        'h6ptharyeflndngybinoomjep3szbuih1gcvw382iq1vrgxgezroz9mvj564ishp' );
define( 'AUTH_SALT',        'ouvfxmw59w16yalhcvmb2dyqsgt0ldc3inplo9ggl0leqxbix4m6hubgc1n5dzye' );
define( 'SECURE_AUTH_SALT', 'wgacfyglcvrudfgco2kyflek2bk0yupu8pr7t8e51dnahw9gqxtdyqzzpyb80rly' );
define( 'LOGGED_IN_SALT',   'ufatmebwymray4zicll15strvbgkv0ghs8zgcmjgvmztwbsllywbltbjcvhitqta' );
define( 'NONCE_SALT',       'ljcfrk8jjvlcoa6bleelndfpi6smwxhap9nquow86wf1bai3q8jb8raxatiikne0' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wprq_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
