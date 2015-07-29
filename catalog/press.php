<?php
/*ini_set('display_errors', 1);
error_reporting(-1);*/
include ("../block/bd.php");
$thisurl = $_SERVER['REQUEST_URI'];
$api_key = 'rumquq0178';
$urlka = str_replace('/catalog/', '',$thisurl); /* удалил слеш “/” в начале файлы */
$questionMark = strpos($urlka, '?');
if (!empty($questionMark)) {
  $urlka = substr($urlka, 0, $questionMark);
}
$url_parts = explode('/', $urlka);
$city_chpu = empty($url_parts[0])?'':$url_parts[0];

$list_msg = '';
$list_mode = false;
if (empty($city_chpu) || $city_chpu == 'press.php') {
  $list_mode = true;
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
      $list_msg .= "<a href='/catalog/{$myrow3['chpu']}'>{$myrow3['name']}</a>\n";
    }
    while ($myrow3 = mysql_fetch_array($result3));
  }
  else {
    $list_msg = "<p>Информация по запросу не может быть извлечена в таблице нет записей.</p>";
  }
} else {
  $service_alias = empty($url_parts[1])?'':$url_parts[1];
  $result = mysql_query("SELECT * FROM citys WHERE chpu='{$city_chpu}'");
  $myrow = mysql_fetch_array($result);
//Проверяем кол-во заисей в идеале 1, если 0 то страницы не существует
  if (empty($myrow)) {echo "Страницы такого города не сущетвует ";  exit;}

//Флаг вывода HTML: true - для компаний, иначе выводим рубрики
  $companies_mode = false;
  $service = null;

  if (empty ($service_alias) ) {
    $serv_name = 'справочники';
    if(!empty($_REQUEST['rubric'])) {
      $serv_result = mysql_query("SELECT * FROM rubrics WHERE name='{$_REQUEST['rubric']}' AND `city`={$myrow['id']}");
      $service = mysql_fetch_array($serv_result);
      if (empty($service)) {
        echo "Страницы не сущетвует ";
        echo $urlka;
        print_r($url_parts);
        exit;
      }
      $serv_name = $_REQUEST['rubric'];
    }
  } else {
    $serv_sql = "SELECT * FROM rubrics WHERE alias='{$service_alias}' AND `city`={$myrow['id']}";
    $serv_result = mysql_query($serv_sql);
    $service = mysql_fetch_array($serv_result);
    if (empty($service)) {
      echo "Страницы не сущетвует ";
      echo $serv_sql;
      exit;
    }
    $serv_name = $service['name'];
  }

//Достаём данные с 2ГИС
//Получаем рубрики
  $url_rubrics = "http://catalog.api.2gis.ru/rubricator?key={$api_key}&version=1.3&where={$myrow['name']}";
//Если указана рубрика
  if($service) {
    //parent_id != 0 - указана подрубрика, значит ищем компании
    if($service['parent_id'] != 0) {
      $companies_mode = true;
      $url_rubrics = "http://catalog.api.2gis.ru/searchinrubric?key={$api_key}&version=1.3&where={$myrow['name']}&what={$service['name']}";
      if (!empty($_REQUEST['page'])) {
        $url_rubrics .= '&page='.$_REQUEST['page'];
      }
    } else {
      //Иначе ищем подрубрики
      $url_rubrics .= "&parent_id={$service['id']}";
    }
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url_rubrics);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $data = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($httpcode<200 || $httpcode>=300) {
    echo "Ошибка справочника ";
    print_r($data);
    exit;
  }
  $api_rubrics = json_decode($data);
  if ($api_rubrics->response_code != 404) {
    if ($companies_mode) {
      //Получили список компаний и обрабатываем
    } else {
      //Получили список рубрик и обрабатываем
      $rubric_sql = "INSERT INTO `rubrics`(`id`, `name`, `alias`, `parent_id`, `city`) VALUES ";
      $rubrics_sql_values = array();
      foreach ($api_rubrics->result as $rubric) {
        $rubrics_sql_values[] = "('{$rubric->id}', '{$rubric->name}','{$rubric->alias}','{$rubric->parent_id}', {$myrow['id']})";
      }
      $rubric_sql .= join(', ', $rubrics_sql_values) . " ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `alias`=VALUES(`alias`), `parent_id`=VALUES(`parent_id`), `city`={$myrow['id']}";
      if(!mysql_query($rubric_sql) ) { echo $rubric_sql; }
    }
  }
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <?php include('./meta.php'); ?>
  <link href="/style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="/favicon.ico" />
    <script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <style>
        #map {
            width:595px;
            height:650px;
        }
        .company {
          padding: 8pt;
          border-bottom: 1px solid rgba(199, 155, 155, 0.5);
        }

        .company.name {
          font-size: 14pt;
        }

      .rubric {
        margin-bottom: 3pt;
      }
    </style>

<script type="text/javascript">
var myMap;

// Дождёмся загрузки API и готовности DOM.
ymaps.ready(init);

