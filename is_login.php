<?php
	session_start();
	$locate = $_GET[locate];
	$page = $_GET[page];

	$condition = $_SESSION["islogin"];
	
	if($locate == 1) //homepage -> join
  	{
		if($condition == "YES")
	  	{
			echo "<script> alert('로그인 되어있습니다.'); location.href='./homepage.html'; </script>";
	  	}
	  	else
	  	{
		 	 echo "<script> location.href='./joinpage.html'; </script>";
	 	}
  	}
  	else if($locate == 2) //homepage ->login
  	{
	  	if($condition == "YES") //로그인 상태 -> 일로 왔다는 것은 로그아웃을 누름
	  	{
		  	echo "<script>location.href='./logout.php'; </script>";
	  	}
	  	else //로그인X 상태 -> 일로 왔다는 것은 로그인을 누름
	  	{
		  	echo "<script> location.href='./loginpage.html'; </script>";
	  	}
  	}
  	else if($locate == 3) //homepage -> goto board
  	{
		if($condition != "YES")
	  	{
		  	echo "<script> alert('로그인이 필요합니다.'); location.href='./loginpage.html';</script>";
	  	}
	  	else
	  	{
		  	echo "<script> location.href='./boardpage.php?page=1';</script>";
	  	}
	}
?>
