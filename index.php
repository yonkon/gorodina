<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Города России. Карты, панорамы, фото и пробки.</title>
<meta name="description" content="Путешествие по городам Росии по карте с фото. Панорамы красивейших городов." />
<meta name="keyword" content="Фото городов россии карта пробки" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="/favicon.ico" />
<script type="text/javascript" src="http://www.panoramio.com/wapi/wapi.js?v=1&amp;hl=ru"></script>
<style type="text/css">
  #div_attr_ex {
    position: relative;
    margin: 0 0 20px 30px;
    float: center;
    width: 870px;
  }
  #div_attr_ex_list {
    position: absolute;
    left: 800px;
  }
  #div_attr_ex .panoramio-wapi-images {
    background-color: #E5ECF9;
  }
  #div_attr_ex .pwanoramio-wapi-tos{
    background-color: #E5ECF9 !important;
  }
          #map {
            width:990px;
            height:600px;
        }
</style>
<script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
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
        center: [61.951886,75.286121], // Москва
        zoom: 4
    });

    document.getElementById('destroyButton').onclick = function () {
        // Для уничтожения используется метод destroy.
        myMap.destroy();
    };

}
</script>
</head>
<body>
<? include ("block/header.php");?>
<? include ("block/nav.php");?>
<div class="content">
<h1>Города России:</h1>
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
<br /><br /><br />
     <p align="justify">Здесь вы можете путешествовать по городам! Портал <i>GoRodina.ru</i> создан с целью дать максимально интересный и полезный контент о вашем городе. Ведь в нашей не объятной Родине столько замечательных городов, с самыми необычными местами, людьми, погодными условиями и животным миром. Приблизится ко всему этому очень просто...</p>
	 <p align="justify">А как же справочная информация? Больницы, бары, туристические агенства, рестораны, спорт клубы и многое другое. Как все отыскать в чужом или даже родном городе? У нас много справочной информации с демонстрацией на крте вашего города. Поэтому вы не только узнаете полезнуюю информацию об искомом месте, но также сможете увидеть точку где распологается интересующий объект на карте города.</p>
<h2>Фото России:</h2>
<div id="div_attr_ex">
  <div id="div_attr_ex_list"></div>
  <div id="div_attr_ex_photo"></div>
  <div id="div_attr_ex_attr"></div>
</div>
<script type="text/javascript">
  var sand = {'tag': 'Россия'};
  var sandRequest = new panoramio.PhotoRequest(sand);
  var attr_ex_photo_options = {
    'width': 800,
    'height': 800,
    'attributionStyle': panoramio.tos.Style.HIDDEN};
  var attr_ex_photo_widget = new panoramio.PhotoWidget(
      'div_attr_ex_photo', sandRequest, attr_ex_photo_options);

  var attr_ex_list_options = {
    'width': 90,
    'height': 700,
    'columns': 1,
    'rows': 8,
    'croppedPhotos': true,
    'disableDefaultEvents': [panoramio.events.EventType.PHOTO_CLICKED],
    'orientation': panoramio.PhotoListWidgetOptions.Orientation.VERTICAL,
    'attributionStyle': panoramio.tos.Style.HIDDEN};
  var attr_ex_list_widget = new panoramio.PhotoListWidget(
    'div_attr_ex_list', sandRequest, attr_ex_list_options);

  var attr_ex_attr_options = {'width': 370};
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

<div align="center"><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Горизонтальный большой -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-1224980819146957"
     data-ad-slot="4994310786"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></div>

<h2>Карта России:</h2>

<div id="map"></div>
</div>
	  <div align="center">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Широкий прямоугольник -->
<ins class="adsbygoogle"
     style="display:inline-block;width:336px;height:280px"
     data-ad-client="ca-pub-1224980819146957"
     data-ad-slot="6471043980"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>

<? include ("block/footer.php");?>
		  </div><!-- #content-->

	
</body>
</html>
