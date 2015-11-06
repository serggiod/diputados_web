<?php
/** Nombre a mi aplicación. */
define('APP_NAME', 'diputados_web');

/** Url a mi aplicación. */
define('APP_URL', 'http://localhost/diputados_web');

/** Url principal de la pagina web. */
define('LEG_URL', 'http://www.legislaturajujuy.gov.ar');

/** Path a mi aplicación. */
define('APP_PATH', '/home/sdominguez/Desarrollo_Web/diputados_web/public_html');

/** Path to external class. */
define('EXTERNAL_CLASS', '/home/sdominguez/Desarrollo_Web/diputados_web/public_html/lib');

/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'diputados_web');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'sdominguez');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', 'rjwfthw72x45');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', 'utf8_general_ci');

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';

/**
 * Idioma de WordPress.
 *
 * Cambia lo siguiente para tener WordPress en tu idioma. El correspondiente archivo MO
 * del lenguaje elegido debe encontrarse en wp-content/languages.
 * Por ejemplo, instala ca_ES.mo copiándolo a wp-content/languages y define WPLANG como 'ca_ES'
 * para traducir WordPress al catalán.
 */
define('WPLANG', 'es_ES');

/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/** WordPress absolute path to the Wordpress directory. */
/** Sets up WordPress vars and included files. */
define('ABSPATH','/home/sdominguez/Desarrollo_Web/diputados_web/public_html/lib/class.wordpress/');
require_once(ABSPATH . 'wp-settings.php');
