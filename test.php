<?php
$text = 'All people is are be more then before what is are the most inportant';
$is_have = preg_match('/are/',$text, $matching);

echo "preg_match 리턴형 : ".gettype($is_have)."<br>";
echo "is_have 값 : ".$is_have."<br>";
echo "matching : ";
var_dump($matching);

?>
