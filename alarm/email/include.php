<?php
/**
 * @file
 * Email include.php
 *
 * Quick start include for using Emailfrom PHP scripts
 */

/**
 * Define include path for classes
 */
//define('ALARM_CLASSPATH', __DIR__ .'/../../lib');
define('LIB_CLASSPATH', '/data02/htdocs/elk/milg/lib');

// Log level (0 = Debug, 1 = Info, 2 = Notice, 3 = Warning, 4 = Error)
define('LOGLEVEL', 0);
define('FDS_LOGFILE', '/data02/logs/fds.log');

use Util\Logger;
use Util\Http\HttpClient;
use Alarm\uapi\email\EmailClient;

/**
 * Register class loader.
 */
spl_autoload_register(function ($class) {
  $class = str_replace('\\', '/', $class);
  require LIB_CLASSPATH .'/' . $class . '.php';
});

/**
 * Create telegram client.
 */
function create_eamil_client($params = array()) {
  $params += array(
//      'command' => TELEGRAM_COMMAND,
//      'keyfile' => TELEGRAM_KEYFILE,
//      'configfile' => TELEGRAM_CONFIG,
//      'homepath' => TELEGRAM_HOMEPATH,
      'log_level' => LOGLEVEL,
      'log_file' => FDS_LOGFILE,
  );

//print_r($params);
//print_r("\n");

  $logger = new Util\Logger($params);
  return new Alarm\uapi\email\EmailClient($logger);
}

