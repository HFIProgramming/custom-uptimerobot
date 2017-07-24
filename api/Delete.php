<?php

/**
 * Created by PhpStorm.
 * User: NeverBehave
 * Date: 2017/7/25
 * Time: 上午2:18
 */
class Delete
{
	public $tempDirectory;

	public function __construct($tempDirectory)
	{
		$this->tempDirectory = $tempDirectory;
	}


	public function deleteAllFiles()
	{
		$files = glob($this->tempDirectory.'*'); // get all file names
		var_dump($files);
		foreach ($files as $file) { // iterate files
			if (is_file($file))
				unlink($file); // delete file
		}

		return 'success';
	}
}