<?php
spl_autoload_register(function ($class_name) {
	require_once $class_name . '.php';
}); // No idea to make it better?

$config = require_once '../config/config.php';

if (!isset($_GET['clean'])) {
	$content = new Content($config['dir'], $config['pageId'],$config['expire']);
	if ($content = $content->main()) {
		echo $content;
	} else {
		header('HTTP/1.1 500 Internal Service Error');
		echo 'Service Unavailable';
	}
} elseif (strcmp($config['clean_key'],$_GET['clean']) === 0) {
	$delete = new Delete($config['dir']);
	echo $delete->deleteAllFiles();
}else{
	header('HTTP/1.1 404 No Found');
}

