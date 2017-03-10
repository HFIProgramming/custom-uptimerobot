<?php
$pageId = 'oVp3YhBVk';
$address = 'https://stats.uptimerobot.com/api';
error_reporting(0);
if (isset($_GET['function'])) {
	$function = urldecode($_GET['function']);
	$url = '';
	switch ($function) {
		case 'auth':
			$function = 'auth';
			$url = $address . '/auth/' . $pageId;
			header("HTTP/1.1 200 OK");
			//@TODO Post
			break;
		case 'status':
			$function = 'status';
			$url = $address . '/status-page/' . $pageId . '/' . $_GET['pagenumber'] . '?sort=' . $_GET['sorttype'];
			readMain($function, $url);
			break;
		case 'monit':
			$function = $_GET['monit'];
			$url = $address . '/monitor-page/' . $pageId . '/' . $_GET['monit'];
			readMain($function, $url);
			break;
	}
	exit();
} else {
	echo '<h1>WTF? You should not be here!</h1>';
	header("HTTP/1.1 403 Forbidden");
	exit();
}

function readMain($function, $url)
{
	if ((!file($function . 'time')) || (time() - file($function . 'time')[0] >= 180)) {
		$timefile = fopen($function . 'time', "w") or die('errror while open file');
		$time = time();
		fwrite($timefile, $time);
		fclose($timefile);

		$content = getContent($url);
		$file = fopen($function, "w") or die('errror while open file');
		fwrite($file, $content);
		fclose($file);

		echo file_get_contents($function);
	} else {
		echo file_get_contents($function);
	}
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
		$i = 1;
		while (true) {
			$result = file_get_contents($url, false, stream_context_create($opts));
			if ($result) {
				break;
			}
			if ($i >= 5) {
				echo '<h1>Something goes Wrong! Try again Later :(</h1>';
				header("HTTP/1.1 404 No Found");
				$result = 'Something goes Wrong! Try again Later :(';
				break;
			}
			$i++;
			sleep(5);
		}

		return $result;
	} else {
		echo '<h1>Don\'t be nesty :(</h1>';
		header("HTTP/1.1 400 Bad Request");
		exit();
	}
}