<?php
//에러 출력용 코드
error_reporting(E_ALL);
ini_set("display_error",1);

session_start();

if(!isset($_SESSION['islogin'])) //로그인 여부 확인하기
{
	echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
	exit;
}

//게시글 관련 : 기본 변수 선언 및 초기화
$id = $_SESSION["id"];
$title = $_POST[title];
$content  = $_POST[content];
$mode = $_POST[mode];
$save = mysqli_connect('localhost','kknock','Anapple11!','kknockhomepage');			//게시글 용
$database = mysqli_connect('localhost','kknock','Anapple11!','kknockhomepage');		//업로드 파일 용
$is_post_upload = 0;

//function about file upload : get user upload file name
function getbasename($path) {
	$pattern = (strncasecmp(PHP_OS, 'WIN', 3) ? '/([^\/]+)[\/]*$/' : '/([^\/\\\\]+)[\/\\\\]*$/');
	if (preg_match($pattern, $path, $matches))
		return $matches[1];
	return '';
}

//파일 업로드 관련 : 기본 변수 선언 및 초기화
$count_file = 0;	//파일 인덱스
$max_size_of_file = 10 * 1024 * 1024; //업로드 가능한 파일 최대 크기
$upload_file_size = 0;	//현재 업로드된 파일의 크기의 합
$before_file_name = array();	//파일의 이름
$after_file_name = array();	//서버에 저장될 파일의 이름
$save_file_dir = "../uploads/"; //업로드한 파일을 저장할 곳

//게시글 확인 하는 부분
if($title == "" || $content == "") //아무것도 입력 안했을 때
{
	if($title == "")
	{
		echo "<script> alert('제목을 입력하세요'); history.back(); </script>";
		exit;
	}
	else
	{
		echo "<script> alert('내용을 입력하세요'); history.back(); </script>";
		exit;
	}
}

//check 1) get number of file uploaded
//		2) is number of file uploaded is 5 or less 
//		3) check uploaded file size is over 10MB
while(isset($_FILES['userfile']['name'][$count_file]) && $_FILES['userfile']['tmp_name'][$count_file] != "")
{
	$err = $_FILES['userfile']['error'][$count_file];
	$size = $_FILES['userfile']['size'][$count_file];

	//파일 업로드 과정(?)에서의 오류 발생여부 확인 
	if($err != 0 )
	{
		echo "<script> alert('파일 업로드에 에러발생 하였습니다.'); history.back(); </script>";
		exit;
	}

	//업로드한 파일이 5개일 이상 일 때
	if($count_file >= 5)
	{
		echo "<script> alert('최대 5개의 파일을 첨부할 수 있습니다.'); history.back(); </script>";
		exit;
	}

	//업도르한 파일의 크기의 총합 및 확인
	$upload_file_size += $size;
	if($upload_file_size > $max_size_of_file)
	{
		echo "<script> alert('첨부 파일의 최대용량은 10 MB 입니다.'); history.back(); </script>";
		exit;
	}

	//현재 터치중인 파일의 변경 전 및 변경 후 이름 저장
	$before_file_name[$count_file] = getbasename($_FILES['userfile']['name'][$count_file]);
	$after_file_name[$count_file] = "(".($count_file + 1).")".$before_file_name[$count_file];

	$count_file++;
}

