<?php
error_reporting(E_ALL);
ini_set("display_error",1);

function getbasename($path) {
	$pattern = (strncasecmp(PHP_OS, 'WIN', 3) ? '/([^\/]+)[\/]*$/' : '/([^\/\\\\]+)[\/\\\\]*$/');
	if (preg_match($pattern, $path, $matches))
		return $matches[1];
	return '';
}

$numPost = $_POST[npost];
$now_file = $_POST[filenum];
$count = count($_FILES['userfile']['name']);
$max_size_of_file = 10 * 1024 * 1024; //업로드 가능한 파일 최대 크기
$upload_file_size = 0;	//현재 업로드된 파일의 크기의 합
$before_file_name = array();	//파일의 이름
$after_file_name = array();	//서버에 저장될 파일의 이름
$save_file_dir = "/var/www/uploads/"; //업로드한 파일을 저장할 곳
$database = new mysqli('localhost', 'kknock', 'password', 'kknockhomepage');

if(!$database)
{
	echo "<script> alert('에러발생 잠시후에 시도해주세요.'); location.href='./homepage.html'; window.self.close(); </script>";
	exit;
}

//업로드한 파일이 5개일 이상 일 때
if($count + $now_file > 5)
{
	echo "<script> alert('최대 5개의 파일을 첨부할 수 있습니다.'); history.back(); window.self.close(); </script>";
	exit;
}

for($i = 0; $i < $count; $i++)
{
	$err = $_FILES['userfile']['error'][$i];
	$size = $_FILES['userfile']['size'][$i];

	//파일 업로드 과정(?)에서의 오류 발생여부 확인 
	if($err != 0 )
	{
		echo "<script> alert('파일 업로드에 에러발생 하였습니다.'); history.back(); window.self.close(); </script>";
		exit;
	}

	//업도르한 파일의 크기의 총합 및 확인
	$upload_file_size += $size;
	if($upload_file_size > $max_size_of_file)
	{
		echo "<script> alert('첨부 파일의 최대용량은 10 MB 입니다.'); history.back(); window.self.close(); </script>";
		exit;
	}

	//현재 터치중인 파일의 변경 전 및 변경 후 이름 저장
	$before_file_name[$i] = getbasename($_FILES['userfile']['name'][$i]);
	$after_file_name[$i] = $numPost."(".($now_file + $i + 1).")".$before_file_name[$i];
}

for($i = 0; $i < $count; $i++)
{
	$upload_file_dir = $save_file_dir.$after_file_name[$i];

	if(move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $upload_file_dir))
	{
		$size = $_FILES['userfile']['size'][$i];
		$oder = $now_file + $i + 1;
		$upload_query = "insert into uploadfile(filename, postnum, ordernum, filesize, oldpostnum) values('".$before_file_name[$i]."','".$numPost."','".$oder."','".$size."','".$numPost."')";
		$check = $database->query($upload_query);
		if(!$check)
		{
			echo "<script> alert('잠시후에 시도해 주세요'); loaction.href='./homepage.html'; window.self.close(); </script>";
		}
	}
	else
	{
		echo "<script> alert('파일 업로드 에러'); </script>";
		for($j = 0; $j < $i; $j++)
		{
			$oder = $now_file + $j + 1;
			$delete_file_dir = $save_file_dir.$after_file_name[$j];

			//데이터 베이스에서 삭제
			$upload_query = "delete from uploadfile where postnum ='".$numPost."' and ordernum ='".$j."'";
			$check = $database->query($upload_query);
			if(!$check)
			{
				echo "<script> alert('잠시후에 시도해 주세요'); loaction.href='./homepage.html'; </script>";
			}

			//서버에서 삭제
			if(!unlink($delete_file_dir))
			{
				echo "해당 파일 존재하지 않습니다.";
			}
		}
	}
}

$sql = "alter table uploadfile auto_increment = 1; set @c=0; update uploadfile set No = @c:=@c+1";
$database->multi_query($sql);
?>
<!doctype html>

<html>
<head>
	<title> hello </title>
	<script>
		//팝업창에서의 함수 호출 : opener.location.href = "javascript:함수이름();";
		//iframe에서의 함수 호출 : parent.함수이름();
		parent.myReload();
	</script>
</head>
</html>


