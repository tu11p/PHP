<?php
session_start();

if(!isset($_SESSION['islogin'])) //로그인 여부 확인하기
{
	echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
	exit;
}

$mode = 1;

$id = $_SESSION['id'];

$database = mysqli_connect('localhost','kknock','password','kknockhomepage');

//새로 작성한 글의 rootindex를 추가하기
$newroot = $database->query("select rootindex from postlist order by rootindex desc limit 1");
$newroot = $newroot->fetch_array();
$newroot = $newroot['rootindex'] + 1;

$database->query("insert into postlist(title, contents, author, rootindex, reply) values('None','None', '$id', '$newroot','0')");

$sql = "alter table postlist auto_increment = 1; set @c=0; update postlist set No = @c:=@c+1";
$database->multi_query($sql);
?>