$get_post_no;
//게시글 저장하기
if($save) //데이터 베이스 접근함
{
	//총 게시글 개수 추출하기
	$count = $save->query("select * from postlist where No = '1'");
	$count = $count->fetch_array();
	$numofpost = $count['contents'] + 1;
		
	//게시글 저장하기
	if($mode == "1") //게시글 작성일 때
	{
		//답글 제외 게시글의 개수 구하기
		$count_root = $save->query("select * from postlist where No >= '2' order by rootindex desc");
		$count_root = $count_root->fetch_array();
		$count_root = $count_root["rootindex"] + 1;
	
		if($save->query("insert into postlist(title, contents, author, rootindex, reply) values('$title','$content','$id','$count_root','0')"))
		{
			//총 게시글의 개수 증가
			$save->query("update postlist set contents ='".$numofpost."' where No ='1'");

			$is_post_upload = 1;
		}
		else //게시글 업로드 실패
		{
			echo "<script> alert('게시글 업로드 실패하였습니다.'); location.href='./boardpage.php?page=1'; </script>";
			exit;
		}

		$get_post_no = $save->query("select No from postlist where rootindex = $count_root");
		$get_post_no = $get_post_no->fetch_array();
		$get_post_no = $get_post_no['No'];
	}
	else //답글 작성일 때
	{
		$postnumber = $_POST[npost];

		//해당 게시글의 답글 제외한 게시글의 순번구하기
		$ccount = $save->query("select * from postlist where No = '".$postnumber."'");
		$ccount = $ccount->fetch_array();
		$ccount = $ccount["rootindex"];
		
		//현재 답글의 순번을 구하기
		$replynum  = $save->query("select reply from postlist where rootindex = '".$ccount."' order by reply desc");
		$replynum = $replynum->fetch_array();
		$replynum = $replynum['reply'] + 1;

		if($save->query("insert into postlist(title, contents, author, rootindex, reply) values('".$title."','".$content."','".$id."','".$ccount."','".$replynum."')"))
		{
			//총 게시글의 개수 증가
			$save->query("update postlist set contents ='".$numofpost."' where No ='1'");

			$is_post_upload = 1;
		}
		else //게시글 업로드 실패
        {
			echo "<script> alert('답글 업로드 실패하였습니다.'); location.href='./boardpage.php?page=1'; </script>";
			exit;
		}

		$get_post_no = $save->query("select No from postlist where rootindex = $ccount and reply = $replynum");
		$get_post_no = $get_post_no->fetch_array();
		$get_post_no = $get_post_no['No'];
	}
}
else //데이터 베이스 접근 못함 -> 서버 문제
{
	echo "<script> alert('나중에 시도해주세요.'); location.href='./homepage.html';</script>";
	exit;
}

//본격적인 파일 업로드 하기
for($i = 0; $i < $count_file; $i++)
{
	$upload_file_dir = $save_file_dir.$get_post_no.$after_file_name[$i];

	if(move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $upload_file_dir))
	{
		$oder = $i + 1;
		$file_name = $before_file_name[$i];
		$file_size = $_FILES['userfile']['size'][$i];
		if(!$database->query("insert into uploadfile(filename, postnum, ordernum, filesize, oldpostnum) values('$file_name','$get_post_no','$oder','$file_size','$get_post_no')"))
		{
			echo "insert uploadfile ";
			echo("쿼리오류 발생: " . mysqli_error($database));
			exit;
		}
	}
	else
	{
		for($j = 0; $j < $i ; $j++)
		{
			$delete_file = $save_file_dir.$after_file_name[$j];

			if(is_file($delete_file))
			{
				$is_del = unlink($delete_file_dir);
			}
		}
		if(!$database->query("delete from postlist where No = $get_post_no"))
		{
			echo "delete postlist ";
			echo("쿼리오류 발생: " . mysqli_error($database));
			exit;
		}
		echo "<script> alert('".$before_file_name[$i]."이 부적절한 파일로 판단됩니다.'); history.back(); </script>";
		exit;
	}
}

//업로드파일의 No 재정렬
$sql = "alter table uploadfile auto_increment = 1; set @c=0; update uploadfile set No = @c:=@c+1";	
$database->multi_query($sql);
//echo("uploadfile No 쿼리오류 발생: " . mysqli_error($database));
//echo "<br>";

//파일 이름 재정령
//미완


//게시글의 No 재정렬
$sql = "alter table postlist auto_increment = 1; set @c=0; update postlist set No = @c:=@c+1";	
$save->multi_query($sql);
//echo("postlist No 쿼리오류 발생: " . mysqli_error($save));
//echo "<br>";

if($is_post_upload)
{
	if($mode == 1)
	{
		echo "<script> alert('게시글 업로드 성공하였습니다.'); location.href='./boardpage.php?page=1'; </script>";
	}
	else
	{
		echo "<script> alert('답글 업로드 성공하였습니다.'); location.href='./boardpage.php?page=1'; </script>";
	}
}

mysqli_close($save);
mysqli_close($database);
?>