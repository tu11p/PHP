<?php
	session_start();
	if(isset($_SESSION["islogin"]))
	{
		session_destroy();
	}
	echo "<script> alert('로그아웃 하였습니다.'); location.href='./homepage.html'; </script>";
?>
