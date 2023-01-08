<?php
	$id = $_GET[id];
	$pw = $_GET[pw];
	$rpw = $_GET[rpw];

	if($id == "" || $pw == "" || $rpw == "")
	{
		echo "<script type='text/javascript'> alert('모든 정보를 기입해주세요.');</script>";
                echo "<script> history.back();</script>";
	}
	else if(strlen($pw) < 6) //비밀번호의 길이가 적당한가?
	{
		echo "<script type='text/javascript'> alert('비밀번호 길이가 부족합니다.');</script>";
		echo "<script> location.replace('./joinpage.html');</script>";
	}
	else
	{
		$check = " ";
		$result = strpos($id,$check);
		$rresult = strpos($id, $check);

		if($result != false || $rresult != false)
		{
			echo "<script> alert('입력에러 공백은 입력하지 마세요.'); history.back(); </script>";
		}
		else		
		{
		$indata = new mysqli('localhost', 'kknock', 'password', 'kknockhomepage');

		//check id overlap;
		$find = "select * from idpass where id ='".$id."'";
		$result = $indata -> query($find);

		if($result->num_rows !=0) //아이디가 중복되었을 경우
		{
			echo "<script> alert('아이디가 중복되었습니다.'); history.back();</script>";
		}
		else if($pw == $rpw) //비밀번호가 일치할 경우
		{

			if($indata) //데이터 베이스 존재
			{
				$sql = "insert into idpass(id, password) values";
				$sql = $sql."('".$id."','".$pw."')";

				if($indata -> query($sql)) //해당 테이블 존재-> 데이터 저장
				{
					echo "<script type='text/javascript'> alert('성공적으로 가입하였습니다.');</script>";
					echo "<script> location.replace('./homepage.html');</script>";
				}
				else //해당 테이블 존재 X -> 테이블 생성 후 저장
				{
					$table = "create table idpass(";
					$table = $table."No bigint(20) unsigned not null auto_increment,";
					$table = $table."id varchar(255) not null,";
					$table = $table."password varchar(255) not null,";
					$table = $table."primary key(No)";
					$table = $table.") default character set utf8;";

					if($indata -> query($table)) //테이블 생성 성공
					{
						$indata -> query($sql);
						echo "<script type='text/javascript'> alert('성공적으로 가입하였습니다.');</script>";
                                       		echo "<script> location.replace('./homepage.html');</script>";

					}
					else //테이블 생성 실패 -> 서버 문제
					{
						echo "<script type='text/javascript'> alert('서버오류 : 추후에 시도해주세요.');</script>";
						echo "<script> location.replace('./homepage.html');</script>";

					}

				}

			}
			else //데이터 베이스 존재 X -> 서버 문제
			{
			       	echo "<script type='text/javascript'> alert('서버가 준비 되지 않았습니다.');</script>";
				echo "<script> location.replace('./homepage.html');</script>";

			}
		}
		else //비밀번호가 일치 하지 않을 경우
		{
			echo "<script type='text/javascript'> alert('비밀번호가 일치하지 않습니다.');</script>";
			echo "<script> location.replace('./joinpage.html');</script>";

		}
		}
	}
?>
