<?php

$no = $_GET[number];

$database = mysqli_connect('localhost','kknock','password','kknockhomepage');

$downfile = $database->query("select * from uploadfile where No = $no");
$downfile = $downfile->fetch_array();

$file_dir = "../uploads/";
$client_file_name = $downfile['filename'];
$server_file_name = $downfile['postnum']."(".$downfile['ordernum'].")".$downfile['filename'];

$file = $file_dir . $server_file_name;
 
if (is_file($file)) {
 
    if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) || preg_match("/Trident/", $_SERVER['HTTP_USER_AGENT'])) { 
        header("Content-type: application/octet-stream"); 
        header("Content-Length: ".filesize("$file"));
        header("Content-Disposition: attachment; filename=$client_file_name");
        header("Content-Transfer-Encoding: binary"); 
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public"); 
        header("Expires: 0"); 
    }
    else { 
        header("Content-type: file/unknown"); 
        header("Content-Length: ".filesize("$file")); 
        header("Content-Disposition: attachment; filename=$client_file_name");
        header("Content-Description: PHP3 Generated Data"); 
        header("Pragma: no-cache"); 
        header("Expires: 0"); 
    }
 
    $fp = fopen($file, "rb"); 
    fpassthru($fp);
    fclose($fp);
}
else {
    echo "해당 파일이 없습니다.";
}
?>
