<?php
include_once('setup.php');
@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('expose_php = off', true);
function ReplaceEngl($str)
{
  $str = str_replace('e', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('x', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('a', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('c', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('y', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('o', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('E', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('X', iconv("windows-1251", 'UTF-8', 'X'), $str);
  $str = str_replace('A', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('C', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('M', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('T', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('H', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('K', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('P', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('B', iconv("windows-1251", 'UTF-8', '�'), $str);
  $str = str_replace('O', iconv("windows-1251", 'UTF-8', '�'), $str);
  return $str;
}
class classTELEMANTV
{
  private $ShiftedArray = array();
  public $TZ = 3;
  public $CACHEDIR = '';
  public $day;
  public $cur_day;
  public $prog_date = '';
  public $download_date = '';
  public $isCached = false;
  public $Content = '';
  public $channel;
  private $tempDate;
  public $kill_age_restriction = false;
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
      preg_match_all('/([0-9]{10})\|([^\|]{0,})\|([^\r]{0,})\r\n/', $this->Content, $matches);
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
    if (isset($this->channel)) {
      $this->ChannelURL = 'https://www.teleman.pl/program-tv/stacje/' . $this->channel . '?date=';
    } else {
      $this->ChannelURL = '';
    }
  }
  function __construct($channel = -1, $day = -1, $kill_age_restriction = false, $LoadNextDateTailFunc = true)
  {
    $this->channel = $channel;
    global $DEFAULT_TZ40005;
    $this->LoadNextDateTailFunc = $LoadNextDateTailFunc;
    $this->isCached = false;
    $this->kill_age_restriction = $kill_age_restriction;
    $this->CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_TELEMANTV/';
    if (!is_dir($this->CACHEDIR)) {
      mkdir($this->CACHEDIR);
      @file_put_contents($this->CACHEDIR . 'index.html', '');
      @file_put_contents($this->CACHEDIR . 'index.php', "<?php\r\n?>");
      @file_put_contents($this->CACHEDIR . 'robots.txt', "User-agent: *\r\nDisallow: \/\r\n");
    }
    if ($day == -1) {
      $this->day = (int) (time() / 86400);
    } else {
      $this->day = $day;
    }
    $this->cur_day = (int) (time() / 86400);
    $this->tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ40005));
    $this->tempDate->setTimestamp($this->day * 86400);
    $this->prog_date = $this->tempDate->format('Ymd');
    $this->download_date = $this->tempDate->format('Y-m-d');
    $this->dDate = new DateTime(null, new DateTimezone($DEFAULT_TZ40005));
    $this->dDate->setTimestamp($this->day * 86400);
  }
  function Get()
  {
    global $DEFAULT_TZ40005;
    global $TIMESET;
    $ftime = filemtime('CHANNELS_LISTS/TELEMAN.txt');
    if (@file_exists('CHANNELS_LISTS/TELEMAN.txt') && ($ftime + $TIMESET) < time()) {
      require_once('Up.php');
      Up::teleman('CHANNELS_LISTS/TELEMAN.txt');
    }
    if (($this->day < $this->cur_day - 10)   || (($this->day > $this->cur_day + 10))) {
      $this->Content = "";
      goto LOG_LABEL;
    }
    $try_counter = 0;
    TRY_LABEL:;
    $try_counter = $try_counter + 1;
    if (@file_exists($this->CACHEDIR . $this->channel . '.' . $this->prog_date)) {
      $this->isCached = true;
      $this->Content = @file_get_contents($this->CACHEDIR . $this->channel . '.' . $this->prog_date);
      if ($this->LoadNextDateTailFunc) $this->LoadNextDateTail();
    } else {
      $this->GetChannelURL();
      if ($this->ChannelURL == '') {
        $this->Content = '';
        return $this->Content;
      }
      $cont = self::get_cURL($this->ChannelURL . $this->download_date . '&hour=-1');
      $cont = preg_replace("'<span class=[^>]*?>'si", "", $cont);
      $cont = str_replace('</span>', '', $cont);
      $pattern = '~<em>(.*?)<\/em>(.*?)<\/a>(.*?)<\/li>~is';
      preg_match_all($pattern, $cont, $TITLES);
      if (isset($TITLES[1][0])) {
        $timeshift = 0;
        $prog = "";
        for ($i = 0; $i < count($TITLES[1]); $i++) {
          $temp = new DateTime($this->tempDate->format('Y-m-d') . ' ' . $TITLES[1][$i], new DateTimezone($DEFAULT_TZ40005));
          $t = $temp->getTimestamp();
          if (isset($prevtime)) {
            if ($t < $prevtime) {
              $t += 86400;
            }
          }
          $prevtime = $t;
          $TIME[$i] = $t;
        }
        date_default_timezone_set($DEFAULT_TZ40005);
        for ($i = 0; $i < count($TIME); $i++) {
          $t = $TIME[$i] + $timeshift + 60 * 60 * (idate('Z', time()) / 3600);
          $prog = $prog . $t . '|' . self::txt($TITLES[2][$i]) . '|' . self::txt($TITLES[3][$i]) . "\r\n";
        }
        if ($prog <> "") {
          $this->isCached = false;
          $this->Content = $prog;
          if ($this->LoadNextDateTailFunc) $this->LoadNextDateTail();
          @file_put_contents($this->CACHEDIR . $this->channel . '.' . $this->prog_date, $this->Content);
        } else {
          $this->isCached = false;
          $this->Content = '';
        }
      } else {
        $this->isCached = false;
        $this->Content = '';
      }
    }
    if ($this->kill_age_restriction) {
      $this->Content = preg_replace("'\s+\[[0-9]{1,2}\+\]'si", "", $this->Content);
    }
    LOG_LABEL:;
    $nz_time = new DateTime(null, new DateTimezone($DEFAULT_TZ40005));
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
    global $DEFAULT_TZ40005;
    $addendum = '';
    if ($this->channel <> 9999999) {
      $tempDate = new DateTime($this->tempDate->format('Y-m-d') . ' 00:00', new DateTimezone($DEFAULT_TZ40005));
      $this->ParseContent();
      $firsttimestamp = $this->LastTimestamp + 1;
      $add_shift = 0;
      if (in_array($this->channel, $this->ShiftedArray)) $add_shift = 86400;
      $lasttimestamp = $tempDate->getTimestamp() + 86400 + 60 * 60 * 7 + (idate('Z', time()) / 3600) * 60 * 60 + 10 + $add_shift;
      $tempTeleguide = new classTELEMANTV($this->channel, $this->day + 1, $this->kill_age_restriction, false);
      $tempTeleguide->Get();
      $tempTeleguide->ParseContent();
      for ($i = 0; $i < $tempTeleguide->ItemsCount; $i++) {
        if ($tempTeleguide->arrTimestamps[$i] >= $firsttimestamp && $tempTeleguide->arrTimestamps[$i] < $lasttimestamp) {
          $addendum = $addendum . $tempTeleguide->arrTimestamps[$i] . '|' . $tempTeleguide->arrTitles[$i] . '|' . $tempTeleguide->arrInfos[$i] . "\r\n";
        }
      }
      $this->Content = $this->Content . $addendum;
    }
    return $addendum;
  }
  function UserFriendlyContent($hoursshift = 0)
  {
    $cont = '';
    $pattern = '/([0-9]{10})\|([^\|]{0,})\|([^\r]{0,})\r\n/';
    $this->ParseContent();
    $last = $this->LastTimestamp + 1;
    for ($i = 0; $i < $this->ItemsCount; $i++) {
      $cont = $cont . $this->TimestampToYYYMMDD($this->arrTimestamps[$i] + $hoursshift * 3600) . '|' . $this->arrTitles[$i] . "\r\n";
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
  public static function txt($s)
  {
    $ch = array("'<script[^>]*?>.*?</script>'si", "'&#.*?;'is", "'&.*?;'is", "'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'", "'&#(\d+);'", "\"\n\"", "\"\r\"", "'/\s+/is'", "'/class=b/is'", "'/href=.*?html/is'", "'|href=.*?html\"|'", "'\(\d+\)'");
    $r = array("", "", "", "\\1", "", "", "", "", "", "", "", "", "");
    $string = preg_replace($ch, $r, $s);
    return $string;
  }
}
