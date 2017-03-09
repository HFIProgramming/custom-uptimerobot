<?php
error_reporting(0);
if(isset($_GET['addr']) && isset($_SERVER['HTTP_REFERER'])){
    $url = urldecode($_GET['addr']);
    $referer = parse_url($_SERVER['HTTP_REFERER']);
    if(preg_match("/^http/",$url) && $referer['host']=='status.hfi.com'){//防盗链验证
        $opts = array('http'=>array(
            'method'=>'GET',
            'timeout'=>10
        ));
        $result = file_get_contents('http://stats.uptimerobot.com/'.$url,FALSE,stream_context_create($opts));
        if($result){
            echo $result;
        }else{
            header("HTTP/1.1 404 Not Found");
        }
    }else{
        header("HTTP/1.1 400 Bad Request");
    }
}else{
    header("HTTP/1.1 403 Forbidden");
}
