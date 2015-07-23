<?php
include ("../block/bd.php");
 $urlka = $_SERVER['REQUEST_URI'];

 if (preg_match ("/([^a-zA-Z0-9\.\/\-\_\#])/", $urlka)) {

   header("HTTP/1.0 404 Not Found");
   echo "<h1 align='center'>Запрещенные символы в адресе URL <a href='/'>НА ГЛАВНУЮ</a></h1>";
   exit;
 }

$urlka = str_replace('/photo/', '',$urlka); /* удалил слеш “/” в начале файлы */


$result = mysql_query("SELECT * FROM citys WHERE chpu='$urlka'");      
$myrow = mysql_fetch_array($result);

//Проверяем кол-во заисей в идеале 1, если 0 то страницы не существует
if (empty($myrow)) {echo "Страницы не сущетвует ";  exit;}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Фото <?=$myrow['rpadej'];?>, <?=$myrow['region'];?>. Фотки родного города - <?=$myrow['name'];?>.</title>
<meta name="description" content="Фото <?=$myrow['rpadej'];?>. Большой архив фоток сделаных в городе - <?=$myrow['name'];?>. Смотреть слайдшоу." />
<meta name="keyword" content="<?=$myrow['region'];?>, <?=$myrow['name'];?>, фото, города, смотреть, фотки" />
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../favicon.ico" />
<script type="text/javascript" src="http://www.panoramio.com/wapi/wapi.js?v=1&amp;hl=ru"></script>
<style type="text/css">
  #div_attr_ex {
    position: relative;
    margin: 0 0 20px 8px;
    float: center;
    width: 510px;
  }
  #div_attr_ex_list {
    position: absolute;
    left: 510px;
  }
  #div_attr_ex .panoramio-wapi-images {
    background-color: #E5ECF9;
  }
  #div_attr_ex .pwanoramio-wapi-tos{
    background-color: #E5ECF9 !important;
  }
</style>
</head>
<body>
<? include ("../block/header.php");?>
<? include ("../block/nav.php");?>

<div class="content">

<div class="block_main">

<h1>Фото города - <?=$myrow['name'];?></h1>

<div id="div_attr_ex">
  <div id="div_attr_ex_list"></div>
  <div id="div_attr_ex_photo"></div>
  <div id="div_attr_ex_attr"></div>
</div>
<script type="text/javascript">
  var sand = {'tag': '<?=$myrow['name'];?>'};
  var sandRequest = new panoramio.PhotoRequest(sand);
  var attr_ex_photo_options = {
    'width': 508,
    'height': 600,
    'attributionStyle': panoramio.tos.Style.HIDDEN};
  var attr_ex_photo_widget = new panoramio.PhotoWidget(
      'div_attr_ex_photo', sandRequest, attr_ex_photo_options);

  var attr_ex_list_options = {
    'width': 90,
    'height': 600,
    'columns': 1,
    'rows': 7,
    'croppedPhotos': true,
    'disableDefaultEvents': [panoramio.events.EventType.PHOTO_CLICKED],
    'orientation': panoramio.PhotoListWidgetOptions.Orientation.VERTICAL,
    'attributionStyle': panoramio.tos.Style.HIDDEN};
  var attr_ex_list_widget = new panoramio.PhotoListWidget(
    'div_attr_ex_list', sandRequest, attr_ex_list_options);

  var attr_ex_attr_options = {'width': 170};
  var attr_ex_attr_widget = new panoramio.TermsOfServiceWidget(
    'div_attr_ex_attr', attr_ex_attr_options);

  function onListPhotoClicked(event) {
    var position = event.getPosition();
    if (position !== null) attr_ex_photo_widget.setPosition(position);
  }
  panoramio.events.listen(
    attr_ex_list_widget, panoramio.events.EventType.PHOTO_CLICKED,
    function(e) { onListPhotoClicked(e); });
  attr_ex_photo_widget.enablePreviousArrow(false);
  attr_ex_photo_widget.enableNextArrow(false);

  attr_ex_photo_widget.setPosition(0);
  attr_ex_list_widget.setPosition(0);
</script>



</div>

<div class="block_mwater">
 <div align="center"><a href="/city/<?=$myrow['chpu'];?>"><div class="h2">Городской сайт</div>
<img width="120px" align="center" height="150px" align="left" src="../im/logo/<?=$myrow['chpu'];?>.png" /></div></a>
</div>
<noindex>

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
<div style="clear:left"> </div>

<div class="block_mwater">
<div class="h2">О городе:</div>
<p align="justify" style="color:#545454; font-size:12px"><?=$myrow['desk'];?></p>
</div>



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
