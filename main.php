<?php
date_default_timezone_set("Asia/Seoul");


//------------------------------------------------------------------------------------------------------------
// fds.conf 로드 
//------------------------------------------------------------------------------------------------------------
$configArray = array();
$config_file = fopen('./fds.conf', 'r') or die('unable to open file');
while ( !feof($config_file) ) {
	$line = trim(fgets($config_file));
	if ( !empty($line) ) {
		$tmpArr = split('=', $line);
		$configArray[$tmpArr[0]] = $tmpArr[1];
	}
}
fclose($config_file);

//print_r("\n");
//print_r($configArray);

if ( !array_key_exists('TIMESTAMP_FILE', $configArray) ) {
	print_r('설정 파일 정보가 올바르지 않습니다. NOT EXIST TIMESTAMP_FILE');
	exit(0);
}

//------------------------------------------------------------------------------------------------------------
// timestamp 로드 
//------------------------------------------------------------------------------------------------------------
$lastTimestamp = NULL;
$timestamp_file = fopen($configArray['TIMESTAMP_FILE'], 'r') or die('unable to open file');
while ( !feof($timestamp_file) ) {
	$line = trim(fgets($timestamp_file));
	if ( !empty($line) ) {
		$lastTimestamp = $line;
	}
}
fclose($timestamp_file);

if ( is_null($lastTimestamp) ) {
	$lastTimestamp = "1421713646106";
}

//------------------------------------------------------------------------------------------------------------
// detect_rule.xml 로드 
//------------------------------------------------------------------------------------------------------------
$ruleXml = simplexml_load_file($configArray['DETECT_RULE_FILE'], "SimpleXMLElement", LIBXML_NOCDATA) or die("Error: Cannot create object");
$ruleJson = json_encode($ruleXml);
$ruleArray = json_decode($ruleJson, TRUE);
//print_r("\n");
//print_r ($ruleArray);


//------------------------------------------------------------------------------------------------------------
// class 로드 
//------------------------------------------------------------------------------------------------------------
define('LIB_CLASSPATH', $configArray['LIB_CLASSPATH']);

//use Util\Logger;
//use Util\Http\HttpClient;

/**
 * Register class loader.
 */
spl_autoload_register(function ($class) {
	$class = str_replace('\\', '/', $class);
	require LIB_CLASSPATH .'/' . $class . '.php';
});


$params = array(
	'log_level' => $configArray['LOGLEVEL'],
	'log_file' => $configArray['FDS_LOGFILE'],
);
$logger = new Util\Logger($params);
$elSearch = new Elasticsearch\Search($logger);

$loopCnt = 0;
//------------------------------------------------------------------------------------------------------------
// 무한 루프 : 30초 마다 실행
//------------------------------------------------------------------------------------------------------------
print_r("\n");
do {
	if ($loopCnt == 0) {
		print_r('lastTimestamp : ' . $lastTimestamp . "\n");
		print_r("=========================================================================\n");
	}

	foreach ($ruleArray["rule"] as $rule) {

		$num = trim($rule["num"]);
		$use_yn = trim($rule["use_yn"]);
		$title = trim($rule["title"]);
		$desc = trim($rule["desc"]);
		$query = trim($rule["query"]);
		$index = trim($rule["index"]);
		$interval = trim($rule["interval"]);
		$level = trim($rule["level"]);
		$alarm = trim($rule["alarm"]);
		$user = trim($rule["user"]);

		$query = str_replace('0000000000000', $lastTimestamp, $query);
		$query = str_replace('%{TO_DAY}%', date("Ymd"), $query);
		//print_r($query);print_r("\n");

		if ($use_yn != 'Y') continue;

		$searchResult = $elSearch->execute($query, $index);
		$total = $searchResult['hits']['total'];

//		print_r ($searchResult);
		//print_r ("\n");
		if ($loopCnt == 0) {
			print_r ('* ' . $title . "\n");		
		}
		else {
			print_r('.');
			if ($loopCnt % 10 == 0) {
				print_r('#');
			}
		}

		//------------------------------------------------------------------------------------------------------------
		// 이상 상황 발생 
		//------------------------------------------------------------------------------------------------------------
		if ($total > 0) {
			// TODO 로그파일 기록
			print_r ("***************************************************************************\n");
			print_r ("$alarm 발송 : lastTimestamp = $lastTimestamp \n");
			print_r ("***************************************************************************\n");
			print_r ("\n");
			//print_r($searchResult);

			$emailClient = create_eamil_client();
			$title = '[마일리지 FDS] ' . $desc . "[$total]";

			// TODO content에 설명 
			$content = "lastTimestamp = $lastTimestamp <br/>";
			$content .= "<hr/>query : <br/>";
			$content .= json_encode($query);
			$content .= "<br/><hr/>data : <br/>";
			$content .= json_encode($searchResult);

			print_r ($title . "\n");
			print_r ($content);
			print_r ("\n");

			$alarmResult = $emailClient->send($title, $content);
			// TODO 알람 로그 기록 
//			print_r ("====> alarmResult : ");
//			print_r ($alarmResult);
		}

		//------------------------------------------------------------------------------------------------------------
		// timestamp 저장 
		//------------------------------------------------------------------------------------------------------------
		$date = new DateTime();
		$lastTimestamp = $date->getTimestamp() * 1000 ;
		$fp = fopen($configArray['TIMESTAMP_FILE'], 'w'); 
		fwrite($fp, $lastTimestamp); 
		fclose($fp);
	}

	$loopCnt++;

	// 30초 마다 실행 
	sleep(30);

} while (true);


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



















print_r("\n");









//curl -XGET '192.168.51.101:9200/mileage-2015*/_search' -d '{"query":{"bool":{"must":[{"term":{"milg_api.milgInfo.MilgKindCd":"510"}},{"range":{"milg_api.userInfo.timestamp":{"gte":"1421713646106"}}}],"must_not":[],"should":[]}}'