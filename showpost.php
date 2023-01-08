<?php
	session_start();

	header('Content-Type: text/html; charset=utf-8');

	$posts = new mysqli('localhost','kknock','password','kknockhomepage');
	$posts->set_charset("utf8");

	function showpost($sql)
	{
		global $posts;

		return $posts->query($sql);
	}
	function returnmaxpage($sql, $mode)
	{
		global $posts;
		$maxpost = $posts->query($sql);
		if($mode == 1)
		{
			$maxpost = $maxpost->fetch_array();
			$maxpost = $maxpost["contents"];
		}
		else if($mode == 2)
		{
			$maxpost = $maxpost->num_rows;
		}
		else
		{
			echo "<script> alert('ERROR'); location.href='./logout.php'; </script>";
		}
		
		if($maxpost % 10 == 0)
		{
			return (int)($maxpost / 10);
		}
		else
		{
			return (int)($maxpost / 10) + 1;
		}
	}	
?>
