<?php
error_reporting(0);
if (isset($_GET['addr'])) {
	$url = urldecode($_GET['addr']);
	if (preg_match('/oVp3YhBVk/',$url)) { // I know can be rounded but. I just keep it here :)
		$opts = ['http' => [
			'method'  => 'GET',
			'timeout' => 10,
		]];
		$result = file_get_contents('https://stats.uptimerobot.com/api/' . $url, false, stream_context_create($opts));
		if ($result) {
			echo $result;
		} else {
			echo '<h1>Oops, something goes wrong :( Try again later!';
			header("HTTP/1.1 404 Not Found");
		}
	} else {
		echo '<h1>Don\'t be nesty :(</h1>';
		header("HTTP/1.1 400 Bad Request");
	}
} else {
	echo '<h1>WTF? You should not be here!</h1>';
	header("HTTP/1.1 403 Forbidden");
}
