<?php
session_start();

if(!isset($_SESSION['islogin']))
{
	echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
	exit;
}

$now_user = $_SESSION['id'];    //현재 해당 요청을 한 사용자
$idx = $_GET[idx];              //게시글의 고유번호 가져오기 : No

//변경할 데이터 베이스
$database_postlist = mysqli_connect('localhost','kknock','Anapple11!','kknockhomepage');    //postlist 변경용 데이터 베이스 변수
$database_comment = mysqli_connect('localhost','kknock','Anapple11!','kknockhomepage');     //comment 변경용 데이터 베시스 변수
$database_uploadfile = mysqli_connect('localhost','kknock','Anapple11!','kknockhomepage');  //uploadfile 변경용 데이터 베이스 변수

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
}
else    //답글 삭제
{

}


mysqli_close($database_postlist);
mysqli_close($database_comment);
mysqli_close($database_uploadfile);

?>