<?php

/**
 * @file
 */

namespace Elasticsearch;

use Util\Http\HttpClient;


define('EL_SERVER', '10.203.5.28');		// 192.168.51.101(local) // 10.203.5.28(master)  / 10.203.5.27(slave)
define('EL_SERVER_PORT', '9200');		


/**
 * Http Client
 */
class Search {

	protected $logger;

	/**
     * Class constructor.
     *
     * @var EmailLogger $logger
     *   Logging interface.
     */
	public function __construct($logger) {
//		print_r ("==> " . __METHOD__ ."\n");
		$this->logger = $logger;
	}
	
	public function execute($query, $index = NULL) {
//		print_r ("==> " . __METHOD__ ."\n");

		$url = 'http://'.EL_SERVER.':'.EL_SERVER_PORT.'/';
		if ( !is_null($index) ) {
			$url = $url . $index . '/';
		}
		$url = $url . '_search';

//		print_r ('    url = ' . $url . "\n");
	
		$httpClient = new HttpClient($this->logger);
		$result = $httpClient->executeCurl($url, 'GET', $query);
		$result = json_decode($result, true);

		return $result;
	}





  /**
   * Get logger.
   *
   * @return EmailLogger
   */
  function getLogger() {
    return $this->logger;
  }

  /**
   * Shorthand for debug message.
   */
  protected function debug($message, $args = NULL) {
    $this->logger->logDebug($message, $args);
  }

  /**
   * Shorthand for log message.
   *
   * @param mixed $message
   */
  protected function log($message, $args = NULL) {
    $this->logger->logInfo($message, $args);
  }

  /**
   * Get logged messages.
   */
  function getLogs() {
    return $this->logger->formatLogs();
  }

  /**
   * Class destructor.
   */
  public function __destruct() {
    unset($this->logger);
  }

}
