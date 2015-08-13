<?php
//header("Content-Type: text/plain");
$api_key = 'rusmca2171'; // maps.ngs.ru
//$api_key = 'rumquq0178';
if(empty($_REQUEST['id']) || empty($_REQUEST['firm_id']) || empty($_REQUEST['hash'])) {
    $res = array('status' => "Bad request");
    print json_encode($res);
    exit;
}
$firm_id = $_REQUEST['firm_id'];
$id = $_REQUEST['id'];
$hash = $_REQUEST['hash'];

//$id = '2533803071470024';
//$hash = '6BBhg2G4465904035A59186d9da43708H6H335J0fuvvo77J233G456655A5hc4998589473473H2G3717C8G5I3GJee';
//documentation: http://api.2gis.ru/doc/firms/profiles/profile/

$url = "http://catalog.api.2gis.ru/profile?key={$api_key}&version=1.3&id={$id}&hash={$hash}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$data = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($httpcode<200 || $httpcode>=300) {
    $res = array('status' => "Ошибка справочника ", 'data' =>$data);
    print json_encode($res);
    exit;
}
$api_rubrics = json_decode($data);
if ($api_rubrics->response_code == 200) {
    $html = '';
    $name_encoded = str_replace('/', '\xA6', urlencode($api_rubrics->name))
    ?>


    <?php
    foreach ($api_rubrics->contacts as $contact_group) {
        foreach ($contact_group->contacts as $contact) {
            $alias = empty($contact->alias)?'':$contact->alias;
            $url_txt = empty($contact->alias)?$contact->value:$alias;
            $alias = empty($alias)?'':"{$alias}<br/>";
            switch ($contact->type) {
                case 'email':
                    $html .= "<p class=\"contacts email\">{$alias}E-mail:<span><a href=\"mailto:{$contact->value}\">{$contact->value}</a></span></p>";
                    break;
                case 'website':
                    $html .= "<p class=\"contacts website\">Сайт:<span><a href=\"{$contact->value}\">{$url_txt}</a></span></p>";
                    break;
                case 'phone':
                    $html .= "<p class=\"contacts phone\">{$alias}Тел.:<span>{$contact->value}</span></p>";
                    break;
                case 'fax':
                    $html .= "<p class=\"contacts fax\">{$alias}Факс:<span>{$contact->value}</span></p>";
                    break;
                case 'skype':
                    $html .= "<p class=\"contacts skype\">Skype:<span><a href=\"skype:{$contact->value}\">{$url_txt}</a></span></p>";
                    break;
                case 'instagram':
                    $html .= "<p class=\"contacts instagram\">Instagram:<span><a href=\"{$contact->value}\">{$url_txt}</a></span></p>";
                    break;
                case 'twitter':
                    $html .= "<p class=\"contacts twitter\">Twitter:<span><a href=\"{$contact->value}\">{$url_txt}</a></span></p>";
                    break;
                case 'facebook':
                    $html .= "<p class=\"contacts facebook\">Feacebook:<span><a href=\"{$contact->value}\">{$url_txt}</a></span></p>";
                    break;
                case 'vkontakte':
                    $html .= "<p class=\"contacts vkontakte\">Вконтакте:<span><a href=\"{$contact->value}\">{$url_txt}</a></span></p>";
                    break;
                default:
                    if ($alias == $contact->value.'<br/>') {
                        $alias = '';
                    }
                    $html .= "<p class=\"contacts {$contact->type}\">{$alias}{$contact->type}:<span>{$contact->value}</span></p>";
                    //'icq', 'jabber'
                    break;

            }
        }
    }
    $html .= "<p>{$api_rubrics->article}</p>";
    $html .= "<p><a href=\"{$api_rubrics->link->link}\">{$api_rubrics->link->text}</a></p>";
    $html.= "<br/><p><a class=\"gis-btn\" target='_blank' href=\"http://2gis.ru/city/{$api_rubrics->project_id}/center/{$api_rubrics->lon}%2C{$api_rubrics->lat}/zoom/17/routeTab/to/{$api_rubrics->lon}%2C{$api_rubrics->lat}%E2%95%8E{$name_encoded}?utm_source=profile&utm_medium=route_from&utm_campaign=partnerapi\"  title=\"Проезд на автомобиле или общественном транспорте от {$api_rubrics->name}, {$api_rubrics->city_name}\">Маршрут от</a>
            <a class=\"gis-btn\" target='_blank' href=\"http://2gis.ru/city/{$api_rubrics->project_id}/center/{$api_rubrics->lon}%2C{$api_rubrics->lat}/zoom/17/routeTab/from/{$api_rubrics->lon}%2C{$api_rubrics->lat}%E2%95%8E{$name_encoded}?utm_source=profile&utm_medium=route_to&utm_campaign=partnerapi\" role=\"button\" title=\"Проезд на автомобиле или общественном транспорте до {$api_rubrics->name}, {$api_rubrics->city_name}\">до</a>";

     if ($api_rubrics->reviews_count) {
         $html .= "<a class=\"gis-btn\" target='_blank' href=\"http://2gis.ru/city/{$api_rubrics->project_id}/firm/{$api_rubrics->id}/photos/{$api_rubrics->id}/center/{$api_rubrics->lon}%2C{$api_rubrics->lat}/zoom/17?utm_source=profile&utm_medium=photo&utm_campaign=partnerapi\" role=\"button\" title=\"Фотографии услуг и продукции {$api_rubrics->name}, {$api_rubrics->city_name}\">Посмотреть фотографии</a>
        <a class=\"gis-btn\" target='_blank' href=\"http://2gis.ru/city/{$api_rubrics->project_id}/firm/{$api_rubrics->id}/flamp/{$api_rubrics->id}/callout/firms-{$api_rubrics->id}/center/{$api_rubrics->lon}%2C{$api_rubrics->lat}/zoom/17?utm_source=profile&utm_medium=review&utm_campaign=partnerapi\" role=\"button\" title=\"Отзывы о работе {$api_rubrics->name}, {$api_rubrics->city_name}\">Прочитать отзывы</a>";
     }

    if ($api_rubrics->booklet_url) {
        $html .= "<a class=\"gis-btn\" target='_blank' href=\"{$api_rubrics->booklet_url}?utm_source=profile&utm_medium=booklet&utm_campaign=partnerapi\" role=\"button\" title=\"Посмотреть список товаров, услуг и цен фирмы {$api_rubrics->name}, {$api_rubrics->city_name}\">Услуги и цены</a>";
    }
    $html .= "<a class=\"gis-btn\" target='_blank' href=\"http://2gis.ru/city/{$api_rubrics->project_id}/firm/{$api_rubrics->id}/entrance/center/{$api_rubrics->lon}%2C{$api_rubrics->lat}/zoom/17?utm_source=profile&utm_medium=entrance&utm_campaign=partnerapi\" role=\"button\" title=\"Вход в организацию {$api_rubrics->name}, {$api_rubrics->city_name }}\">Найти вход</a>
</p>";
    $data = array('html' => $html, 'register_bc_url' => $api_rubrics->register_bc_url);
    $res = array('status' => "OK", 'data' =>$data);
    print json_encode($res);
    exit;
} else {
    $res = array('status' => "\"Bad\" API response", 'data' =>$api_rubrics);
    print json_encode($res);
    exit;
}