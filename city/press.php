<?php
include ("../block/bd.php");
 $urlka = $_SERVER['REQUEST_URI'];
 $serv_name = 'Cправочники';
 $api_key = 'rumquq0178';
 if (preg_match ("/([^a-zA-Z0-9\.\/\-\_\#])/", $urlka)) {

   header("HTTP/1.0 404 Not Found");
   echo "<h1 align='center'>Запрещенные символы в адресе URL <a href='/'>НА ГЛАВНУЮ</a></h1>";
   exit;
 }

$urlka = str_replace('/city/', '',$urlka); /* удалил слеш “/” в начале файлы */

$result = mysql_query("SELECT * FROM citys WHERE chpu='$urlka'");      
$myrow = mysql_fetch_array($result);

//Проверяем кол-во заисей в идеале 1, если 0 то страницы не существует
if (empty($myrow)) {echo "Страницы не сущетвует ";  exit;}

$url_rubrics = "http://catalog.api.2gis.ru/rubricator?key={$api_key}&version=1.3&where={$myrow['name']}&pagesize=50";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_rubrics);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$data = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$rubrics = null;
if ($httpcode>=200 & $httpcode<300) {
  $api_rubrics = json_decode($data);
  if ($api_rubrics->response_code != 404) {
    //Получили список рубрик и обрабатываем
    $rubric_sql = "INSERT INTO `rubrics`(`id`, `name`, `alias`, `parent_id`, `city`) VALUES ";
    $rubrics_sql_values = array();
    foreach ($api_rubrics->result as $rubric) {
      $rubrics_sql_values[] = "('{$rubric->id}', '{$rubric->name}','{$rubric->alias}','{$rubric->parent_id}', {$myrow['id']})";
    }
    $rubric_sql .= join(', ', $rubrics_sql_values) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `alias`=VALUES(`alias`), `parent_id`=VALUES(`parent_id`), `city`={$myrow['id']}";
    mysql_query($rubric_sql);

  }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Сайт города – <?=$myrow['name'];?>. Городской портал <?=$myrow['rpadej'];?>.</title>
<meta name="description" content="Городской портал <?=$myrow['rpadej'];?>. Смотрите карты, фото, панорамы и другие справочные материалы вашего города." />
<meta name="keyword" content="<?=$myrow['name'];?>, сайт, городской, портал, город" />
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../favicon.ico" />
<style>
.block_water {
width:430px;

float:left;
margin:18px;
padding:10px;
background-image: url('http://all.ru/im/bg.png');

}

.rubric {
  margin-top: 3pt;
  font-size: 10pt;
}
</style>
  <script src="http://catalog.api.2gis.ru/assets/apitracker.js"></script>

</head>
<body>
<? include ("../block/header.php");?>
<? include ("../block/nav.php");?>
<div class="content">
<img width="40px" height="50px" align="left" src="../im/logo/<?=$myrow['chpu'];?>.png" /><h1 align="center">Сайт города - <?=$myrow['name'];?></h1>

<div class="block_water">
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

<div id="catalog" class="block_water">
  <div class="h2"><?php echo ($serv_name); ?>: </div>
  <p>Данные предоставлены <a href="http://2gis.ru" >2ГИС</a></p>
<?php
  //Вывод рекламы если есть
if (!empty ($api_rubrics->advertising)) {
  foreach($api_rubrics->advertising as $ad) {
    echo
    "<div class=\"rubric advertising\">
        <input type='hidden' name='hash' value='{$ad->hash}'/>
        <a href=\"/catalog/{$myrow['chpu']}/{$ad->alias}\">{$ad->title}</a>
        <p>{$ad->text}</p>
        <p class=\"fas_warning\">{$ad->fas_warning}</p>
      </div> ";
  }
}
  //Выводим данные о рубриках

  if (empty ($api_rubrics->result)) {
    echo "<p>Справочников не найдено</p>";
  } else {
    foreach($api_rubrics->result as $rubric) {
      echo
      "<div class=\"rubric\">
        <a href=\"/catalog/{$myrow['chpu']}/{$rubric->alias}\">{$rubric->name}</a>
      </div> ";
    }
  }
?>
</div>

<div class="block_water">
<div class="h2">Фото:</div>
<p><a href="../photo/<?=$myrow['chpu'];?>">Смотреть фотогалерею города</br><br />
<img src="../im/pncity/<?=$myrow['chpu'];?>.jpg" /></a></p>

</br>

<p><a href="../panoram/<?=$myrow['chpu'];?>">
Смотреть панораму города:</br>
<img src="../im/panorami.png" /></a></p>
</div>

<!--<div style="clear:left"> </div>-->

<div class="block_water">
<div class="h2">Карта города:</div>
<p align="center"><a href="../map/<?=$myrow['chpu'];?>">Яндекс Карта <?=$myrow['rpadej'];?><br /><br />
<img src="../im/map.gif" /></a></p>
</ br>

<div class="h2">Показать на карте <?=$myrow['rpadej'];?>:</div>

<table width="380px" align="center" border="0">
<tr align="center" >
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
Стоянка такси<br />-->

	</td>
	
	<td>
<!--Церковь<br />
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

	</td>
</tr>
</table>

</div>






<div class="block_water">
<table width="400px" align="center" border="0">
<tr align="center" >
	<td valign="top">
 <div class="h2">Погода:</div>
<a href="../pogoda/<?=$myrow['chpu'];?>">Погода  <?=$myrow['rpadej'];?><br /><br />
<img src="//info.weather.yandex.net/<?=$myrow['news'];?>/2.ru.png?domain=ru" border="0" alt="Яндекс.Погода"/><img width="1" height="1" src="https://clck.yandex.ru/click/dtype=stred/pid=7/cid=1227/*https://img.yandex.ru/i/pix.gif" alt="" border="0"/></a>
</div>

	</td>
	
	
	<td valign="top">
	
<div class="h2">Пробки:</div>
 <a href="../probki/<?=$myrow['chpu'];?>">Узнать пробки<br /><br />
 <img src="../im/probki.jpg"/>
 </a>
	</td>
</tr>
</table>
</div>









<div class="block_water">
<div class="h2">Работа:</div>

<!-- Блок CSS -->
<style type="text/css">
#rabotaSearchFormContainer {width:400px;border:1px solid #a6a6ff;background-color:#e1f0fa;color:#000000;font-family:Arial;font-size:11px}
#rabotaSearchFormContainer select {width:100%}
#rabotaSearchListContainer {font-family: Arial; color: #000000;}
#rabotaSearchListContainer a {color: #000080;}
#rabotaSearchListContainer a.title, span.title {font-weight: bold;}
#rabotaSearchListContainer input, select  {font-family:Arial;background-color:#ffffff;color:#000000;}
#rabotaSearchListContainer th, #rabotaSearchListContainer td  {padding:3px 0 4px 10px; vertical-align:middle;}
#rabotaSearchListContainer th:first-child, #rabotaSearchListContainer td:first-child  {padding:3px 0 4px 5px;}
tr.stripped {background-color: #eeeeee;}
</style>
<!-- Конец блока CSS -->

<!-- Форма поиска -->
<form id="rabotaSearchForm"  action="../work/<?=$myrow['chpu'];?>" method="post"  name="rabotaSearchForm">
<input type="hidden" name="mode" value="search">
<table id="rabotaSearchFormContainer">
<tr>
<td colspan="2">Должность:
<input type="text" id="w" name="w" value="" class="rabota-input" />
</td>
</tr>
<tr>
<td colspan="2">
<input type="radio" id="t1" name="t" value="1">&nbsp;<label for="t1">Вакансии</label>
<input type="radio" id="t2" name="t" value="2">&nbsp;<label for="t2">Резюме</label>
</td>
</tr>
<tr>
<td colspan="2">
<select id="c" name="c" class="rabota-select"><option value="1"><?=$myrow['name'];?></option></select>
</td>
</tr>
<tr>
<td colspan="2">
<select id="r" name="r" class="rabota-select"><option value="">- Выберите область деятельности -</option></select>
</td>
</tr>
<tr>
<td width="70%">
<select id="s" name="s" class="rabota-select"><option value="7">по дате</option><option value="3">по зарплате</option><option value="2">по релевантности</option></select>
</td>
<td width="30%">
<select id="d" name="d" class="rabota-select"><option value="asc">&Delta;</option><option value="desc">&nabla;</option></select>
</td>
</tr>
<tr>
<td>
<input type="submit" value="Найти" />
</td>
<td>
<select id="pp" name="pp" class="rabota-select"><option value="10">10</option><option value="30">30</option><option value="50">50</option></select>
</td>
</tr>
</table>
</form>
</div>
<!-- Вызов JS-функций -->
<script type="text/javascript">
var rabota_informer_count = 0;
function rabota_print_informer()
{
	var s = document.createElement("script");
	s.src = 'http://www.rabota.ru/v3_viewSearchForm.html?'+document.location.search.substring(1);
	s.type = "text/javascript";
	s.charset = "UTF-8";
	document.getElementsByTagName("head")[0].appendChild(s);
	s.onreadystatechange = function() {
		if(s.readyState == "loaded" || s.readyState == "complete") {
			try {
				var r2 = new rabotaSearchForm();
				window.status = "";
			} catch (e) {
				window.status = "Еще одна попытка...";
				rabota_informer_count++;
				if(rabota_informer_count < 5) {
					rabota_print_informer();
				} else {
					alert("Отсутствует соединение с сервером. Попробуйте обновить страницу.");
				}
			}
		}
	}
}
rabota_print_informer();
</script>
<!-- Конец вызова JS-функций -->

<!--<div style="clear:left"> </div>-->
<div class="block_water">
<div class="h2">О городе:</div>
<p align="justify" style="color:#545454; font-size:12px"><?=$myrow['desk'];?></p>
</div>

<div style="clear:left"> </div>
</div>

<? include ("../block/footer.php");?>

</body>
</html>
