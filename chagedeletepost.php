<?php
session_start();

if(!isset($_SESSION['islogin']))
{
	echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
	exit;
}

//기본정보 및 데이터베이스에 연결
$idx = $_GET[idx];  //현재 게시글의 고유번호
$database = new mysqli('localhost','kknock','password','kknockhomepage');
$minusnum = -1;  //총 지우기는 게시글의 수

//root index 구하기 -- 게시글 및 답글 삭제용
$rootindex = $database->query("select rootindex from postlist where No ='".$idx."'");
$rootindex = $rootindex->fetch_array();
$rootindex = $rootindex["rootindex"];

//reply index 구하기 -- 게시글인 답글인 확인용
$reply = $database->query("select reply from postlist where No ='".$idx."'");
$reply = $reply->fetch_array();
$reply = $reply["reply"];

//업로드된 파일의 위치
$save_upload_dir = "../uploads/";

if($reply == 0) //게시글 삭제
{
    //rindex 삭제할 게시글들의 고유번호를 저장
    $rindex = $database->query("select No from postlist where rootindex = $rootindex order by No desc");

    $minusnum = $rindex->num_rows;  //삭제할 게시글의 갯수

    $start_index = $idx;    //시작하는 게시글의 고유번호
    $end_index = $rindex;   //끝나는 게시글의 고유번호  --> 댓글 및 업로드한 파일의 정보 수정에 사용함
    $end_index = $end_index->fetch_array();
    $end_index = $end_index['No'];

    //게시글 및 그의 답글 삭제
    $database->query("delete from postlist where rootindex = $rootindex");

    //게시글에서의 고유번호 수정 : 정보 가져오기
    $update = $database->query("select No, rootindex from postlist where rootindex > $rootindex");

    //게시글에서의 고유번호 수정 : 실제로 수정하는 부분
    while($renew = $update->fetch_array()) 
    {
        $no = $renew['No'];
        $new_root = $renew['rootindex'] - 1;

        $database->query("update postlist set rootindex = $new_root where No = $no");
    }

    //삭제한 게시글들의 댓글 삭제




}