<?php
require_once('setup.php');
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('expose_php = off', true);

class ChannelClass
{
  public $Name = '';
  public $ID = '';
  public $URL = '';
  public $LOGO = '';
  public $CIL = '';
}
class Up
{

  public static function dream($url, $pattern, $save)
  {
    global $proxy;
    $cont = self::get_cURL($url);
    if (preg_match_all('/403 Forbidden/', $cont)) {
      $cont = self::get_cURL($proxy . 'https%3A%2F%2Fteleguide.info%2Fkanals.html&b=24&f=norefer');
      $c = array("#\/browse\.php\?u=https%3A%2F%2Fteleguide\.info%2F#is", "#\&amp\;b=24#is");
      $r = array("/", "");
      $cont = preg_replace($c, $r, $cont);
    }
    if (preg_match_all('/vsetv\.com/', $url)) {
      $cont = iconv("windows-1251", "utf-8", $cont);
    }
    if (preg_match_all('/teleguide\.info/', $url)) {
      $utf = explode('<div id="channel-list-original">', $cont);
      $cont = strstr($utf[1], '<div id="footer-inner-left">', true);
    }
    preg_match_all($pattern, $cont, $matches);
    $cont = "";

    for ($i = 0; $i < count($matches[1]); $i++) {
      $tempChannel = new ChannelClass();
      $tempChannel->ID = $matches[1][$i];
      $tempChannel->Name = $matches[2][$i];
      $ChannelsInfo[$i] = $tempChannel;
    }
    if (isset($ChannelsInfo)) {
      sort($ChannelsInfo);
      $cont = "";
      for ($i = 0; $i < count($ChannelsInfo); $i++) {
        $cont = $cont . ($ChannelsInfo[$i]->Name . "|" . $ChannelsInfo[$i]->ID . "|\r\n");
      }
      file_put_contents($save, $cont);
    }
    return;
  }
  public static function SPIELFILM($TEXT)
  {
    $cont = self::get_cURL('https://m.tvspielfilm.de/sender/');
    $utf = explode('<div class="component channels all-channels abc-scroll">', $cont);
    $cont = strstr($utf[1], '</main>', true);
    preg_match_all('#<li>.*?\/sendungen\/(.*?)\,(.*?)\.html.*?<span>(.*?)<#is', $cont, $matches);
    $cont = "";
    // echo count($matches[1]);
    for ($i = 0; $i < count($matches[1]); $i++) {
      $tempChannel = new ChannelClass();
      $tempChannel->ID = $matches[2][$i];
      $tempChannel->Name = $matches[3][$i];
      $tempChannel->URL = $matches[1][$i] . "," . $matches[2][$i];
      $ChannelsInfo[$i] = $tempChannel;
    }
    if (isset($ChannelsInfo)) {
      sort($ChannelsInfo);
      // print_r($ChannelsInfo);
      for ($i = 0; $i < count($ChannelsInfo); $i++) {
        $cont = $cont . ($ChannelsInfo[$i]->Name . "|" . $ChannelsInfo[$i]->ID . "|" . $ChannelsInfo[$i]->URL . "|\r\n");
      }
      file_put_contents($TEXT, $cont);
    }
    return;
  }

  public static function sbby($TEXT)
  {
    $cont = self::get_cURL('https://tv.sb.by/');
    $utf = explode('<div class="col-md-6 mb-2 tv-list">', $cont);
    $cont = strstr($utf[1], '<div class="col-3 d-none d-lg-block overflow-hidden shadow-bottom pr-0">', true);
    preg_match_all('#stacje\/(.*?)\".*?>(.*?)<#is', $cont, $matches);
    $cont = "";
    for ($i = 0; $i < count($matches[1]); $i++) {
      $tempChannel = new ChannelClass();
      $tempChannel->ID = $matches[1][$i];
      $tempChannel->Name = $matches[2][$i];
      $ChannelsInfo[$i] = $tempChannel;
    }
    if (isset($ChannelsInfo)) {
      sort($ChannelsInfo);
      for ($i = 0; $i < count($ChannelsInfo); $i++) {
        $cont = $cont . ($ChannelsInfo[$i]->Name . "|" . $ChannelsInfo[$i]->ID . "|\r\n");
      }
      //echo file_get_contents('TELEMAN.txt');
      file_put_contents($TEXT, $cont);
    }
    return;
  }