function init () {
    // Создание экземпляра карты и его привязка к контейнеру с
    // заданным id ("map").
    myMap = new ymaps.Map('map', {
        // При инициализации карты обязательно нужно указать
        // её центр и коэффициент масштабирования.
        center: [<?php echo ($myrow['kor2']); ?>, <?php echo ($myrow['kor1']); ?>], // Москва
        zoom: 12
    });
  var companies = document.querySelectorAll('.company .name');
  for (var c in companies) {
    if (!companies.hasOwnProperty(c)) continue;
    var company = companies[c];
    var lat = company.attributes['data-lat'].textContent;
    var lon = company.attributes['data-lon'].textContent;
    var name = company.textContent;
    var myPlacemark = new ymaps.Placemark([lat, lon], { content: name, balloonContent: name });
    myMap.geoObjects.add(myPlacemark);
  }


    document.getElementById('destroyButton').onclick = function () {
        // Для уничтожения используется метод destroy.
        myMap.destroy();
    };

}
</script>

</head>
<body>
<?php include ("../block/header.php");?>
<?php include ("../block/nav.php");?>
<div class="content">

<div class="block_main">
  <?php if($list_mode) {
    echo $list_msg;
  } else {?>
<h1><?php echo ($serv_name); ?></h1>
<div id="catalog">
  <p>Данные предоставлены <a href="http://2gis.ru" >2ГИС</a></p>
  <?php
  if ($companies_mode) {
    //Выводим данные о компаниях
    if (empty ($api_rubrics->result)) {
      echo "<p>Компаний не найдено</p>";
      echo "<a href=\"/catalog/{$city_chpu}\">Назад к справочникам</a>";
    } else {
      foreach($api_rubrics->result as $company) {
        ?>
      <div class="company">
        <p class="name" data-lat="<?php echo ($company->lat); ?>"
           data-lon="<?php echo ($company->lon); ?>">
          <?php echo ($company->name); ?></p>
        <p><?php echo ($company->city_name); ?>,
          <?php echo ($company->address); ?></p>
        <p>
          <?php
          foreach($company->rubrics as $com_rubric) {
            echo
"<span class=\"company_rubric\">
  <a href=\"/catalog/{$city_chpu}?rubric={$com_rubric}\">{$com_rubric}</a>
 </span>";
          }
          ?>
        </p>
      </div>
  <?php
      }
    }
  } else {
    //Выводим данные о рубриках
    if (empty ($api_rubrics->result)) {
      echo "<p>Подрубрик не найдено</p>";
      echo "<a href=\"/catalog/{$city_chpu}\">Назад к справочникам</a>";
    } else {
      foreach($api_rubrics->result as $rubric) {
        echo
        "<div class=\"rubric\">
        <a href=\"/catalog/{$city_chpu}/{$rubric->alias}\">{$rubric->name}</a>
      </div> ";
      }
    }
  }

  ?>
</div>
<div id="map"></div>
  <?php
  }
  ?>
</div>

<div class="block_mwater">
 <div align="center"><a href="/city/<?php echo ($myrow['chpu']); ?>"><div class="h2">Городской сайт</div>
<img width="120px" align="center" height="150px" align="left" src="/im/logo/<?php echo ($myrow['chpu']); ?>.png" /></div></a>
</div>

<div class="block_mwater">

<div class="h2">Показать на карте <?php echo ($myrow['rpadej']); ?>:</div>
	<td>
	<a href="/avtomoyka/<?php echo ($myrow['chpu']); ?>">Автомойки</a><br />
	<a href="/sto/<?php echo ($myrow['chpu']); ?>">Автосервисы</a><br />
	<a href="/parking/<?php echo ($myrow['chpu']); ?>">Автопарковки</a><br />
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
<noindex>

<div class="block_mwater">
<div class="h2">Фото:</div>
<p><a href="/photo/<?php echo ($myrow['chpu']); ?>">Смотреть фото города</br>
<img width="260px" height="110px" src="/im/pncity/<?php echo ($myrow['chpu']); ?>.jpg" /></a></p>

</br>

<p><a href="/panoram/<?php echo ($myrow['chpu']); ?>">
Смотреть панораму города:</br>
<img width="260px"  height="60px" src="/im/panorami.png" /></a></p>
</div>

<div class="block_mwater">
 <div class="h2">Погода</div>
<div align="center"><img  src="//info.weather.yandex.net/<?php echo ($myrow['news']); ?>/2.ru.png?domain=ru" border="0" alt="Яндекс.Погода"/><img width="1" height="1" src="https://clck.yandex.ru/click/dtype=stred/pid=7/cid=1227/*https://img.yandex.ru/i/pix.gif" alt="" border="0"/> </div>
</div>
<div class="block_mwater">
<div class="h2">Пробки:</div>
 <div align="center"><a href="/probki/<?php echo ($myrow['chpu']); ?>">Узнать пробки
 <img src="/im/probki.jpg"/>
 </a></div>
</div>
</noindex>

<div class="block_mwater">
<div class="h2">О городе:</div>
<p align="justify" style="color:#545454; font-size:12px"><?php echo ($myrow['desk']); ?></p>
</div>
<div style="clear:left"> </div>

<noindex>

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
<p align="right">Подробнее...  <a href="/news/<?php echo ($myrow['chpu']); ?>">Читать все новости</a></p>
</div>
</noindex>
<div style="clear:left"> </div>
</div>
<?php include ("../block/footer.php");?>

</body>
</html>