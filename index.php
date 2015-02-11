<?php

$file = "rule.xml";

$xml=simplexml_load_file($file) or die("Error: Cannot create object");
$json = json_encode($xml);
$arry = json_decode($json, TRUE);


print_r($arry["rule"]);

?>

<?php


$EL_SERVER = '192.168.51.101'; // 10.203.5.28(master)  / 10.203.5.27(slave)

foreach ($arry["rule"] as $rule) {
	//echo $rule["query"] . ' : ' . $rule["interval"] . '<br/>';

	$query = $rule["query"];
	$url = "http://192.168.51.101:9200/mileage-2015*/_search?q=".$query;

	echo ("<br/>" . $query);


	//  Initiate curl
	$ch = curl_init();
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);

	$result = json_decode($result, true);
	// Will dump a beauty json :3
	//var_dump($result);


	$hits = $result["hits"];

	$total = $hits["total"];

	echo ('total => ' . $total);

	// 에러 검출 
	if ($total > 0) {
		// TODO 이메일 발송 
	}


	// TODO 
	// 검증할 query + interval 설정 
	//==> db에 저장? 파일에 저장 

	echo ("<br/>");
}


// 클래스를 만들어야 겠지? ;;;  

?>



