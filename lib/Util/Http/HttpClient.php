<?php

/**
 * @file
 */

namespace Util\Http;

/**
 * Http Client
 */
class HttpClient {

	protected $logger;

	/**
     * Class constructor.
     *
     * @var EmailLogger $logger
     *   Logging interface.
     */
	public function __construct($logger) {
		//print_r ("==> " . __METHOD__ ."\n");
		$this->logger = $logger;
	}
	
	public function executeCurl($url, $method = 'GET', $rawStr='')
	{
		//print_r ("==> " . __METHOD__ ."\n");
		//print_r ("    url : " . $url . "\n");
		//print_r ("    method : " . $method . "\n");
		//print_r ("    rawStr : " . $rawStr . "\n");

		$ch = curl_init();
	    $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';
 
		switch(strtoupper($method))
	    {
	        case 'GET':     
		        curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $rawStr);
			    break;
 
	        case 'POST':
		        $info = parse_url($url);
			    $url = $info['scheme'] . '://' . $info['host'] . $info['path'];
				curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_POST, true);
		        //curl_setopt($ch, CURLOPT_POSTFIELDS, $info['query']);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $rawStr);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',	
					'Content-Length: ' . strlen($rawStr))
				);  

			    break;
 
	        default:
			    return false;
		}
 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$res = curl_exec($ch);
		curl_close($ch);
	 
		return $res;
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
