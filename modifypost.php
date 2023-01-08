<?php
	session_start();
	$postindex = $_POST[idx];
	$page = $_POST[page];
	$title = $_POST[title];
	$content  = $_POST[content];
	$term = $_POST[term];
	$seek = $_POST[search];
	$id = $_SEESION['id'];
	
	if(!isset($_SESSION['islogin']))
	{
		echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
		exit;
	}

	$save = mysqli_connect('localhost','kknock','password','kknockhomepage');
	$nowpost = $save->query("select * from postlist where No = '".$postindex."'");
	$nowpost = $nowpost->fetch_array();
	$author = $nowpost["author"];

	if($title == "" || $content =="")
	{
		if($title =="")
		{
			echo "<script> alert('제목을 입력하세요'); history.back(); </script>";
		}
		else
		{
			echo "<script> alert('내용을 입력하세요'); history.back(); </script>";
		}
	}
	else if($save) //데이터 베이스 접근함
	{
		//게시글 수정하기
		$save->query("update postlist set title = '$title' where No ='$postindex'");
		$save->query("update postlist set contents = '$content' where No ='$postindex'");

		echo "<script> alert('게시글 수정 하였습니다.');</script>";
		if(isset($_POST[term]))
		{
			echo "<script> location.href='./readpost.php?idx=$postindex&write=$author&page=$page&term=$term&search=$seek'; </script>";
		}
		else
		{
			echo "<script> location.href='./readpost.php?idx=$postindex&write=$author&page=$page'; </script>";
		}
	}
	else //데이터 베이스 접근 못함 -> 서버 문제
	{
		echo "<script> alert('나중에 시도해주세요.'); location.href='./homepage.html';</script>";
	}
?>
