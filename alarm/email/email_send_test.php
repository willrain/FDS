<?php
date_default_timezone_set("Asia/Seoul");

/**
 * Include defaults and class loader.
 */
require_once 'include.php';


$date = new DateTime();
$timestamp = $date->getTimestamp();	// 밀리초까지 

$emailClient = create_eamil_client();
//$emailClient->setSendParam("title", "content");
$title = '제목 ';
$content = '내용 ';

$result = $emailClient->send($title, $content);

print_r ("====> result : ");
print_r ($result);



