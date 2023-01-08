<?php
	session_start();

	if(!isset($_SESSION["islogin"]))
	{
		echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
	}
	$database = new mysqli('localhost','kknock','password','kknockhomepage');

	$no = $_POST[npost];
        $cotent = $_POST[content];
        $root = $_POST[rootindex];
        $reply = $_POST[rpy];
	
	$sql = "update comment set  content='".$cotent."' where postnum ='".$no."' and rootlist = '".$root."' and rpy ='".$reply."'";

	if($database->query($sql))
	{
		echo "<script> alert('수정 하였습니다.');</script>";
	}
	else
	{
		echo "<script> alert('서버 에러!'); location.href='./homepage.html'; </script>";
		exit;
	}
	echo "<script> history.back(); </script>";
?>
