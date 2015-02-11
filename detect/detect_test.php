<?php
date_default_timezone_set("Asia/Seoul");


define('LIB_CLASSPATH', '/data02/htdocs/elk/milg/lib');

use Util\Logger;
use Util\Http\HttpClient;

/**
 * Register class loader.
 */
spl_autoload_register(function ($class) {
	$class = str_replace('\\', '/', $class);
	require LIB_CLASSPATH .'/' . $class . '.php';
});


//------------------------------------------------------------------------------------------------------------
// static 변수 선언
//------------------------------------------------------------------------------------------------------------
$EL_SERVER = "192.168.51.101"; // 10.203.5.28(master)  / 10.203.5.27(slave)
$EL_SERVER_PORT = "9200";
$EL_INDEX = "mileage-2015*";


//------------------------------------------------------------------------------------------------------------
// detect_rule.xml 로드 
//------------------------------------------------------------------------------------------------------------
$ruleXml=simplexml_load_file("../resources/detect_rule.xml", "SimpleXMLElement", LIBXML_NOCDATA) or die("Error: Cannot create object");
$ruleJson = json_encode($ruleXml);
$ruleArray = json_decode($ruleJson, TRUE);
print_r ($ruleArray);


//------------------------------------------------------------------------------------------------------------
// lastTimestamp 시간 로드 
//------------------------------------------------------------------------------------------------------------





$date = new DateTime();
echo $date->getTimestamp() * 1000;	// 밀리초까지 




$params = array(
	'log_level' => LOGLEVEL,
	'log_file' => FDS_LOGFILE,
);

$logger = new Util\Logger($params);
$elSearch = new Elasticsearch\Search($logger);


$lastTimestamp = array(
	1 => "2015-01-20T00:00:00.000Z"
	, 2 => "2015-01-20T00:00:00.000Z"
);
print_r ($lastTimestamp);


exit(0);

$index = 'mileage-2015*';
$query = '{"query":{"bool":{"must":[{"term":{"milg_api.milgInfo.MilgKindCd":"510"}},{"range":{"milg_api.userInfo.timestamp":{"gte":"1421713646106"}}},{"range":{"milg_api.@timestamp":{"gt":"2015-01-20T00:27:42.592Z"}}}],"must_not":[],"should":[]}},"from":0,"size":10,"sort":[],"facets":{}}';
$result = $elSearch->execute($query, $index);
$hits = $result['hits'];
$total = $hits['total'];

$listTimeStamp = $result['hits']['hits'][0]['_source']['@timestamp'];


print_r ($result);
print_r ("\n");
print_r ("total => " . $total . "\n");
print_r ("listTimeStamp => " . $listTimeStamp . "\n");


exit(0);


//$now_timestamp = ;

//------------------------------------------------------------------------------------------------------------
// 무한 루프 : 1초 마다 실행
//------------------------------------------------------------------------------------------------------------
do {
	
	echo "\n";
	echo date('h:i:s') . "\n";

	foreach ($ruleArray["rule"] as $rule) {


// $lastTimestamp 
// 현재 시간 기록 
// => 데몬 새로 시작할 때 그 시간으로 로딩  



		$num = trim($rule["num"]);
		$use_yn = trim($rule["use_yn"]);
		$desc = trim($rule["desc"]);
		$query = trim($rule["query"]);
		$interval = trim($rule["interval"]);
		$level = trim($rule["level"]);
		$alarm = trim($rule["alarm"]);
		$user = trim($rule["user"]);

//http://192.168.51.101:9200/mileage-2015*/_search?q=milgInfo.MilgKindCd:510&scroll=1m&size=1

		$url = "http://$EL_SERVER:$EL_SERVER_PORT/$EL_INDEX/_search?q=".$query;
		print_r ($url . "\n");
	
		$result = $httpClient->executeCurl($url, 'GET');
		$result = json_decode($result, true);


		$hits = $result["hits"];
		$total = $hits["total"];

		echo ("total => " . $total . "\n");

		//------------------------------------------------------------------------------------------------------------
		// 에러 검출 
		//------------------------------------------------------------------------------------------------------------
		if ($total > 0) {
			echo ("TODO $alarm 발송 \n");
			print_r($result);
		}
	}
	
	// 10초 마다 실행 
	sleep(10);

} while (true);




// TODO . 로그 저장 


