<?php

// path constants
define('HTTP_DOCROOT', '/webdev');
//define('HTTP_DOCROOT', '/data/home/nroberts/public_html');
define('WEBSERVICE_PATH', HTTP_DOCROOT. '/webservice');
define('SCRIPTS_PATH', HTTP_DOCROOT.'/scripts');
define('WEBSERVICE_HTACCESS_FILE', WEBSERVICE_PATH.'/.htaccess');
define('WEBSERVICE_INCLUDE_PATH', WEBSERVICE_PATH.'/include');
define('WEBSERVICE_CONTROLLERS_PATH', WEBSERVICE_INCLUDE_PATH.'/handler');
define('TAB', "\t");
define('NEWLINE', "\n");
define('SPACE', ' ');
define('CARROT', '^');

// parsing constants
define('PARAM_MODULE', '_module');
define('CONTROLLER_CLASS_PREFIX', 'APIController');
define('CONTROLLER_CLASS_SUFFIX', '.php');
define('REWRITE_PATTERN', 'RewriteRule');
define('DISPATCHER_PATTERN', 'dispatcher.php?_module=');
define('COMMENT_PATTERN', '#');

