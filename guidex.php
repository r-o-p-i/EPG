<?php
@session_start();
@error_reporting(0);
@ini_set('html_errors', false);
@ini_set('expose_php = off', false);
@ini_set('memory_limit', '8M');
@ini_set('max_execution_time', 0);
@ini_set("display_errors", 0);

class guidex
{

  function Clean()
  {
    $C = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_';
    $name = array("GUIDE", "STV", "VSETV", "TELEMANTV", "TRICOLOR", "SKY_DE", "SKY", "SBBY", "SPIELFILM");
    for ($i = 0; $i < count($name); $i++) {
      self::delTre($C . $name[$i] . '/');
    }
    for ($i = 0; $i < count($name); $i++) {
      if (!is_dir($C . $name[$i])) {
        mkdir($C . $name[$i]);
        file_put_contents($C . $name[$i] . '/index.html', '');
        file_put_contents($C . $name[$i] . '/index.php', "<?php\r\n?>");
        file_put_contents($C . $name[$i] . '/robots.txt', "User-agent: *\r\nDisallow: \/\r\n");
      }
    }
    require_once('Up.php');
    Up::dream('http://www.teleguide.info/kanals.html', '#id=\"programm_logo3\"><a href=\"\/kanal(.*?)\.html\" title=\"(.*?)\"#is', 'CANNELS_LISTS/TELEGUIDE.txt');
    //Up::stv();
    Up::dream('http://www.vsetv.com/channels.html', '/\<option value=channel_([0-9]{1,})\>([^\<]{1,})\<\/option>/', 'CANNELS_LISTS/VSETV.txt');
    //Up::skydelist();
    Up::teleman('CANNELS_LISTS/TELEMAN.txt');
    Up::SPIELFILM('CANNELS_LISTS/SPILEFILM.txt');
    Up::sbby('CANNELS_LISTS/SBBY.txt');
    Up::tricolor('CANNELS_LISTS/TRICOLOR.txt');
    return true;
  }
  function delTre($dir)
  {
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file") && !is_link($dir)) ? self::delTre("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }
  function yandex($yandex)
  {
    header('Content-Disposition: attachment; filename=' . $yandex . '_name_index_url.dat');
    header("Content-type: text/plain; charset=UTF-8");
    $URL = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER['SCRIPT_NAME']) . "/";
    $CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_MAILRU/';
    if (!is_dir($CACHEDIR)) {
      mkdir($CACHEDIR);
      file_put_contents($CACHEDIR . 'index.html', '');
      file_put_contents($CACHEDIR . 'index.php', "<?php\r\n?>");
      file_put_contents($CACHEDIR . 'robots.txt', "User-agent: *\r\nDisallow: \/\r\n");
    }
    $arrs = array('187', '213', '149', '163', '177', '198', '191', '162', '87', '77', '75', '66', '65', '64', '63', '62', '56', '55', '37', '22', '2');
    array_unshift($arrs, $yandex);
    $ul = [];
    $c = "";
    for ($s = 0; $s < count($arrs); $s++) {
      $r = guidex::yan($arrs[$s]);
      $t = explode("\r\n", $r);
      for ($i = 0; $i < count($t); $i++) {
        preg_match('/^(.*?)\|(.*?)\|(.*?)$/', $t[$i], $m);
        if (!in_array($m[1], $ul) && $m[1] != "") {
          array_push($ul, $m[1]);
          $c = $c . ($m[1] . '|' . $m[2] . '|' . $m[3] . "\r\n");
        }
      }
    }
    @file_put_contents($CACHEDIR . 'yandex.txt', $c);
    print($c);
  }

  public static function mailru($mailru)
  {
    header('Content-Disposition: attachment; filename=' . $mailru . '_name_index_url.dat');
    header("Content-type: text/plain; charset=UTF-8");
    $URL = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER['SCRIPT_NAME']) . "/";
    $CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_MAILRU/';
    if (!is_dir($CACHEDIR)) {
      mkdir($CACHEDIR);
      file_put_contents($CACHEDIR . 'index.html', '');
      file_put_contents($CACHEDIR . 'index.php', "<?php\r\n?>");
      file_put_contents($CACHEDIR . 'robots.txt', "User-agent: *\r\nDisallow: \/\r\n");
    }
    $ch = guidex::mailik($mailru);
    @file_put_contents($CACHEDIR . $mailru . '.txt', $ch);
    print($ch);
  }

  public static function yan($yandex)
  {
    $blok = ('https://tv.yandex.ru/ajax?params=' . urlencode('[{"name":"i-tv-region","method":"get","args":{"params":"{\"type\":\"regional\",\"packageIds\":[],\"limit\":1000,\"fields\":\"schedules,channels,finish,channel,id,title,logo,sizes,38,src,favourite\",\"lang\":\"ru\"}","cacheKey":"channels?params={\"type\":\"regional\",\"packageIds\":[],\"limit\":1000,\"fields\":[\"schedules\",\"channels\",\"finish\",\"channel\",\"id\",\"title\",\"logo\",\"sizes\",\"38\",\"src\",\"favourite\"]}","userRegion":"' . $yandex . '","resource":"channels","ncrd":' . time() . '509}},{"name":"i-tv-region","method":"get","args":{"params":"{\"type\":\"local\",\"packageIds\":[],\"limit\":1000,\"fields\":\"schedules,channels,finish,channel,id,title,logo,sizes,38,src,favourite\",\"lang\":\"ru\"}","cacheKey":"channels?params={\"type\":\"local\",\"packageIds\":[],\"limit\":1000,\"fields\":[\"schedules\",\"channels\",\"finish\",\"channel\",\"id\",\"title\",\"logo\",\"sizes\",\"38\",\"src\",\"favourite\"]}","userRegion":"' . $yandex . '","resource":"channels","ncrd":' . time() . '510}},{"name":"i-tv-region","method":"get","args":{"params":"{\"type\":\"satelite\",\"packageIds\":[],\"limit\":1000,\"fields\":\"schedules,channels,finish,channel,id,title,logo,sizes,38,src,favourite\",\"lang\":\"ru\"}","cacheKey":"channels?params={\"type\":\"satelite\",\"packageIds\":[],\"limit\":1000,\"fields\":[\"schedules\",\"channels\",\"finish\",\"channel\",\"id\",\"title\",\"logo\",\"sizes\",\"38\",\"src\",\"favourite\"]}","userRegion":"' . $yandex . '","resource":"channels","ncrd":' . time() . '510}}]') . '&sk=73409385e265bf01bc1b26617fb54dbf&userRegion=' . $yandex . '&resource=null&ncrd=' . time() . '532');
    $bloks = guidex::get_json($blok);
    $blok = json_decode($bloks, true);
    $arr = json_decode($blok['0']['response'], true);
    $arr1 = json_decode($blok['1']['response'], true);
    $arr2 = json_decode($blok['2']['response'], true);
    if (count($arr['channels']) > 0) {
      for ($i = 0; $i < count($arr['channels']); $i++)
        $v = $v . ($arr['channels'][$i]['title']) . "|yandex_" . $yandex . "_" . $arr['channels'][$i]['id'] . "|" . str_replace("//avatars.mds.yandex.net/", "", $arr['channels'][$i]['logo']['sizes']["160"]['src']) . "\r\n";
    }
    if (count($arr1['channels']) > 0) {
      for ($i = 0; $i < count($arr1['channels']); $i++)
        $n = $n . ($arr1['channels'][$i]['title']) . "|yandex_" . $yandex . "_" . $arr1['channels'][$i]['id'] . "|" . str_replace("//avatars.mds.yandex.net/", "", $arr1['channels'][$i]['logo']['sizes']["160"]['src']) . "\r\n";
    }
    if (count($arr2['channels']) > 0) {
      for ($i = 0; $i < count($arr2['channels']); $i++)
        $m = $m . ($arr2['channels'][$i]['title']) . "|yandex_" . $yandex . "_" . $arr2['channels'][$i]['id'] . "|" . str_replace("//avatars.mds.yandex.net/", "", $arr2['channels'][$i]['logo']['sizes']["160"]['src']) . "\r\n";
    }
    return ($v . $n . $m);
  }

  public static  function mailik($reg)
  {
    $q = "";
    for ($i = 1; $i < 40; $i++) {
      $b = guidex::get_json("https://tv.mail.ru/ajax/channel/index/?region_id=" . $reg . "&page=" . $i);
      $a = json_decode($b, true);
      if (count($a['pager']['next']['url']) > 0) {
        $q = $q . guidex::sonnet($a, $reg);
      } else {
        break;
      }
    }
    return ($q);
  }
  private static function sonnet($arr, $reg)
  {
    if (count($arr['data'][0]['channel']) > 0) {
      for ($i = 0; $i < count($arr['data'][0]['channel']); $i++)
        $q = $q . ($arr['data'][0]['channel'][$i]['name']) . "|mailru_" . $reg . "_" . $arr['data'][0]['channel'][$i]['id'] . "|" . $arr['data'][0]['channel'][$i]['pic_url'] . "\r\n";
    }
    if (count($arr['data'][1]['channel']) > 0) {
      for ($i = 0; $i < count($arr['data'][1]['channel']); $i++)
        $m = $m . ($arr['data'][1]['channel'][$i]['name']) . "|mailru_" . $reg . "_" . $arr['data'][1]['channel'][$i]['id'] . "|" . $arr['data'][1]['channel'][$i]['pic_url'] . "\r\n";
    }
    if (count($arr['data'][2]['channel']) > 0) {
      for ($i = 0; $i < count($arr['data'][2]['channel']); $i++)
        $b = $b . ($arr['data'][2]['channel'][$i]['name']) . "|mailru_" . $reg . "_" . $arr['data'][2]['channel'][$i]['id'] . "|" . $arr['data'][2]['channel'][$i]['pic_url'] . "\r\n";
    }
    return $q . $m . $b;
  }

  public static function get_json($link)
  {
    $cookie = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie . "  path=/;");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; CrOS i686 9.10.0; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Gecko/20100101 Firefox/29.0');
    $data = $cookie . curl_exec($ch);
    $header = substr($data, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $body = substr($data, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    preg_match_all("/Set-Cookie: (.*?)=(.*?);/i", $header, $res);
    $cookie = '';
    foreach ($res[1] as $key => $value) {
      $cookie .= $value . '=' . $res[2][$key] . '; ';
    };
    curl_close($ch);
    return ($data);
  }
}
