<?php
include ("../block/bd.php");
 $urlka = $_SERVER['REQUEST_URI'];

 if (preg_match ("/([^a-zA-Z0-9\.\/\-\_\#])/", $urlka)) {

   header("HTTP/1.0 404 Not Found");
   echo "<h1 align='center'>Запрещенные символы в адресе URL <a href='/'>НА ГЛАВНУЮ</a></h1>";
   exit;
 }

$urlka = str_replace('/probki/', '',$urlka); /* удалил слеш “/” в начале файлы */


$result = mysql_query("SELECT * FROM citys WHERE chpu='$urlka'");      
$myrow = mysql_fetch_array($result);

//Проверяем кол-во заисей в идеале 1, если 0 то страницы не существует
if (empty($myrow)) {echo "Страницы не сущетвует ";  exit;}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Пробки города - <?=$myrow['name'];?> онлайн. Пробки на дорогах <?=$myrow['name'];?> (<?=$myrow['rpadej'];?>) сейчас. Яндекс.пробки.</title>
<meta name="description" content="Узнать пробки сейчас онлайн в городе <?=$myrow['name'];?>(<?=$myrow['rpadej'];?>). Заторы на дорогах города на картах. " />
<meta name="keyword" content="<?=$myrow['k'];?>" />
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../favicon.ico" />
 <script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <style>
       #map {
            width: 595px; height: 600px; padding: 0; margin: 0;
        }
    </style>

<script type="text/javascript">
ymaps.ready(init);

function init () {
    var myMap = new ymaps.Map('map', {
            center: [<?=$myrow['kor2'];?>, <?=$myrow['kor1'];?>],
            zoom: 12,
            controls: []
        });

    // Создадим элемент управления "Пробки".
    var trafficControl = new ymaps.control.TrafficControl({ state: {
            // Отображаются пробки "Сейчас".
            providerKey: 'traffic#actual',
            // Начинаем сразу показывать пробки на карте.
            trafficShown: true
        }});
    // Добавим контрол на карту.
    myMap.controls.add(trafficControl);
    // Получим ссылку на провайдер пробок "Сейчас" и включим показ инфоточек.
    trafficControl.getProvider('traffic#actual').state.set('infoLayerShown', true);    
}
</script>
</head>
<body>

<? include ("../block/header.php");?>
<? include ("../block/nav.php");?>

<div class="content">

<div class="block_main">
<h1>Пробки <?=$myrow['rpadej'];?></h1>

<div id="map"></div>

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

</br>

<p><a href="../panoram/<?=$myrow['chpu'];?>">
Смотреть панораму города:</br>
<img width="260px"  height="60px" src="../im/panorami.png" /></a></p>
</div>

<div class="block_mwater">
 <div class="h2">Погода</div>
<div align="center"><img  src="//info.weather.yandex.net/<?=$myrow['news'];?>/2.ru.png?domain=ru" border="0" alt="Яндекс.Погода"/><img width="1" height="1" src="https://clck.yandex.ru/click/dtype=stred/pid=7/cid=1227/*https://img.yandex.ru/i/pix.gif" alt="" border="0"/> </div>
</div>
</noindex>
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