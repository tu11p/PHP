<?php
/*
 * 구분 3 -> auto_increment 초기화
 * 1 : 글 삭제로 인한 해당 게시글 모든 댓글 삭제 -> deletepost.php에서 실행되므로 신경X
 * 2 : 댓글 삭제 -> 해당 댓글의 모든 대댓글 삭제 -> 특정 댓글의 인덱스 감소 -> href = readpost.php
 * 3 : 대댓글 삭제 -> 해당 대댓글 삭제 -> 대댓글이 속한 부모댓글의 인덱스 재정렬 -> href = readpost.php
 */
	session_start();
	$id = $_SESSION["id"];
	$npost = $_GET[npost];
	$rlist = $_GET[root];
	$rpy = $_GET[rpy];
	
	$database = new mysqli('localhost','kknock','password','kknockhomepage');

	$sql = "select * from comment where postnum='".$npost."' and rootlist='".$rlist."' and rpy='".$rpy."'";

	$data = $database->query($sql);

	$data = mysqli_fetch_array($data);
	
	if($data == NULL)
	{
		echo "<script> alert('삭제된 댓글입니다.'); history.back(); </script>";
		exit;
	}


	//삭제 가능한가??
	if($id == "admin" || $id == $data["author"])	//가능함 
	{
		//해당 댓글 삭제함
		$delno = $data["No"];
		$database->query("delete from comment where No = $delno"); //해당 댓글 삭제
		if($data["rpy"] == 0) //case 2 : 댓글 삭제->대댓글 도 삭제, 댓글 rootlist 재정렬
		{
			$rootlist = $data["rootlist"];
			$database->query("delete from comment where rootlist = $rootlist");
			$pile = $database->query("select No, rootlist from comment where postnum=$npost and rootlist > $rlist");
			while($ok = $pile->fetch_array())
			{
				$no = $ok["No"];
				$num = $ok["rootlist"] - 1;
				$database->query("update comment set rootlist = $num where No = $no"); 
			}
		}
		else //case 3 : 대댓글 삭제 -> 해당 대댓글 재정렬
		{
			$rootlist = $data["rootlist"];
			$rpy = $data["rpy"];
			$change = $database->query("select No, rpy from comment where rootlist = $rootlist and rpy > $rpy");
			while($ok = $change->fetch_array())
			{
				$reply = $ok["rpy"] - 1;
				$no = $ok["No"];
				$database->query("update comment set rpy = $reply where No = $no");
			}
		}
	}
	else	//불가능함
	{
		echo "<script> alert('권한이 없습니다.'); location.href='./homepage.html'; </script>";
		exit;
	}

	//No 재정렬
        $sql = "alter table comment auto_increment = 1; set @c=0; update comment set No = @c:=@c+1";
	$database->multi_query($sql);
	echo "<script> history.back(); </script>";
?>