  public static function teleman($TEXT)
  {
    $cont = self::get_cURL('https://www.teleman.pl/program-tv/stacje/CanalPlus-Sport');
    $utf = explode('<div id="content" class="container">', $cont);
    $cont = strstr($utf[1], '<div id="stationListing">', true);
    preg_match_all('#stacje\/(.*?)\".*?>(.*?)<#is', $cont, $matches);
    $cont = "";
    for ($i = 0; $i < count($matches[1]); $i++) {
      $tempChannel = new ChannelClass();
      $tempChannel->ID = $matches[1][$i];
      $tempChannel->Name = $matches[2][$i];
      $ChannelsInfo[$i] = $tempChannel;
    }
    if (isset($ChannelsInfo)) {
      sort($ChannelsInfo);
      for ($i = 0; $i < count($ChannelsInfo); $i++) {
        $cont = $cont . ($ChannelsInfo[$i]->Name . "|" . $ChannelsInfo[$i]->ID . "|\r\n");
      }
      //echo file_get_contents('TELEMAN.txt');
      file_put_contents($TEXT, $cont);
    }
    return;
  }
  public static function stv()
  {
    $cont = self::get_cURL('http://www.s-tv.ru/tv/');
    $utf = explode('<div class="channels-list">', $cont);
    $cont = strstr($utf[1], '<div class="tv-cats"', true);
    preg_match_all('#<a href="\/tv\/(.*?)\/\"><img src=.*?alt="" /><span>(.*?)<\/span>#is', $cont, $matches);
    $cont = "";
    for ($i = 0; $i < count($matches[1]); $i++) {
      $tempChannel = new ChannelClass();
      $tempChannel->ID = $matches[1][$i];
      $tempChannel->Name = $matches[2][$i];
      $ChannelsInfo[$i] = $tempChannel;
    }
    if (isset($ChannelsInfo)) {
      sort($ChannelsInfo);
      for ($i = 0; $i < count($ChannelsInfo); $i++) {
        $cont = $cont . ($ChannelsInfo[$i]->Name . "|" . $ChannelsInfo[$i]->ID . "|\r\n");
      }
      file_put_contents("STV.txt", $cont);
    }
    return;
  }
  public static function skydelist()
  {
    $jsons = self::get_sky('https://www.sky.de/sgtvg/service/getChannelList');
    $jsons = json_decode($jsons, true);
    $cont = "";
    for ($i = 0; $i < count($jsons['channelList']); $i++) {
      $tempChannel = new ChannelClass();
      $tempChannel->Name = $jsons['channelList'][$i]['name'];
      $tempChannel->LOGO = $jsons['channelList'][$i]['logo'];
      $tempChannel->CIL = $jsons['channelList'][$i]['id'];
      $ChannelsInfo[$i] = $tempChannel;
    }
    if (isset($ChannelsInfo)) {
      sort($ChannelsInfo);
      for ($i = 0; $i < count($ChannelsInfo); $i++) {
        $cont = $cont . ("|" . $ChannelsInfo[$i]->Name . "|" . $ChannelsInfo[$i]->CIL . "|" . $ChannelsInfo[$i]->LOGO . "|\r\n");
      }
      file_put_contents("SKYDE.txt", $cont);
    }
    return;
  }

  public static function SKYCOM($TEXT)
  {

    $regions = array('4101/1');
    $cont = "";
    for ($i = 0; $i < count($regions); $i++) {
      $cont .= file_get_contents('https://awk.epgsky.com/hawk/linear/services/' . $regions[$i]);
    }
    $jsons = json_decode($cont, true);
    $cont = "";
    count($jsons['services']);
    // foreach ($jsons['services'] as $value) {
    for ($i = 0; $i < count($jsons['services']); $i++) {
      $tempChannel = new ChannelClass();
      $tempChannel->Name = $jsons['services'][$i]['t'];
      // echo $jsons['services'][$i]['t'];
      // $tempChannel->LOGO = $jsons['channelList'][$i]['logo'];
      $tempChannel->ID = $jsons['services'][$i]['sid'];
      $ChannelsInfo[$i] = $tempChannel;
    }

    sort($ChannelsInfo);

    if (isset($ChannelsInfo)) {
      $cont = "";
      $Prev = $ChannelsInfo[0]->Name;
      for ($i = 0; $i < count($ChannelsInfo); $i++) {
        if ($i > 0) {
          if ($ChannelsInfo[$i]->Name == $Prev) {
            goto skipthis;
          }
        }

        $cont = $cont . ($ChannelsInfo[$i]->Name . "|" . $ChannelsInfo[$i]->ID . "|\r\n");
        $Prev = $ChannelsInfo[$i]->Name;
        skipthis:;
      }
      file_put_contents($TEXT, $cont);
      //header("Location: TeleguideHelper4.php");
    }
  }
  public static function get_sky($link)
  {
    $cookie = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (SmartHub; SMART-TV; U; Linux/SmartTV; Maple2012) AppleWebKit/534.7 (KHTML, like Gecko) SmartTV Safari/534.7');
    $data = $cookie . curl_exec($ch);
    curl_setopt($ch, CURLOPT_POST, true);
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
  public static function get_cURL($link)
  {
    $cookie = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (SmartHub; SMART-TV; U; Linux/SmartTV; Maple2012) AppleWebKit/534.7 (KHTML, like Gecko) SmartTV Safari/534.7');
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
  public static function post_cURL($data, $head, $tost)
  {
    $cookie = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $data);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $tost);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $data = curl_exec($ch);
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
