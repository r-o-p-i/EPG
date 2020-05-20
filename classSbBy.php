<?php
include_once('setup.php');
@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('expose_php = off', false);

class classSbBy
{

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
  private $LoadPrevDateTailFunc;


  function __construct($channel = -1, $day = -1, $kill_age_restriction = false, $LoadPrevDateTailFunc = true)
  {
    global $DEFAULT_TZ;
    $this->LoadPrevDateTailFunc = $LoadPrevDateTailFunc;
    $this->channel = $channel;
    $this->isCached = false;
    $this->kill_age_restriction = $kill_age_restriction;
    $this->CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_SBBY/';
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
    $this->tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));
    $this->tempDate->setTimestamp($this->day * 86400);
    $this->prog_date = $this->tempDate->format('Ymd');

    $this->download_date = $this->tempDate->format('Ymd');
    $this->downloadstart = mktime(0, 0, 0, $this->tempDate->format('m'), $this->tempDate->format('d'), $this->tempDate->format('Y'));
    $this->downloadend =  mktime(0, 0, 0, $this->tempDate->format('m'), $this->tempDate->format('d') + 1, $this->tempDate->format('Y'));
    // debug(date("Y.m.d-H:i", $this->downloadstart));
    //debug(date("Y.m.d-H:i", $this->downloadend));
  }
  function Get()
  {
    global $DEFAULT_TZ;
    global $TIMESET;
    $ftime = filemtime('CHANNELS_LISTS/SBBY.txt');
    if (@file_exists('CHANNELS_LISTS/SBBY.txt') && ($ftime + $TIMESET) < time()) {
      require_once('Up.php');
      Up::sbby('CHANNELS_LISTS/SBBY.txt');
    }
    if (($this->day < $this->cur_day - 10)   || (($this->day > $this->cur_day + 10))) {
      $this->Content = "";
      goto LOG_LABEL;
    }
    $try_counter = 0;
    TRY_LABEL:;
    $try_counter = $try_counter + 1;
    if (@file_exists($this->CACHEDIR . $this->channel . '.' . $this->download_date)) {
      $this->isCached = true;
      $this->Content = @file_get_contents($this->CACHEDIR . $this->channel . '.' . $this->download_date);
      // if ($this->LoadNextDateTailFunc) $this->LoadNextDateTail();
    } else {
      $CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/';
      $tent = @file_get_contents($CACHEDIR . 'CHANNELS_LISTS/SBBY.txt');
      $r = "#\|" . $this->channel . "\|(.*?)\|#m";
      preg_match_all($r, $tent, $m1);
      $cont = self::get_cURL('https://tv.sb.by/ajax/?CHANNEL_ID=' . $this->channel . '&DATE_START=' . $this->downloadstart . '&DATE_END=' . $this->downloadend);
      preg_match_all('#content=\".*?\>(\d+:\d+).*?<\/time>.*?\"name\"\>(.*?)<#is', $cont, $TITLES);
      count($TITLES[1]);
      for ($i = 0; $i < count($TITLES[1]); $i++) {
        $TITLES[2][$i] = self::txt($TITLES[2][$i]);
      }
      if (!isset($TITLES[1][0])) {
        if ($try_counter < 2) {
          $tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));
          $tempDate->setTimestamp(($this->day - 1) * 86400);
          $this->download_date = $tempDate->format('Ymd');
          goto TRY_LABEL;
        }
      }
      $timeshift = 0;
      if (isset($TITLES[1][0])) {
        if (($TITLES[1][0] >= '00:00' && $TITLES[1][0] <= '03:30') && $try_counter == 1) {
          $timeshift = 86400;
        }
      }
      $prog = "";
      for ($i = 0; $i < count($TITLES[1]); $i++) {
        $temp = new DateTime($this->tempDate->format('Ymd') . ' ' . $TITLES[1][$i], new DateTimezone($DEFAULT_TZ));
        $t = $temp->getTimestamp();
        if (isset($prevtime)) {
          if ($t < $prevtime) {
            $t += 86400;
          }
        }
        $prevtime = $t;
        $TIME[$i] = $t;
      }
      if (isset($TIME)) {
        date_default_timezone_set($DEFAULT_TZ);
        for ($i = 0; $i < count($TIME); $i++) {
          $t = $TIME[$i] + $timeshift + 60 * 60 * (idate('Z', time()) / 3600);
          $prog = $prog . $t . '|' . $TITLES[2][$i] . "\r\n";
        }
      }
      if ($prog <> "") {
        $this->isCached = false;
        $this->Content = $prog;
        if ($this->LoadPrevDateTailFunc) $this->LoadPrevDateTail();
        @file_put_contents($this->CACHEDIR . $this->channel . '.' . $this->prog_date, $this->Content);
      } else {
        $this->isCached = false;
        $this->Content = '';
      }
    }
    LOG_LABEL:;
    if ($this->kill_age_restriction) {
      $this->Content = preg_replace("'\s+\([0-9]{1,2}\+\)'si", "", $this->Content);
    }
    $nz_time = new DateTime(null, new DateTimezone($DEFAULT_TZ));
    $t = $nz_time->format('Y-m-d H:i:s');
    if (($this->isCached)) {
    } else {
      if ($this->Content == "") {
        $void = '"VOID" ';
      } else {
        $void = '';
      }
    }
    return $this->Content;
  }
  function TimestampToYYYMMDD($timestamp)
  {
    date_default_timezone_set('UTC');
    return date("Y-m-d H:i", $timestamp);
  }
  function LoadPrevDateTail()
  {
    global $DEFAULT_TZ;
    $addendum = '';
    if ($this->channel <> 9999999) {
      $cont = $this->Content;
      $tok = strtok($cont, "\r\n");
      if ($tok) {
        preg_match_all('/([0-9]{10})\|/', $tok, $matches);
        if (isset($matches[1][0])) {
          $lasttimestamp = $matches[1][0];
          $tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));
          $tempDate->setTimestamp($lasttimestamp);
          $firstdt = $tempDate->format('Y-m-d');
          $temp = new DateTime($firstdt . ' 01:00', new DateTimezone($DEFAULT_TZ));
          $firsttimestamp = $temp->getTimestamp();
          $tempTeleguide = new classSbBy($this->channel, $this->day - 1, $this->kill_age_restriction, false);
          $cont = $tempTeleguide->Get();
          preg_match_all('/([0-9]{10})\|([^\|]{0,})\r\n/', $cont, $matches);
          for ($i = 0; $i < count($matches[1]); $i++) {
            if ($matches[1][$i] >= $firsttimestamp && $matches[1][$i] < $lasttimestamp) {
              $addendum = $addendum . $matches[1][$i] . '|' . $matches[2][$i] . "\r\n";
            }
          }
        }
      }

      $this->Content = $addendum . $this->Content;
    }
  }
  function UserFriendlyContent($hoursshift = 0)
  {
    $cont = '';
    $pattern = '/([0-9]{10})\|([^\|]{0,})\r\n/';
    preg_match_all($pattern, $this->Content, $matches);
    for ($i = 0; $i < count($matches[1]); $i++) {
      $cont = $cont . $this->TimestampToYYYMMDD($matches[1][$i] + $hoursshift * 3600) . '|' . $matches[2][$i] . "\r\n";
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
    curl_setopt($ch, CURLOPT_COOKIE, $cookie . ' expires=Sat, 17-Sep-1970 09:53:42 GMT; path=/');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Mobile Safari/537.36');
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
    $ch = array("'<script[^>]*?>.*?</script>'si", "'\|'", "'&#.*?;'is", "'&.*?;'is", "'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'", "'&#(\d+);'", "\"\n\"", "\"\r\"", "'/\s+/is'");
    $r = array("", " ", "", "", "\\1", "", "", "", "", "");
    $string = preg_replace($ch, $r, $s);
    return $string;
  }
}
