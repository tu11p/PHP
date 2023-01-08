<?php
session_start();
$index = $_GET[idx];
$writer = $_GET[write];
$viewer = $_SESSION["id"];
$page = $_GET[page];
$term = $_GET[term];
$seek = $_GET[search];

if(!isset($_SESSION['islogin']))
{
	echo "<script> alert('로그인이 필요합니다.'); location.href='./homepage.html'; </script>";
	exit;
}

$database = new mysqli('localhost','kknock','password','kknockhomepage');

//클라이언트가 요청한 게시글 정보
$now_title = $database->query("select * from postlist where No ='".$index."'");
$now_title = $now_title->fetch_array();

//클라이언트가 요청한 게시글 댓글 정보
$tcomment = $database->query("select * from comment where postnum ='".$index."' order by rootlist asc, rpy asc");
$count = $tcomment->num_rows;

//클라이언트가 요청한 게시글 첨부파일 정보
$uploaded_file = $database->query("select * from uploadfile where postnum = $index");
$count_file = $uploaded_file->num_rows;
$num_of_file = 1;
?>

<!doctype html>
<html>
	<head>
		<title> <?php echo $now_title["title"]; ?> </title>
		<style tyep="text/css">
			div#contents{
				border: 1px;
				background-color: #FAE7E7;
				width: 1000px;
				height: <?php $h = 1100+$count*100; echo $h."px;"; ?>
				max-width: 100%;
				max-height: 100%;
				margin-left: auto;
				margin-right: auto;
				margin-top: 10px;
			}
		</style>
		<style type="text/css">
			table#mainboard{
				border: 1px solid black;
				border-collapse: collapse;
				width: 80%;
				margin-left: auto;
				margin-right: auto;
				height: 500px;
				max-height: 100%;
			}
			th, td{
				border: 1px solid black;
				text-align: center;
				padding: 5px;
			}
		</style>
		<script type="text/javascript">
			function check_delete(){	//게시글 삭제 권한여부
				<?php
				if($viewer==$writer || $viewer=="admin")
				{
					echo "var post_delete = confirm('정말로 삭제하겠습니까?');";
				}
				else
				{
					echo "alert('권한이 없습니다.');";
				}
				?>
				if( typeof(post_delete) != 'undefined')
				{
					if(post_delete == true)
					{
						location.href='./deletepost.php?idx=<?php echo $index; ?>';
					}
				}
			}
			function check_modify(){	//게시글 수정 권한 여부
				<?php
				if($viewer==$writer || $viewer=="admin")
				{
					if(isset($_GET[term]))
					{
						echo "location.href='./modifypost.html?idx=$index&page=$page&term=$term&search=$seek&write=$writer'";
					}
					else
					{
						echo "location.href='./modifypost.html?idx=$index&page=$page'";
					}
				}
				else
				{
					echo "alert('권한이 없습니다.');";
				}
				?>
			}
			function modifycomment(textareaid, formid, rootindex, rpy){		//댓글 및 대댓글 수정
				//document.getElementById -> 어떤 개체의 정보를 가져온다.
				//document.createElement -> 개체를 생성함.
				//setAttribute -> 해당 개체에 특성(?)을 부여및 수정함
				var mytextarea = document.getElementById(textareaid);
				var myformid = document.getElementById(formid);
				mytextarea.style = "border:1px;width:99%;resize:none";
				mytextarea.readOnly = false;
				mytextarea.focus();

				var button = document.createElement("input");
				var button1 = document.createElement("input");
				button.setAttribute("type","submit");
				button.setAttribute("value","저장");
				button1.setAttribute("type","button");
				button1.setAttribute("value","취소");
				button1.setAttribute("onclick","history.go(0);");

				myformid.setAttribute("method","POST");
				myformid.setAttribute("action","./modifycomment.php");

				//hidden input : 필수
				var index = document.createElement("input");
				index.setAttribute("type","hidden");
				index.setAttribute("name","npost");
				index.setAttribute("value","<?php echo $index; ?>");

				var rindex = document.createElement("input");
				rindex.setAttribute("type","hidden");
				rindex.setAttribute("name","rootindex");
				rindex.setAttribute("value",rootindex);

				var reply = document.createElement("input");
				reply.setAttribute("type","hidden");
				reply.setAttribute("name","rpy");
				reply.setAttribute("value",rpy);

				myformid.appendChild(index);	//npost
				myformid.appendChild(rindex);	//rootindex
				myformid.appendChild(reply);	//reply
				myformid.appendChild(button);	//저장 버튼
				myformid.appendChild(button1);	//취소 버튼
				//cotent 본문에 존재

				myfromid.submit();
			}
			function deletecomment(npost, root,rpy){	//댓글 및 대댓글 삭제

				var commend_delete = confirm('정말로 삭제하겠습니까?');

				if(commend_delete == true)
				{
					location.href = "./deletecomment.php?npost="+npost+"&root="+root+"&rpy="+rpy+"";
				}
			}
			function writeccomment(formid, rootlist){	//대댓글 작성
				var form_id = document.getElementById(formid);
				var ccmt = document.createElement("form");
				ccmt.setAttribute("charset","UTF-8");
				ccmt.setAttribute("method","POST");
				ccmt.setAttribute("action","./savecomment.php");

				var button = document.createElement("input");
                var button1 = document.createElement("input");
                button.setAttribute("type","submit");
				button.setAttribute("value","저장");
				button1.setAttribute("type","button");
				button1.setAttribute("value","취소");
				button1.setAttribute("onclick","history.go(0);");


				var tarea = document.createElement("textarea");
				tarea.style = "border:1px;width:99%;resize:none";
				tarea.readOnly = false;
				tarea.setAttribute("name","content");
			
				var mode = document.createElement("input");
				mode.setAttribute("type","hidden");
				mode.setAttribute("name","mode");
				mode.setAttribute("value","2");
			
				var rlist = document.createElement("input");
				rlist.setAttribute("type","hidden");
				rlist.setAttribute("name","rlist");
				rlist.setAttribute("value",rootlist);

				var post = document.createElement("input");
				post.setAttribute("type","hidden");
				post.setAttribute("name","postnum");
				post.setAttribute("value",<?php echo $index; ?>);


				form_id.appendChild(ccmt);
			
				ccmt.appendChild(tarea);
				ccmt.appendChild(button);
				ccmt.appendChild(button1);	
				ccmt.appendChild(mode);
				ccmt.appendChild(rlist);
				ccmt.appendChild(post);
			}
		</script>
	</head>
	<body>
	<div id="contents">
		<!-- 게시글 출력 -->
		<table id="mainboard">
			<thead>
				<tr>
				<th style="width:500px; max-width: 100%; height: 10px; max-heigth:100%;"><?php echo $now_title["title"]; ?></th>
				<th style="width:100px; max-width: 100%; height: 10px; max-height:100%;"><?php echo $now_title["author"]; ?></th>
				</tr>
			</thead>
			<tbody>
			<tr>
			<br> <br>
			<td colspan="2"> <textarea wrap="virtual" style="width: 750px; height: 600px; max-width:100%; max-height:100%; border: 0px; resize: none; background-color: #FAE7E7; margin-top:5px;" readonly><?php echo $now_title["contents"]; ?></textarea></td>
			</tr>
			</tbody>
		</table>

		<!-- 첨부파일 있으면 출력 없으면 X : 미완 파일 다운로드 설정한함-->
		<center>
		<?php if($count_file){ ?>
		<table style="border:0px;border-collapse:collapse;width:750px;max-width:100%">
		<h3> 첨부파일 </h3>
		<?php while($info = $uploaded_file->fetch_array()){ ?>
		<tbody>
			<tr>
				<td width="100px" style="text-align:left;">
					<form action="./filedown.php" method="get" target="aboutfile">
						<input type="hidden" name="number" value="<?php echo $info['No']; ?>">
						<?php echo $num_of_file."."; ?>
						<input type="submit" value="<?php echo $info['filename']; ?>" >
					</form> 
				</td>
			</tr>
		</tbody>
		<?php $num_of_file++; } ?>
		</table>
		<?php } ?>
		</center>
		<br>

		<!-- 게시글 관련 버튼 출력 : 삭제 , 수정 , 목록으로, 답글 작성 -->
		<center>
		<?php
		$reply = $now_title["reply"];
		if($reply == 0)
		{ ?>
			<button style="margin-top:5px;" onclick="location.href='./writepost.html?mode=2&npost=<?php echo $index; ?>'">답글 쓰기</button>
		<?php } ?>
		<button style="margin-top:5px;" onclick="location.href='./boardpage.php?page=<?php echo $page; if(isset($_GET[term])) {echo "&term=$term&search=$seek"; } ?>'">목록</button>
		<button style="margin-top:5px;" onclick="check_delete()" > 삭제 </button>
		<button style="margin-top:5px;" onclick="check_modify()"> 수정 </button>
		</center>
		<br>

		<!-- 댓글 입력 창 및 댓글 출력-->
		<center>
		<table style="height:10px; max-height:100%; width:780px; max-width:100%; border:0px;">
			<thead>
				<form action="./savecomment.php" method="POST">
					<input type="hidden" name="postnum" value="<?php echo $index; ?>" >
					<input type="hidden" name="mode" value="1">
					<tr>
						<th style="border:0px;"> <textarea name="content" wrap="virtual" placeholder="댓글을 입력하세요." maxlength="100" cols="40" rows="3" style="resize:none;width:660px;max-width:100%;"></textarea></th>
						<th style="text-align:left;border:0px;"> <input type="submit" value="등록"> </th>
					</tr>
				</form>
			</thead>
		</table>
		
		<!-- 댓글 출력하기 -->
		<table style="border:0px;border-collapse:collapse;width:750px;max-width:100%">
		<?php while($info = $tcomment->fetch_array()){ ?>
		<tbody>
			<tr>
				<td width="100px" style="text-align:center;word-break:break-all"><?php if($info["rpy"] != 0){echo "┗&nbsp;";} echo "<b>".$info["author"]."</b>"; ?> </td>

				<td style="text-align:center;">
					<form id="form<?php echo $info["No"]; ?>">
						<textarea id="comment<?php echo $info["No"]; ?>" name="content" wrap="virtual" style="border:0px;width:99%;resize:none;background-color: #FAE7E7;" readonly><?php echo $info["content"]; ?></textarea>
					</form>
 				</td>

				<td width="100px" style="word-break:break-all;" >
				<?php if($info["rpy"] == 0){ ?>
					<button type='button' style='margin-top:1px;' onclick="writeccomment('form<?php echo $info["No"]; ?>','<?php echo $info["rootlist"]; ?>')">답변</button><br>
				<?php } if($info["author"] == $viewer || $viewer == "admin"){ ?>
					<button type='button' style='margin-top:1px;' onclick="modifycomment('comment<?php echo $info["No"]; ?>','form<?php echo $info["No"]; ?>','<?php echo $info["rootlist"]; ?>','<?php echo $info["rpy"]; ?>')">수정</button><br>
					<button type='button' style='margin-top:1px;' onclick="deletecomment(<?php echo $index; ?>,<?php echo $info["rootlist"]; ?>,<?php echo $info["rpy"]; ?>)">삭제</button>
				<?php } ?>
				</td>
			</tr>
		</tbody>
		<?php } ?>
		</table>
		</center>
	</div>
	<iframe name="aboutfile" style="width:0px; height: 0px; border: 0px">유감이요. 당신은 해당 기능을 사용못합니다.</iframe>
	</body>
</html>
