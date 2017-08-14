<?php

/**
 * Created by PhpStorm.
 * User: NeverBehave
 * Date: 2017/7/25
 * Time: 上午12:14
 */
class Content
{
    public $tempDirectory;
    public $pageId;
    public $time;
    public $address;

    public function __construct($tempDirectory, $pageId, $time = 60, $address = 'https://stats.uptimerobot.com/api')
    {
        $this->tempDirectory = $tempDirectory;
        $this->pageId = $pageId;
        $this->time = $time;  // Expire Time (sec)
        $this->address = $address;
    }

    public function main()
    {
        if (isset($_GET['function'])) {
            $function = $_GET['function'];
            $filename = false;
            switch ($function) {
                case 'auth':
                    //$function = 'auth';
                    //$url = $this->address . '/auth/' . $pageId;
                    //@TODO Post
                    break;
                case 'status':
                    if (!(isset($_GET['pagenumber']) && isset($_GET['sorttype']))) {
                        return false;
                    }
                    $filename = 'status';
                    break;
                case 'monit':
                    $filename = $_GET['monit'] . 'status';
                    break;
                default:
                    return false;
                    break;
            }

            if ($function == 'status') {
                $data = json_decode($this->readData($filename, $this->combineAddress($function)), true);
				for ($i=0; $i < count($data["psp"]["monitors"]); $i++) {
					usort($data["psp"]["monitors"][$i]["allLogs"],function($a,$b){return -($a['timestamp']<=>$b['timestamp']);});
				}
				return json_encode($data);

            } else {
                return $this->readData($filename, $this->combineAddress($function));
            }
        }

        return false;
    }

    public function readData($filename, $url)
    {
        if (
            (!@file($this->tempDirectory . $filename . 'time')) ||  // File Does not exist
            (time() - @file($this->tempDirectory . $filename . 'time')[0] >= $this->time // Data Expire
            )
        ) {
            $time = time();
            $timeFile = @fopen($this->tempDirectory . $filename . 'time', 'w+') or die('Service Temporary Unavailable, No Write Permission');
            fwrite($timeFile, $time); // Write Generated time
            fclose($timeFile);

            $content = $this->getContent($url);

            $dataFile = @fopen($this->tempDirectory . $filename, 'w+') or die('Service Temporary Unavailable, No Write Permission');
            fwrite($dataFile, $content);  // Write Data
            fclose($dataFile);
        }

        return file_get_contents($this->tempDirectory . $filename);
    }

    public function combineAddress($function)
    {
        $url = false; // init

        switch ($function) {
            case 'auth':
                //$function = 'auth';
                //$url = $this->address . '/auth/' . $pageId;
                //@TODO Post
                break;
            case 'status':
                $url = $this->address . '/status-page/' . $this->pageId . '/' . $_GET['pagenumber'] . '?sort=' . $_GET['sorttype'];
                break;
            case 'monit':
                $url = $this->address . '/monitor-page/' . $this->pageId . '/' . $_GET['monit'];
                break;
            default:
                $url = false;
        }

        return $url;
    }

    public function getContent($url)
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

            $i = 1;  // Retry Times

            while (true) {
                $result = file_get_contents($url, false, stream_context_create($opts));

                // Jump out when Success.
                if ($result) {
                    break;
                }

                // Write Error Message When Failed too many times.
                if ($i >= 5) {
                    $result = 'Something goes Wrong! Try again Later :( [Code 404]';
                    break;
                }

                $i++;

                sleep(5);
            }

            return $result;
        }

        return 'Don\'t be nesty :( [Code 400]';
    }
}
