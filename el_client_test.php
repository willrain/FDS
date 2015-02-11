<?php
require 'vendor/autoload.php';

//http://192.168.51.101:9200/mileage-2015*/_search?q=milgInfo.MilgKindCd:510&scroll=1m&size=1

date_default_timezone_set("Asia/Seoul");
// 현재 시간 보다 큰 것에 대해서만 검색 
$date = new DateTime();
$timestamp = $date->getTimestamp() * 1000;	// 밀리초까지 
// -- test
$timestamp = '1421713646106';


$jsonStr = '{"query":{"bool":{"must":[{"term":{"milg_api.milgInfo.MilgKindCd":"510"}},{"range":{"milg_api.userInfo.timestamp":{"gte":"0000000000000"}}}],"must_not":[],"should":[]}}}';

$jsonStr = str_replace("0000000000000", $timestamp, $jsonStr);

print_r ($jsonStr);
print_r ("\n-----------------------------------------------------------------\n");

$param = array();
$param['index'] = 'mileage-2015*';
$param['type'] = 'milg_api';
$param['body'] = $jsonStr;
$param['size'] = 1;
//$param['body']['query']['bool']['must']['range']['userInfo.timestamp']['gt'] = '1421713646106';

print_r($param);

//[timestamp] => 
//1421648052081
//1421627444167
//1421627262001

$client = new Elasticsearch\Client();
$result = $client->search($param);


print_r($result);





