﻿<?php
include ("../block/bd.php");
 $urlka = $_SERVER['REQUEST_URI'];

 if (preg_match ("/([^a-zA-Z0-9\.\/\-\_\#])/", $urlka)) {

   header("HTTP/1.0 404 Not Found");
   echo "<h1 align='center'>Запрещенные символы в адресе URL <a href='/'>НА ГЛАВНУЮ</a></h1>";
   exit;
 }

$urlka = str_replace('/panoram/', '',$urlka); /* удалил слеш “/” в начале файлы */


$result = mysql_query("SELECT * FROM citys WHERE chpu='$urlka'");      
$myrow = mysql_fetch_array($result);

//Проверяем кол-во заисей в идеале 1, если 0 то страницы не существует
if (empty($myrow)) {echo "Страницы не сущетвует ";  exit;}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Панорама - <?=$myrow['rpadej'];?>.Фото панорама улиц и дорог города - <?=$myrow['name'];?> (<?=$myrow['region'];?>)</title>
<meta name="description" content="Смотреть на карте панораму <?=$myrow['rpadej'];?>. Панорамы карт Гугл, увидеть <?=$myrow['name'];?> со спутника" />
<meta name="keyword" content="<?=$myrow['name'];?>, <?=$myrow['region'];?>, панорама, фото, карта, улиц, дорог" />
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../favicon.ico" />
 <style>
       #map-canvas {
        height: 450px;
        margin: 0px;
        padding: 0px;
		float:left;
      }
    </style>
	
 <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
function initialize() {
  var fenway = new google.maps.LatLng(<?=$myrow['kor2'];?>, <?=$myrow['kor1'];?>);
  var mapOptions = {
    center: fenway,
    zoom: 14
  };
  var map = new google.maps.Map(
      document.getElementById('map-canvas'), mapOptions);
  var panoramaOptions = {
    position: fenway,
    pov: {
      heading: 34,
      pitch: 10
    }
  };
  var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);
  map.setStreetView(panorama);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
</head>
<body>
<? include ("../block/header.php");?>
<? include ("../block/nav.php");?>

<div class="content">

<div class="block_main">
<h1>Панорама города <?=$myrow['name'];?></h1>
<div id="map-canvas" style="width: 590px; height: 300px"></div>
 <div id="pano" style=" width: 590px; height: 350px;"></div>

</div>

<div class="block_mwater">
 <div align="center"><a href="/city/<?=$myrow['chpu'];?>"><div class="h2">Городской сайт</div>
<img width="120px" align="center" height="150px" align="left" src="../im/logo/<?=$myrow['chpu'];?>.png" /></div></a>
</div>
<noindex>

<div class="block_mwater">
<div class="h2">Фото:</div>
<p><a href="../photo/<?=$myrow['chpu'];?>">Смотреть фото города</br>
<img width="260px" height="110px" src="../im/pncity/<?=$myrow['chpu'];?>.jpg" /></a></p>
</div>

<div class="block_mwater">
 <div class="h2">Погода</div>
<div align="center"><img  src="//info.weather.yandex.net/<?=$myrow['news'];?>/2.ru.png?domain=ru" border="0" alt="Яндекс.Погода"/><img width="1" height="1" src="https://clck.yandex.ru/click/dtype=stred/pid=7/cid=1227/*https://img.yandex.ru/i/pix.gif" alt="" border="0"/> </div>
</div>

<div class="block_mwater">
<div class="h2">Пробки:</div>
 <div align="center"><a href="../probki/<?=$myrow['chpu'];?>">Узнать пробки
 <img src="../im/probki.jpg"/>
 </a></div>
</div>
</noindex>
<div style="clear:left"> </div>
<div class="block_mwater">
<div class="h2">О городе:</div>
<p align="justify" style="color:#545454; font-size:12px"><?=$myrow['desk'];?></p>
</div>

<noindex>
<div class="block_mwater">
<div class="h2">Карта города:</div>
<p align="center"><a href="../map/<?=$myrow['chpu'];?>">Яндекс Карта <?=$myrow['rpadej'];?></ br>
<img width="260px"  height="80px" src="../im/map.gif" /></a></p>
</ br>

<div class="h2">Показать на карте <?=$myrow['rpadej'];?>:</div>
	<td>
	<a href="../avtomoyka/<?=$myrow['chpu'];?>">Автомойки</a><br />
	<a href="../sto/<?=$myrow['chpu'];?>">Автосервисы</a><br />
	<a href="../parking/<?=$myrow['chpu'];?>">Автопарковки</a><br />
<!--Аэропорт<br />
Банкомат<br />
Банк<br />
Бар<br />
Автовокзал<br />
Кафе<br />
Железнодорожная станция<br />
Почта<br />
Ресторан<br />
Стоянка такси<br />
Церковь<br />
Суд<br />
Посольство<br />
Автозаправочная станция<br />
Больница<br />
Мечеть<br />
Кинотеатр<br />
Зоопарк<br />
Музей<br />
Ночной клуб<br />
Парк<br />
Аптека<br />
Полиция<br />-->
</div>

<div class="block_mwater">
<div class="h2">Новости:</div>
<?php

 $url = "http://news.yandex.ru/".$myrow['news']."/index.rss";
 $content = file_get_contents($url);

 $patern_titl = "|<title>(.*?)</title>|is";
 $patern_link = "|<link>(.*?)</link>|is";
 $patern_description = "|<description>(.*?)</description>|is";

  preg_match_all($patern_titl,$content,$titl);
 
  preg_match_all($patern_link,$content,$link);
 
  preg_match_all($patern_description,$content,$description);
 

for($i=2;$i<10;$i++) {
   
   echo "<p> – ".$titl[1][$i]."</p> <br />";
	
   }


?>
<p align="right">Подробнее...  <a href="/news/<?=$myrow['chpu'];?>">Читать все новости</a></p>
</div>
</noindex>
<div style="clear:left"> </div>
</div>
<? include ("../block/footer.php");?>
</body>
</html>
