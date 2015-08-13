<?php
$db = mysql_connect ("localhost","root","");
mysql_select_db("gorodina",$db);

mysql_query('SET NAMES utf8');
mysql_query ("set character_set_results='utf8'");
?>