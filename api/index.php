<?php
error_reporting(0);
if (isset($_GET['addr'])) {
	$url = urldecode($_GET['addr']);
	if (preg_match('oVp3YhBVk',$url)) {
		$opts = ['http' => [
			'method'  => 'GET',
			'timeout' => 10,
		]];
		$result = file_get_contents('https://stats.uptimerobot.com/api/' . $url, false, stream_context_create($opts));
		if ($result) {
			echo $result;
		} else {
			header("HTTP/1.1 404 Not Found");
		}
	} else {
		header("HTTP/1.1 400 Bad Request");
	}
} else {
	header("HTTP/1.1 403 Forbidden");
}
