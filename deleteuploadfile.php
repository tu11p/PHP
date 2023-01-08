<?php
$npost = $_GET[npost];
$file_num = $_GET[filenum];
$database = new mysqli('localhost','kknock','password','kknockhomepage');

$myquery = "select * from uploadfile where postnum ='".$npost."' and ordernum = '".$file_num."'";
$infor = $database->query($myquery);
$infor = $infor->fetch_array();

//데이터 베이스에서 삭제
$myquery = "delete from uploadfile where postnum ='".$npost."' and ordernum = '".$file_num."'";
$result = $database->query($myquery);

if(!$result)
{
    echo "<script> alert('삭제 오류'); </script>";
    exit;
}

$save_file_dir = '../uploads/';
$file_name = $infor['postnum']."(".$infor['ordernum'].")".$infor['filename'];
    
$delete_file_dir = $save_file_dir.$file_name;


//서버에서 삭제 unlink 성공 0 , 실패 -1(C언어) , 성공 True, 실패 False(PHP)

if(is_file($delete_file_dir)) {
    
    $is_del = unlink($delete_file_dir);

    if(!$is_del)
    {
        echo "<script> alert('파일 삭제에 실패하였습니다.'); </script>";
        exit;
    }
    
    $reSort = $infor['ordernum']; 
    $myquery = "select * from uploadfile where postnum = '".$npost."' and ordernum > '".$reSort."'";
    $change = $database->query($myquery);
    

    while($update = $change->fetch_array())
    {
        $no = $update['No'];
        $ordernum = $update['ordernum'];
        $file_name = $npost."(".$ordernum.")".$update['filename'];
        $before_file = $save_file_dir.$file_name;

        $ordernum = $ordernum - 1;
        $file_name = $npost."(".$ordernum.")".$update['filename'];
        $after_file = $save_file_dir.$file_name;

        if(is_file($before_file))
        {
            rename($before_file,$after_file);
        }   
        else{
            //파일이 없을 때 
        }

        $myqquery = "update uploadfile set ordernum = $ordernum where No = $no";
        $is_up = $database->query($myqquery);

        if(!$is_up)
        {
            echo "실패"."<br>";
        }
    }
}
else{
    echo "<script> alert('파일이 없습니다.'); </script>";
}

$sql = "alter table uploadfile auto_increment = 1; set @c=0; update uploadfile set No = @c:=@c+1";
$database->multi_query($sql);
?>
<!doctype html>

<html>
<head>
	<title> hello </title>
	<script>
		//parent.myReload();
        opener.location.href = "javascript:myReload();";
	</script>
</head>
</html>