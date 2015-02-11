<?php

/**
 * @file
 * Definition of Drupal/Email/EmailClient
 */

namespace Alarm\uapi\email;

use Util\Http\HttpClient;
//use \streamWrapper;
//use \Exception;

define('UAPI_SERVER', 'http://uapi.ssglocal.com');
define('PATH_SEARCH', '/automail/search.json');
define('PATH_SEND', '/automail/send.json');


define('FROM_EMAIL', '135224@emart.com');
define('FROM_NAME', 'FDS_관리자');
define('ADMIN_ID', '135224');
define('ADMIN_NAME', '윤승환');

define('TMPL_ID', '15160');
define('TMPL_TYPE', '2');


/**
 * Email Client
 */
class EmailClient {

	/**
	 * Running parameters to pass to the process.
	 *
	 * @var array
     */
	protected $rawStr = NULL; 

	/**
	 * @var EmailLogger
	 */
	protected $logger;

	protected $rcv_user = array(
		'135224' => array(
			'mbrid' => '0000000001'
			, 'name' => '윤승환'
			, 'emailAddr' => 'willrain@emart.com'
			, 'phone' => '01062448579'
		)
		, '145734' => array(
			'mbrid' => '0000000002'
			, 'name' => '최시원'
			, 'emailAddr' => 'siwonchoi@emart.com'
			, 'phone' => '01052882528'
		)
	);

	/**
     * Class constructor.
     *
     */
	public function __construct($logger) {
//		print_r ("==> " . __METHOD__ ."\n");
		$this->logger = $logger;
	}

	public function send ($title, $content) {
//		print_r ("==> " . __METHOD__ ."\n");

//		print_r ($content);
//		print_r ("\n");

		
		$this->rawStr = $this->setSendParam($title, $content);
		print_r ("this->rawStr : " );
		print_r ($this->rawStr);
		print_r ("\n");

		if (is_null($this->rawStr)) { 
			$this->log("Error : 파라메터 설정 안되어 있음.");
			return false;
		}
		
		$url = UAPI_SERVER . PATH_SEND;
		$method = 'POST';

		$httpClient = new HttpClient($this->logger);
		$result = $httpClient->executeCurl($url, $method, $this->rawStr);

		// 파라메처 초기화 
		$this->rawStr = NULL;

		return $result;
	}

	protected function setSendParam($title, $content) {
//		print_r ("==> " . __METHOD__ ."\n");

		$data = array();
		foreach ($this->rcv_user as $rcv_user) {
			$reqDto = array(
			//	'reqkey' => $timestamp.'_0001830499'		// 발송키 (재전송 이용) 
				'tmplid'		=> TMPL_ID					// 템플릿아이디 
				, 'tmpltype'	=> TMPL_TYPE				// 템플릿타입 1:템필릿 2:HTML 3:URL 
				, 'fromemail'	=> FROM_EMAIL				// 발신자 이메일 주소 
				, 'fromname'	=> FROM_NAME				// 발신자 이름 
				, 'admid'		=> ADMIN_ID					// 발송자 ID 
				, 'admname'		=> ADMIN_NAME				// 발송자 이름 
				, 'mbrid'		=> $rcv_user['mbrid']		// 회원 키 (Unique Key) 
				, 'toemail'		=> $rcv_user['emailAddr']	// 수신자 이메일 주소 
				, 'toname'		=> $rcv_user['name']		// 수신자 이름 
				, 'title'		=> $title	 // 제목 
				, 'content'		=> $content	 // HTML 전송 내용 
			//	, 'replyemail' => 'willrain@emart.com'	 // 회신 이메일 주소 
			//	, 'sendtime' => ''	 // 발송 예약 시간 
			//	, 'url' => ''	 // URL 전송 내용 
			//	, 'siteid' => ''	 // 몰구분 ID 
			//	, 'sitename' => ''	 // 몰구분 이름 
			//	, 'corpid' => ''	 // 업체구분 
			//	, 'corpname' => ''	 // 업체 이름 
			//	, 'mailtype' => ''	 // 메일구분 
			//	, 'mailname' => ''	 // 메일구분명
			//	, 'surveyid' => ''	 // 설문ID 
			);

			array_push($data, $reqDto);
		}

		$reqDtoList = array(
			"sendMail" => array('data'=>$data)
		);

//		$reqDtoList = json_encode($reqDtoList); 
//		print_r ($reqDtoList);
//		print_r ("\n");

		return json_encode($reqDtoList); 
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
