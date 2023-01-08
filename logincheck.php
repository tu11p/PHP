<?php
  session_start();
  $id = $_GET[id];
  $pw = $_GET[pw];

  if($id == "" || $pw == "") //입력 오류
  {
	  if($id == "")
	  {
		  echo "<script> alert('아이디를 입력하세요.'); location.href='./loginpage.html';</script>"; 
	  }
	  else
	  {
		  echo "<script> alert('비밀번호를 입력하세요.'); location.href='./loginpage.html';</script>";
	  }

  }
  else //제대로 입력함
  {
	  $db = mysqli_connect('localhost','kknock','password','kknockhomepage');

	  if($db) //데이터 베이스에 연결 성공
	  {
		  $sql = "select password from idpass where id ='".$id."'";
		  $data = $db->query($sql);


		  if($data->num_rows == 0) //사용자가 존재 X
		  {
			  mysqli_close($db);
			  echo "<script> alert('사용자가 존재하지 않습니다.'); location.href='./loginpage.html';</script>";
		  }
		  else //사용자가 존재 O
		  {
			 $password = $data->fetch_array();
	
			 if($pw == $password['password']) //비밀번호 일치
			 {
				 $_SESSION["islogin"] = "YES";
				 $_SESSION["id"] = "$id";
				 $_SESSION["pw"] = "$pw";
				 echo "<script> alert('로그인성공'); location.href='./homepage.html';  </script>";
			 }
		 	 else //비밀번호 불일치
			 {
				 mysqli_close($db);
				 echo "<script> alert('비밀번호를 잘못 기입하였습니다.'); location.href='./loginpage.html';</script>";
		  	 }
		  }
	  }
	  else //데이터 베이스에 연결 실패
	  {
		  mysqli_close($db);
		  echo "<script> alert('서버가 준비되지않았습니다.'); location.href='./homepage.html'; </script>";
	  } 
  }
?>
