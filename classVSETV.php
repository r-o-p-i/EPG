<?php
include_once('setup.php');
require_once('Up.php');
@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('expose_php = off', true);

class classVSETV
{

  public $CACHEDIR = '';
  public $day;
  public $prog_date = '';
  public $download_date = '';
  public $isCached = false;
  public $Content = '';
  public $channel;
  private $tempDate;
  public $kill_age_restriction = true;
  private $LoadNextDateTailFunc;
  public $channels;
  private $ChannelURL = '';
  public $FirstTimestamp = null;
  public $LastTimestamp = null;
  public $ItemsCount = 0;
  public $arrTimestamps = array();
  public $arrTitles = array();
  public $arrInfos = array();
  function ResetItems()
  {
    $this->ItemsCount = 0;
    unset($this->arrTimestamps);
    unset($this->arrTitles);
    unset($this->arrInfos);
    $this->LastTimestamp = null;
    $this->FirstTimestamp = null;
  }
  function ParseContent()
  {
    $this->ResetItems();
    if ($this->Content <> '') {
      preg_match_all('#(\d+)\|(.*?)\|(.*?)\r\n#m', $this->Content, $matches);
      if (isset($matches[1])) {
        $this->ItemsCount = count($matches[1]);
        for ($i = 0; $i < $this->ItemsCount; $i++) {
          $this->arrTimestamps[$i] = $matches[1][$i];
          $this->arrTitles[$i] = $matches[2][$i];
          $this->arrInfos[$i] = $matches[3][$i];
        }
        $this->FirstTimestamp = $this->arrTimestamps[0];
        $this->LastTimestamp = $this->arrTimestamps[$this->ItemsCount - 1];
      }
    }
  }
  function GetChannelURL()
  {
    global $TIMESET;
    $ftime = filemtime('VSETV.txt');
    if (@file_exists('VSETV.txt') && ($ftime + $TIMESET) < time()) {
      require_once('Up.php');
      Up::dream('http://www.vsetv.com/channels.html', '/\<option value=channel_([0-9]{1,})\>([^\<]{1,})\<\/option>/', 'VSETV.txt');
    }
    $this->ChannelURL = 'http://www.vsetv.com/schedule_channel_' . $this->channel . '_day_';
  }
  function __construct($channel = -1, $day = -1, $kill_age_restriction = true, $LoadNextDateTailFunc = true)
  {
    global $DEFAULT_TZ;
    $this->channel = $channel;
    $this->LoadNextDateTailFunc = $LoadNextDateTailFunc;
    $this->isCached = false;
    $this->kill_age_restriction = $kill_age_restriction;
    $this->CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_VSETV/';
    if (!is_dir($this->CACHEDIR)) {
      mkdir($this->CACHEDIR);
      @file_put_contents($this->CACHEDIR . 'index.html', '');
      @file_put_contents($this->CACHEDIR . 'index.php', "<?php\r\n?>");
      @file_put_contents($this->CACHEDIR . 'robots.txt', "User-agent: *\r\nDisallow: \/\r\n");
    }
    if ($day == -1) {
      $this->day = (int)(time() / 86400);
    } else {
      $this->day = $day;
    }
    $this->cur_day = (int)(time() / 86400);
    $this->tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));
    $this->tempDate->setTimestamp($this->day * 86400);
    $this->prog_date = $this->tempDate->format('Ymd');
    $this->download_date = $this->tempDate->format('Y-m-d');
  }
  function Get()
  {
    global $DEFAULT_TZ;
    if (($this->day < $this->cur_day - 10) || ($this->day > $this->cur_day + 10)) {
      $this->Content = "";
      goto LOG_LABEL;
    }
    $try_counter = 0;
    TRY_LABEL:;
    $try_counter = $try_counter + 1;
    if (@file_exists($this->CACHEDIR . $this->channel . '.' . $this->prog_date)) {
      $this->isCached = true;
      $this->Content = @file_get_contents($this->CACHEDIR . $this->channel . '.' . $this->prog_date);
      if ($this->LoadNextDateTailFunc) {
        $this->LoadNextDateTail();
      }
    } else {
      $this->GetChannelURL();
      if ($this->ChannelURL == '') {
        $this->Content = '';
        return $this->Content;
      }
      $cont = self::get_cURL($this->ChannelURL . $this->download_date . '.html');
      $list = self::get_cURL('http://www.vsetv.com/jquery-gs.js');
      preg_match_all('/\(document\)\.ready\(function\(\)\{(.*?)\(\)\;\}\)/is', $cont, $n1);
      $utf = explode('function ' . $n1[1][0] . '() {', $list);
      $list = strstr($utf[1], '});', true);
      preg_match('|url: \"(.*?)\"|is', $list, $matches);
      $j = json_decode(str_replace(".", "class=", self::post_cURL("http://www.vsetv.com" . $matches[1])), true);
      preg_match_all('#\$\((.*?)\)\.replaceWith\(\"(.*?)\"\)\;#is', $list, $m);
      $utf = explode('<div class="chnum">1</div>', $cont);
      $cont = strstr($utf[1], '<div class="adver">', true);
      $cont = iconv("windows-1251", "utf-8", $cont);
      $patPic = '/<img src=\"(.*?)\">/';
      $s = '';
      preg_match_all($patPic, $cont, $picMat);
      for ($j = 0; $j <= count($picMat[1]); $j++) {
        if (strpos($s, $picMat[1][$j]) === false) {
          $s .= $picMat[1][$j] . '|';
        }
      }
      //  echo $s;
      $res = explode("|", $s);
      for ($j = 0; $j <= count($res); $j++) {
        /// echo $res[$j];
        if ($res[$j]) {
          $urs = @file_get_contents('http://www.vsetv.com' . $res[$j]);
          file_put_contents('PIC/pic_' . $j, $urs);
          $num = filesize('PIC/pic_' . $j);
          //echo $num . '<br>';
          if ($num == 1213) {
            $cont = str_replace('<img src="' . $res[$j] . '">', "0", $cont);
          }
          if ($num == 1217) {
            $cont = str_replace('<img src="' . $res[$j] . '">', "5", $cont);
          }
        }
      }

      $a[0] = "<a " . $j[$m[1][0]] . "></a>";
      $a[1] = "<a " . $j[$m[1][1]] . "></a>";
      $a[2] = "<a " . $j[$m[1][2]] . "></a>";
      $a[3] = "<a " . $j[$m[1][3]] . "></a>";
      $a[4] = "<a " . $j[$m[1][4]] . "></a>";
      $a[5] = "<a " . $j[$m[1][5]] . "></a>";
      $c = array("~$a[0]~", "~$a[1]~", "~$a[2]~", "~$a[3]~", "~$a[4]~", "~$a[5]~", "~&.*?;~", "~<a href=.*?>~", "~<div id=desc.*?>~");/*"~<img.*?>~",*/
      $r = array($m[2][0], $m[2][1], $m[2][2], $m[2][3], $m[2][4], $m[2][5], "", "", "");/*, ""*/
      $cont = preg_replace($c, $r, $cont);
      $pattern = '/class=\"(pasttime|onair|time)\">(.*?)<\/div><div class=\".*?\">(.*?)<\/div>(<div class=\"desc\">(.*?)<\/div>|)/is';
      preg_match_all($pattern, $cont, $TITLES);
      //  var_dump($cont);

      if (isset($TITLES[2][0])) {
        for ($i = 0; $i < count($TITLES[2]); $i++) {
          $TITLES[3][$i] = self::txt($TITLES[3][$i]);
          $TITLES[4][$i] = ($TITLES[4][$i] != "") ? "<table fontSize='20px'><table><tr><td style='vertical-align:top;padding-right:8px'></td><td style='color:#00ccff;'><h3> " . self::txt($TITLES[3][$i]) . "<br><br>" . $this->tempDate->format('Y m d') . "</h3></td></tr> </table></font></div>  <div class='genre' style='font-size: 16px;'> <b style='color: #00ccff; font-weight: bold;'> Краткое описание: </b><font color='BEBEBE'>" . self::txt($TITLES[4][$i]) . "</font></div> </div></div> </table> \r\n" : "\r\n";
        }
        if (!isset($TITLES[2][0])) {
          if ($try_counter < 2) {
            $tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));
            $tempDate->setTimestamp(($this->day - 1) * 86400);
            $this->download_date = $tempDate->format('Ymd');
            goto TRY_LABEL;
          }
        }
        $timeshift = 0;
        if (isset($TITLES[2][0])) {
          if (($TITLES[2][0] >= '00:00' && $TITLES[2][0] <= '03:30') && $try_counter == 1) {
            $timeshift = 86400;
          }
        }
        $prog = "";
        for ($i = 0; $i < count($TITLES[2]); $i++) {
          $temp = new DateTime($this->tempDate->format('Y-m-d') . ' ' . $TITLES[2][$i], new DateTimezone($DEFAULT_TZ));
          $t = $temp->getTimestamp();
          if (isset($prevtime)) {
            if ($t < $prevtime) {
              $t += 86400;
            }
          }
          $prevtime = $t;
          $TIME[$i] = $t;
        }
        date_default_timezone_set($DEFAULT_TZ);
        for ($i = 0; $i < count($TIME); $i++) {
          $t = $TIME[$i] + $timeshift + 60 * 60 * (idate('Z', time()) / 3600);
          $prog = $prog . $t . '|' . self::txt($TITLES[3][$i]) . '|' . $TITLES[4][$i];
        }
        if ($prog <> "") {
          $this->isCached = false;
          $this->Content = $prog;
          if ($this->LoadNextDateTailFunc) {
            $this->LoadNextDateTail();
          }
          @file_put_contents($this->CACHEDIR . $this->channel . '.' . $this->prog_date, $this->Content);
        } else {
          $this->isCached = false;
          $this->Content = time() . '  Внутренняя ошибка парсера';
        }
      } else {
        $this->isCached = false;
        $this->Content = time() . '  Внутренняя ошибка парсера';
      }
    }
    LOG_LABEL:;
    if ($this->kill_age_restriction) {
      $this->Content = preg_replace("'\s+\[[0-9]{1,2}\+\]'si", "", $this->Content);
    }
    $nz_time = new DateTime(null, new DateTimezone($DEFAULT_TZ));
    $t = $nz_time->format('Y-m-d H:i:s');
    if ($this->LoadNextDateTailFunc) {
      if ($this->Content == "") $void = '"VOID" ';
      else $void = '';
    }
    return $this->Content;
  }
  function TimestampToYYYMMDD($timestamp)
  {
    date_default_timezone_set('UTC');
    return date("Y-m-d H:i", $timestamp);
  }
  function LoadNextDateTail()
  {
    $addendum = '';
    global $DEFAULT_TZ;
    if ($this->channel <> 9999999) {
      $tempDate = new DateTime($this->tempDate->format('Y-m-d') . ' 00:00', new DateTimezone($DEFAULT_TZ));
      $this->ParseContent();
      $firsttimestamp = $this->LastTimestamp + 1;
      $lasttimestamp = $tempDate->getTimestamp() + 86400 + 60 * 60 * 5 + (idate('Z', time()) / 3600) * 60 * 60 + 10;
      $tempTeleguide = new classVSETV($this->channel, $this->day + 1, $this->kill_age_restriction, false);
      $tempTeleguide->Get();
      $tempTeleguide->ParseContent();
      for ($i = 0; $i < $tempTeleguide->ItemsCount; $i++) {
        if ($tempTeleguide->arrTimestamps[$i] >= $firsttimestamp && $tempTeleguide->arrTimestamps[$i] < $lasttimestamp) {
          $addendum = $addendum . $tempTeleguide->arrTimestamps[$i] . '|' . $tempTeleguide->arrTitles[$i] . "\r\n";
        }
      }
      $this->Content = $this->Content . $addendum;
    }
    return $addendum;
  }
  function UserFriendlyContent($hoursshift = 0)
  {
    $cont = '';
    $pattern = '#(\d+)\|(.*?)\|.*?\r\n#m';
    $this->ParseContent();
    for ($i = 0; $i < $this->ItemsCount; $i++) {
      $cont = $cont . $this->TimestampToYYYMMDD($this->arrTimestamps[$i] + $hoursshift * 3600) . ' | ' . $this->arrTitles[$i] . "\r\n";
    }
    return $cont;
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
  public static function post_cURL($data)
  {
    $cookie = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $data);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: */*', 'Accept-Encoding: gzip, deflate', 'X-Requested-With: XMLHttpRequest'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (SmartHub; SMART-TV; U; Linux/SmartTV; Maple2012) AppleWebKit/534.7 (KHTML, like Gecko) SmartTV Safari/534.7');
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
  public static function txt($s)
  {
    $ch = array("'<script[^>]*?>.*?</script>'si", "'&#.*?;'is", "'&.*?;'is", "'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'", "'&#(\d+);'", "\"\n\"", "\"\r\"", "'/\s+/is'", "'/class=b/is'", "'/href=.*?html/is'", "'|href=.*?html\"|'");
    $r = array("", "", "", "\\1", "", "", "", "", "", "", "", "");
    $string = preg_replace($ch, $r, $s);
    return $string;
  }
}
