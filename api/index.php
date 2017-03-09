<?php
$pageId = 'oVp3YhBVk';
$address = 'https://stats.uptimerobot.com/api';
error_reporting(0);
if (isset($_GET['function'])) {
	$function = urldecode($_GET['function']);
	$url = '';
	switch ($function) {
		case 'auth':
			$url = $address . '/auth/' . $pageId;
			//@TODO Post
			break;
		case 'status':
			$url = $address . '/status-page/' . $pageId . '/' . $_GET['pagenumber'] . '?sort=' . $_GET['sorttype'];
			break;
		case 'monit':
			$url = $address . '/monitor-page/' . $pageId . '/' . $_GET['monit'];
	}
	getContent($url);
} else {
	echo '<h1>WTF? You should not be here!</h1>';
	header("HTTP/1.1 403 Forbidden");
	exit();
}


function getContent($url)
{
	if (!empty($url)) {
		$opts = [
			'http' => [
				'method'  => 'GET',
				'timeout' => 10,
			],
			'ssl'  => [
				"verify_peer"      => true,
				"verify_peer_name" => true,
			],
		];
		$result = file_get_contents($url, false, stream_context_create($opts));
		if ($result) {
			echo $result;
		} else {
			header("HTTP/1.1 404 Not Found");
			echo '<h1>Oops, something goes wrong :( Try again later!';
		}
	} else {
		echo '<h1>Don\'t be nesty :(</h1>';
		header("HTTP/1.1 400 Bad Request");
	}
	exit();
}