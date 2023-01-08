<?php
include $_SERVER['DOCUMENT_ROOT']."/Renew_fileName.php";

session_start();

if(!isset($_SESSION['islogin']))
{
	echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
	exit;
}

$now_user = $_SESSION['id'];    //현재 해당 요청을 한 사용자
$idx = $_GET[idx];              //게시글의 고유번호 가져오기 : No

//변경할 데이터 베이스
$database_postlist = mysqli_connect('localhost','kknock','password','kknockhomepage');    //postlist 변경용 데이터 베이스 변수
$database_comment = mysqli_connect('localhost','kknock','password','kknockhomepage');     //comment 변경용 데이터 베시스 변수
$database_uploadfile = mysqli_connect('localhost','kknock','password','kknockhomepage');  //uploadfile 변경용 데이터 베이스 변수

//현재 게시글의 정보를 가져온다.
$infor_post = $database_postlist->query("select * from postlist where No = $idx");
$infor_post = $infor_post->fetch_array();

if($now_user != $infor_post['author']) //라마미터 변형이 있었는지 확인하기
{
    echo "<script> alert('주소를 파라미터 맘대로 바꾸지 마세요.'); location.href='./logout.php'; </script>";
    exit;
}

//rootindex를 구한다 : 게시글 및 답글 삭제용
$post_root_index = $infor_post['rootindex'];

//reply를 구한다 : 게시글 인지 답글 판별용 : 답글 삭제용
$post_reply = $infor_post['reply'];

//upload file 디렉토리
$save_file_dir = '../uploads/';

if($post_reply == 0) //게시글 삭제
{
	$will_del_post = $database_postlist->query("select * from postlist where rootindex = $post_root_index"); //삭제할 post의 정보 모음
    $del_post_num = $will_del_post->num_rows;   //삭제 될 post의 개수

    //파일 삭제하기
    while($delete = $will_del_post->fetch_array())
    {
        $now_post_no = $delete['No']; //현재 게시글의 No

		$delete_file = $database_uploadfile->query("select * from uploadfile where postnum = $now_post_no");

        while($now_file = $delete_file->fetch_array())
        {
            $delete_file_name = $now_file['postnum']."(".$now_file['ordernum'].")".$now_file['filename'];

            $delete_file_dir = $save_file_dir.$delete_file_name;

            if(is_file($delete_file_dir))
            {
                unlink($delete_file_dir);
            }
        }
	}
	
	//게시글 삭제하기 -> comment 데이터베이스 내용 삭제됨 , uploadfile 데이터베이스 내용 삭제됨
	$database_postlist->query("delete from postlist where rootindex = $post_root_index");

	//게시글 rootindex 감소시키기
	$minus_root = $database_postlist->query("select No, rootindex from postlist where rootindex > $post_root_index");

	while($do_minum = $minus_root->fetch_array())
	{
		$now_change_no = $do_minum['No'];
		$new_root = $do_minum['rootindex'] - 1;

		$database_postlist->query("update postlist set rootindex = $new_root where No = $now_change_no");
	}

	//총 게시글의 수를 수정한다.
	$now_total_post = $database_postlist->query("select * from postlist where No = '1'");
	$now_total_post = $now_total_post->fetch_array();
	$now_total_post = $now_total_post['contents'];
	$now_total_post = $now_total_post - $del_post_num;

	$database_postlist->query("update postlist set contents = '$now_total_post' where No = '1'");
}
else    //답글 삭제
{
	$del_post = $database_postlist->query("select * from postlist where rootindex = $post_root_index and reply = $post_reply");
	$del_post = $del_post->fetch_array();

	//업로드한 파일 삭제
	$del_file_num = $del_post['No'];
	$delete_file = $database_uploadfile->query("select * from uploadfile where postnum = $del_file_num");

	while($delete = $delete_file->fetch_array())
	{
		$delete_file_name = $delete['postnum']."(".$delete['ordernum'].")".$delete['filename'];
		
		$delete_file_dir = $save_file_dir.$delete_file_name;
		
		if(is_file($delete_file_dir))
		{
			unlink($delete_file_dir);
		}
	}

	//해당 답글 삭제
	$database_postlist->query("delete from postlist where rootindex = $post_root_index and reply = $post_reply");

	//해당 답글 아래 글들의 reqly들을 1씩 감속한다.
	$update_rpys = $database_postlist->query("select * form postlsit where rootindex = $post_root_index and reply > $post_reply");

	while($update_reply = $update_rpys->fetch_array())
	{
		$now_no = $update_reply['No'];
		$new_rpy = $update_reply['reply'] - 1;

		$database_postlist->query("update postlist set reply = $new_rpy where No = $now_no");
	}

	//총 게시글의 수를 수정한다.
	$now_total_post = $database_postlist->query("select * from postlist where No = '1'");
	$now_total_post = $now_total_post->fetch_array();
	$now_total_post = $now_total_post['contents'];
	$now_total_post = $now_total_post - 1;

	$database_postlist->query("update postlist set contents = '$now_total_post' where No = '1'");
}

//업로드한 파일의 이름을 rename한다.
renow_filename();

//게시글 No 재정렬
$sql = "alter table postlist auto_increment = 1; set @c=0; update postlist set No = @c:=@c+1";	
$database_postlist->multi_query($sql);

//댓글 No 재정렬
$sql = "alter table comment auto_increment = 1; set @c=0; update comment set No = @c:=@c+1";	
$database_comment->multi_query($sql);

//업로드파일의 No 재정렬
$sql = "alter table uploadfile auto_increment = 1; set @c=0; update uploadfile set No = @c:=@c+1";	
$database_uploadfile->multi_query($sql);


mysqli_close($database_postlist);
mysqli_close($database_comment);
mysqli_close($database_uploadfile);

echo "<script> alert('해당 게시글 삭제하였습니다.'); location.href='./is_login.php?locate=3&page=1'; </script>";

?>