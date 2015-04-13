<?php
//The entry below were created by iThemes Security to disable the file editor
define( 'DISALLOW_FILE_EDIT', FALSE );

# Database Configuration
define( 'DB_NAME', 'wp_kennettdesign' );
define( 'DB_USER', 'kennettdesign' );
define( 'DB_PASSWORD', 'njB27z9x84MLPGgq' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix  = 'kd1308_';

# Security Salts, Keys, Etc
define('AUTH_KEY', '(8wX;>B{{X>/jZ#~A-.Yx#pe(=5ST7|.]+AW+v&Qq{x{25E[a9H>m;;S#K@t?>0_');
define('SECURE_AUTH_KEY', '$wCV!?xo4{(hr0bG[+Pv y>[&bbsFytzT,27[L$[%5HiRJ(44Znc`Kbp|sXB$RlN');
define('LOGGED_IN_KEY', 'O@+Bw-Z>&?1W/.l10~B-!|1.D>8ki1ms*Kp0Oa(rNN^|f$_)D]^Gt9_U5PS]%,V4');
define('NONCE_KEY', '/8ThDki^&&-G|oEb|K3|E3fe3-b`flk#:^<O~-4W:<:*[.):4eMV|J*.11cW&`6i');
define('AUTH_SALT',        '|4D[H|8Q]npR?Q8yr+k_ex-A1;R.KBtei3$Cy3bF<vOqYnb29>bt7eZ{as}[r@:M');
define('SECURE_AUTH_SALT', 'BWMr@ C<[4]zuE|a}:Xt1*bS$vJr*F_W<PhNz>wU(5P[nG3-R+Aj?eY?Df=`uEK2');
define('LOGGED_IN_SALT',   'sd .:#W<``)~bdp6q.Jr^?2e(2H,)k.{]jC#R#]E6mIwf47A |?tjnl6zSG3 p*a');
define('NONCE_SALT',       'e~F|P Kps7_v`b|P{;TO/w%Y3Y,.qUY6*@*V}DJM~V7=guLEx-I/ut-32!51qW|2');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'PWP_NAME', 'kennettdesign' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', '722feae4d6e1b4549c9e05058932005a742faee8' );

define( 'WPE_FOOTER_HTML', "" );

define( 'WPE_CLUSTER_ID', '1648' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_CDN_DISABLE_ALLOWED', false );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISABLE_WP_CRON', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'kennett-design.com', 1 => 'www.kennett-design.com', 2 => 'kennettdesign.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-1648', );

$wpe_ec_servers=array ( );

$wpe_largefs=array ( );

$wpe_netdna_domains=array ( 0 =>  array ( 'match' => 'kennett-design.com', 'zone' => 'kennettdesign', ), );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( 'default' =>  array ( 0 => 'unix:///tmp/memcached.sock', ), );

define( 'WP_AUTO_UPDATE_CORE', false );

$wpe_special_ips=array ( 0 => '97.107.135.211', );

$wpe_netdna_domains_secure=array ( );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

define( 'WPE_CACHE_TYPE', 'generational' );

define( 'WPE_LBMASTER_IP', '97.107.135.211' );

define( 'WPE_SFTP_PORT', 22 );
define('WPLANG','');

# WP Engine ID


# WP Engine Settings



define( 'WP_MEMORY_LIMIT', '96M' );


# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');

$_wpe_preamble_path = null; if(false){}
