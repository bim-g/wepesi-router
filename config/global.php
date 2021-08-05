<?php
// default Language
define("LANG", "fr");

//web root configaration
define('WEB_ROOT', str_replace('public/index.php', '', $_SERVER['SCRIPT_NAME']));
define('ROOT', str_replace('public/index.php', '', $_SERVER['SCRIPT_FILENAME']));

// default timezone
define('TIMEZONE', 'Africa/Kigali');