<?php
    $files = mysqli_connect('localhost','kknock','Anapple11!','kknockhomepage');
    
	function renow_filename()
	{
        global $files;
        $save_file_dir = "../uploads/";

        $need_change = $files->query("select * from uploadfile where postnum != oldpostnum");

        while($now_change = $need_change->fetch_array())
        {
            $old_name = $now_change['oldpostnum']."(".$now_change['ordernum'].")".$now_change['filename'];
            $old_name = $save_file_dir.$old_name;
            $new_name = $now_change['postnum']."(".$now_change['ordernum'].")".$now_change['filename'];
            $new_name = $save_file_dir.$new_name;

            $new_post_num = $now_change['postnum'];
            $now_file_no = $now_change['No'];

            if(is_file($old_name))
            {
                rename($old_name, $new_name); //파일 이름 변경

                //데이터베이스 업데이트
                $files->query("update uploadfile set oldpostnum = $new_post_num where No = $now_file_no");
            }
            else
            {
                $files->query("delete from uploadfile where No = $now_file_no");
            }
        }

        $sql = "alter table uploadfile auto_increment = 1; set @c=0; update uploadfile set No = @c:=@c+1";	
        $files->multi_query($sql);

        mysqli_close($files);
    }
?>