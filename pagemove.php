<?php
	$condition = $_GET[condition];
	$now_page = $_GET[page];
	$maxpage = $_GET[maxpage];
	$term = $_GET[term];
	$seek = $_GET[search];
	
	if($condition == "M")
	{
		if($now_page > 1)
		{
			$now_page = $now_page - 1;
		}
		else
		{
			echo "<script> alert('이전 페이지가 존재하지 않습니다.'); </script>";
		}
	}
	else
	{
		if($now_page < $maxpage)
		{
			$now_page = $now_page + 1;
		}
		else
		{
			echo "<script> alert('다음 페이지가 존재하지 않습니다.'); </script>";
		}
	}
	
	if(isset($_GET[term]))
	{
		echo "<script> location.href='./boardpage.php?page=$now_page&term=$term&search=$seek'; </script>";
	}
	else
	{
		echo "<script> location.href='./boardpage.php?page=$now_page'; </script>";
	}
	mysqli_close($data);
?>
