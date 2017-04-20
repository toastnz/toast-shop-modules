<?php

define('TOAST_MODULES_DIR', basename(__DIR__));
if (!defined('BRAND_NAME')) {
    define('BRAND_NAME', 'Toast');
}

/*
 * Set the order export email constant values
 * NOTE: These are for the field description hints only.
 */
define('DEFAULT_DEV_ORDER_EXPORT_EMAIL_TO', 'admin@toast.co.nz');

define('DEFAULT_NZ_ORDER_EXPORT_EMAIL_FROM', 'noreply@cdb.co.nz');
define('DEFAULT_NZ_ORDER_EXPORT_EMAIL_TO', 'weborder_veggiebullet@cdb.co.nz');

define('DEFAULT_AUS_ORDER_EXPORT_EMAIL_FROM', 'noreply@cdbgoldair.com.au');
define('DEFAULT_AUS_ORDER_EXPORT_EMAIL_TO', 'weborder_veggiebullet@cdbgoldair.com.au');
