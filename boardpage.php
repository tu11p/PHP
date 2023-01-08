<?php include $_SERVER['DOCUMENT_ROOT']."/showpost.php"; 
	$page = $_GET[page];
	$term = $_GET[term];
	$seek = $_GET[search];

	//로그인 체크
	if(!isset($_SESSION['islogin']))
	{
		echo "<script> alert('로그인이 안되어 있습니다.'); location.href='./homepage.html'; </script>";
		exit;
	}

	$postdata;
	$maxpage;
	$start = ($page - 1) * 10;

	if(isset($_GET[term])) //게시글 검색 누름
	{
		$sql = "select * from postlist where $term like '%$seek%' and No >= '2' order by rootindex desc, reply asc limit $start, 10";
		$postdata = showpost($sql);
		$maxpage = returnmaxpage("select * from postlist where $term like '%$seek%'",2);
	}
	else //게시글 검색 누름 X
	{
		$sql = "select * from postlist where No >= '2' order by rootindex desc, reply asc limit $start, 10";
		$postdata = showpost($sql);
		$maxpage = returnmaxpage("select contents from postlist where No ='1'",1);
	}
?>
<!doctype html>
<html>
	<head>
		<title> 게시판 </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8">
		<style type="text/css">
				div#sidebar{
				border: 1px solid black;
				width: 100px;
				max-width: 100%;
				height: 300px;
				max-height: 100%;
				background-color: #FAF7F7;
				float: left;
				margin-left: 5%;
				margin-top: 20px;
			}
			#contents{
				border: 1px solid black;
				background-color: #F3F0F0;
				width: 800px;
				max-width: 100%;
				height: 550px;
				max-height: 100%;
				margin-left: auto;
				margin-right: auto;
			}
		</style>
		<style type="text/css">
			table{
				border: 1px solid black;
				border-collapse: collapse;
				width: 80%;
				margin-left: auto;
				margin-right: auto;
			}
			th, td{
				border-collapse: collapse;
				border: 1px solid black;
				text-align: center;
				padding: 5px;
			}
			li{
				display: inline;
			}
		</style>
	</head>

	<body>
		<h1> <center> <a href="./homepage.html" title="HLhomepage" style="text-decoration:none;color:black;" > Eunsu Kim </a> <center></h1>
		<div id="sidebar">  <!-- 사이드바 부분 -->
		<center>
			<br>
			<?php
  				$id = $_SESSION["id"];
		     		echo $id." ";
			?>

			님 <br> 환영합니다.
			<br><br>
			<button type="button" style="width: 90px; border: 1px solid black; height:60px;max-width:100%;max-height:50%; " onclick="location.href='./writepost.html?mode=1'" >게시글 <br> 작성하기 </button>
			<br><br>

			<button type="button" style="width: 90px; border: 1px solid black; height:90px;max-width:100%;max-height:50%; border-radius:50%;" onclick="location.href='./logout.php'" >로그<br> 아웃 </button>
		</center>
		</div>
		<div id="contents"> <!-- 본격 게시판 -->
		<center>
			<br>
			<table class="table">
				<thead>
					<tr>
						<th> No. </th>
						<th> 제목 </th>
						<th> 작성자 </th>
					</tr>
				</thead>
				<?php 
					//게시글 출력하기//
					while($letter = $postdata->fetch_array())
					{
						$title = $letter["title"];
						$number = $letter["No"] - 1;
						$reply = $letter["reply"];
						if(strlen($title) > 30)
						{
							$title = str_replace($letter["title"],mb_substr($letter["title"],0,20,"utf-8")."...",$letter["title"]);
						}
					
				?>
				<tbody>
					<tr>
					<th> <?php echo $number; ?> </th>
					<th> <a href="./readpost.php?idx=<?php echo $letter["No"]; ?>&write=<?php echo $letter["author"]; ?>&page=<?php echo $page; if(isset($_GET[term])) {echo "&term=".$term."&search=".$seek;} ?>" style="text-decoration:none;color:black;"> <?php if($reply != 0){echo "┗RE&nbsp;:&nbsp;";} echo $title; ?> </a></th> <!-- 글제목에 하이퍼링크 -->
					<th> <?php echo $letter['author']; ?> </th>
					</tr>
				</tbody>
				<?php } ?>
			</table>
			<ul >
			<button type="button" style="margin-right: 5px;" onclick="location.href='./pagemove.php?condition=M&page=<?php echo $page; if(isset($_GET[term])){ echo "&term=$term&search=$seek";}?>&maxpage=<?php echo $maxpage; ?>'"> &lt;&nbsp;prev </button>
				<?php
				//페이지 출력하기			
				$startpage;
				$endpage;
				//시작할 페이지 결정
				if($page <= 3)
				{
					$startpage = 1;
				}
				else
				{
					$startpage = $page; //페이지가 1일 때
				}
				//마지막 페이지 결정
				if($page ==  $maxpage || $page + 1 == $maxpage || $page + 2 == $maxpage)
				{
					$endpage = $maxpage;
				}
				else if($maxpage==0)
				{
					$endpage = 1;
				}
				else
				{
					$endpage = $page + 2;
				}

				for($count = $startpage; $count <= $endpage; $count++)
				{
			 ?>	
			<li> <a style="text-decoration:none;color:black;" href="./boardpage.php?page=<?php echo $count; if(isset($_GET[term])){ echo "&term=$term&search=$seek"; }?>"> <?php if($count == $page){ ?> <b> <?php echo $count; ?> </b> <?php }else{echo $count;}  ?> </a> </li>
			<?php } ?>
			<button type="button" style="margin-left: 5px;" onclick="location.href='./pagemove.php?condition=P&page=<?php echo $page; ?>&maxpage=<?php echo $maxpage; if(isset($_GET[term])){ echo "&term=$term&search=$seek";} ?>'">next&nbsp;&gt; </button>
			</ul>
		</center>
		<center>
		<br>
		<form action="./boardpage.php" method="GET">
			<input type="hidden" name="page" value="1"  >
		<select  name="term">
			<option name="title" value="title" > title </option>
			<option name="content" value="contents" > content </option>
			<option name="author" value="author" > author </option>
		</select>
		<input type="text" maxlength="15" name="search" value="<?php if(isset($_GET[search])){echo $seek;}?>">
		<input type="submit" value="조회">
		</form>
		<center>
		</div>
	</body>
</html>
