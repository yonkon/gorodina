<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Выбрать город России. Карты, панорамы, фото и пробки.</title>
<meta name="description" content="Путешествие по городам Росии по карте с фото. Панорамы красивейших городов." />
<meta name="keyword" content="Фото городов россии карта пробки" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
<? include ("block/header.php");?>
<? include ("block/nav.php");?>
<div class="content">
<h1>Выберите город России:</h1>
 <div class="goroda"><?
  include ("block/bd.php");
	$result3 = mysql_query("SELECT * FROM citys ORDER BY name");
			if (!$result3)
				{
				echo "<p>Запрос на выборку данных из базы не прошел. Напишите об этом администратору 123@123.com. <br> <strong>Код ошибки:</strong></p>";
				exit(mysql_error());
				}

			if (mysql_num_rows($result3) > 0)
			{
			$myrow3 = mysql_fetch_array($result3);
				do 
				{
				printf ("<a href='/city/%s'>%s</a> ",$myrow3["chpu"],$myrow3["name"]);
				}
				while ($myrow3 = mysql_fetch_array($result3));
			}
			else{echo "<p>Информация по запросу не может быть извлечена в таблице нет записей.</p>";exit();}
  ?></div>
<div id="clear"> </div>
</div>
<? include ("block/footer.php");?>
		  </div><!-- #content-->

</body>
</html>
