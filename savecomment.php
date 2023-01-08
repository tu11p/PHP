<?php
	session_start();
	if(!isset($_SESSION["islogin"]))
	{
		echo "<script> alert('로그인이 필요합니다.'); location.href='./logout.php'; </script>";
	}

	$idx = $_POST[postnum];
	$mode = $_POST[mode];
	$content = $_POST[content];

	$id = $_SESSION["id"];
	$database = new mysqli('localhost','kknock','password','kknockhomepage');
	$rootlist;
	$rpy;

	if($mode == "1") //댓글 작성
	{
		$rootlist = $database->query("select rootlist from comment where postnum='".$idx."' order by rootlist desc");
		$rootlist = $rootlist->fetch_array();
		$rootlist = $rootlist["rootlist"] + 1;
		$rpy=0;
	}
	else	//댓글의 댓글 작성
	{
		$rootlist = $_POST[rlist];
		$rpy = $database->query("select rpy from comment where postnum='".$idx."' and rootlist='".$rootlist."' order by rpy desc limit 1");
		$rpy = $rpy->fetch_array();
		$rpy = $rpy["rpy"] + 1;
	}

	if(!$database->query("insert into comment(postnum, author, content, rootlist, rpy) values('$idx','$id','$content','$rootlist','$rpy')"))
	{
		echo("쿼리오류 발생: " . mysqli_error($database));
		
	//	echo "<script> alert('에러발생'); location.href='./homepage.html'; </script>";
		exit;
	}

	//No 재정렬
    $sql = "alter table comment auto_increment = 1; set @c=0; update comment set No = @c:=@c+1";
	$database->multi_query($sql);

	echo "<script> location.href = document.referrer; </script>";
?>
