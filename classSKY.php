<?php
@ini_set('display_errors', true);
@ini_set('html_errors', true);
include_once('setup.php');
function convert($t)
{
  global $DEFAULT_TZ;
  $tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));
  $tempDate->setTimestamp($t);

  return ($tempDate->format('Y-m-d H:i:s'));
}

class Program
{
  public $Time = '';
  public $Title;
  public $TitleLong;
  //    public $Description;     
}

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

class classSKY
{
  //    private $DoNotCACHE=array(999999=>'1');

  public $TZ = 3;
  public $CACHEDIR = '';
  public $day;
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
        //return;

        for ($i = 0; $i < $this->ItemsCount; $i++) {
          $this->arrTimestamps[$i] = $matches[1][$i];
          $this->arrTitles[$i] = $matches[2][$i];
          $this->arrInfos[$i] = $matches[3][$i];
        }



        $this->FirstTimestamp = $this->arrTimestamps[0];
        $this->LastTimestamp = $this->arrTimestamps[$this->ItemsCount - 1];
        //else {$this->FirstTimestamp=null;$this->LastTimestamp=null;}



      }
    }
  }

  function GetChannelURL()
  {
    $this->ChannelURL = 'https://awk.epgsky.com/hawk/linear/schedule/';
  }


  function __construct($channel = -1, $day = -1, $kill_age_restriction = false, $LoadNextDateTailFunc = true)
  {
    self::GetChannelURL();
    $this->channel = $channel;

    //      echo $this->ChannelURL;die();

    global $DEFAULT_TZ;
    $dateTimeZoneMoscow = new DateTimeZone($DEFAULT_TZ);
    $dateTimeZoneLondon = new DateTimeZone("Europe/London");
    $dateTimeMoscow = new DateTime("now", $dateTimeZoneMoscow);
    $dateTimeLondon = new DateTime("now", $dateTimeZoneLondon);
    $this->TZ = ($dateTimeZoneMoscow->getOffset($dateTimeLondon)) / 3600;



    $this->LoadNextDateTailFunc = $LoadNextDateTailFunc;

    $this->isCached = false;
    $this->kill_age_restriction = $kill_age_restriction;
    $this->CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_SKY/';
    if (!is_dir($this->CACHEDIR)) {
      mkdir($this->CACHEDIR);
      @file_put_contents($this->CACHEDIR . 'index.html', '');
      @file_put_contents($this->CACHEDIR . 'index.php', "<?php\r\n?>");
      @file_put_contents($this->CACHEDIR . 'robots.txt', "User-agent: *\r\nDisallow: \/\r\n");
    }

    echo $this->Content;


    if ($day == -1) {
      $this->day = (int) (time() / 86400);
    } else {
      $this->day = $day;
    }

    $this->tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));

    $this->tempDate->setTimestamp($this->day * 86400);

    $this->prog_date = $this->tempDate->format('Ymd');
    $this->download_date = $this->tempDate->format('Ymd');
  }

  function Get()
  {
    global $DEFAULT_TZ;
    $try_counter = 0;


    //       $this->Content=$this->ChannelURL.$this->download_date.'/';
    //       return;







    TRY_LABEL:;
    $try_counter = $try_counter + 1;

    if (@file_exists($this->CACHEDIR . $this->channel . '.' . $this->prog_date)) // && $this->channel<>1378)
    {
      $this->CACHEDIR . $this->channel . '.' . $this->prog_date;
      $this->isCached = true;
      $this->Content = file_get_contents($this->CACHEDIR . $this->channel . '.' . $this->prog_date);
      $this->prog_date;
      $this->LoadNextDateTailFunc;
      if ($this->LoadNextDateTailFunc) $this->LoadNextDateTail();
    } else {
      //================================================


      // $this->GetChannelURL();

      if ($this->ChannelURL == '') {
        $this->Content = '';
        return $this->Content;
        // echo "bad";
      }
      //          echo $this->ChannelURL.$this->download_date.'/';



      $opts = array(
        'http' => array(
          'header' => "User-Agent: Opera/9.80 (Windows NT 6.1; Win64; x64) Presto/2.12.388 Version/12.17\r\n"
        )
      );


      $context = stream_context_create($opts);
      // '.$prog_date.'/'.$this->channel.'/'
      $cont = "";
      for ($i = 0; $i <= 1; $i++) {

        $cont = self::get_cURL($this->ChannelURL . '/' . $this->download_date . '/' . $this->channel, false, $context);
      }
      //echo $cont;
      // $cont = str_replace('"sid":', "DELETEME", $cont);
      // $cont = preg_replace("'DELETEME[0-9]{0,},'si", "", $cont);

      // $pattern = '/\{\"audioDescription\":[^\,]{0,}\,\"[^\"]{0,}\"\:\"([^\"]{0,})\"\,\"[^\"]{0,}\"\:\[[0-9]{0,}\,[^\]]{0,}\]\,\"[^\"]{0,}\"\:\"[^\"]{0,}\"\,\"s\"\:([0-9]{10})\,\"subtitleHearing\":[^\,]{0,},\"[^\"]{0,}\"\:\"([^\"]{0,})\"\,\"url\"\:\"[^\"]{0,}\"/';
      // preg_match_all($pattern, $cont, $TITLES);
      // print_r($jsons = json_decode($cont, true));
      //exit();
      $TITLES = explode('"st"', $cont);
      $Programs = array();
      // echo count($TITLES);
      for ($i = 1; $i < count($TITLES) - 1; $i++) {
        $temp = new Program;
        $temp->Time = self::Parse_R99($TITLES[$i], ':', ',');
        $temp->Title = self::Parse_R99($TITLES[$i], '"t":"', '",');
        $temp->TitleLong = self::Parse_R99($TITLES[$i], '"sy":"', '","');
        $Programs[$i] = $temp;
      }
      if (isset($Programs[0])) {
        $Prev = $Programs[0];
      }
      for ($i = 1; $i < count($Programs); $i++) {
        if ($Programs[$i]->Time == $Prev->Time) {
          unset($Programs[$i]);
        } else
          $Prev = $Programs[$i];
      }

      $Programs = array_values($Programs);

      for ($i = 0; $i < count($Programs); $i++) {
        preg_match_all('/[^\(]{0,}\((S[0-9]{1,}[\,]{0,1}\s+[E]{0,1}[e]{0,1}[p]{0,1}[\s]{0,}[0-9]{1,})\)|[^\(]{0,}\(([0-9]{1,}\/[0-9]{1,})\)/', $Programs[$i]->TitleLong, $matches);
        if (isset($matches[1][0])) {
          if ($matches[1][0] <> '')
            $Programs[$i]->Title = $Programs[$i]->Title . ' (' . $matches[1][0] . ')';
        }
        if (isset($matches[2][0])) {
          if ($matches[2][0] <> '')
            $Programs[$i]->Title = $Programs[$i]->Title . ' (' . $matches[2][0] . ')';
        }
      }
      if (isset($Programs[0])) //not empty
      {



        $prog = "";

        for ($i = 0; $i < count($Programs); $i++) {
          // $t=
          $prog = $prog . $Programs[$i]->Time . '|' . /*ReplaceEngl*/ str_replace('|', '\#/', ltrim($Programs[$i]->Title)) . '|' . str_replace('|', '\#/', ltrim($Programs[$i]->TitleLong)) . "\r\n";
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

    $nz_time = new DateTime(null, new DateTimezone($DEFAULT_TZ));
    $t = $nz_time->format('Y-m-d H:i:s');

    if ($this->LoadNextDateTailFunc) {
      // echo $this->Content;
      if ($this->Content == "") $void = '"VOID" ';
      else $void = '';
    }


    return $this->Content;
  }

  function TimestampToYYYMMDD($timestamp)
  {
    global $DEFAULT_TZ;
    $tempDate = new DateTime(null, new DateTimezone($DEFAULT_TZ));

    $tempDate->setTimestamp($timestamp - 60 * 60 * $this->TZ);
    return $tempDate->format('Y-m-d H:i');
  }

  function LoadNextDateTail()
  {
    global $DEFAULT_TZ;
    $addendum = '';
    if ($this->channel <> 9999999) {
      $tempDate = new DateTime($this->tempDate->format('Y-m-d') . ' 00:00', new DateTimezone($DEFAULT_TZ));
      $this->ParseContent();
      $firsttimestamp = $this->LastTimestamp + 1;
      $lasttimestamp = $tempDate->getTimestamp() + 86400 + 60 * 60 * 5 + $this->TZ * 60 * 60 + 10;
      $tempTeleguide = new classSKY($this->channel, $this->day + 1, $this->kill_age_restriction, false);
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
    for ($i = 0; $i < $this->ItemsCount; $i++) {
      $cont = $cont . $this->TimestampToYYYMMDD($this->arrTimestamps[$i] + $hoursshift * 3600) . '|' . $this->arrTitles[$i] . "\r\n";
    }
    return $cont;
  }
  function Parse_R99($p1, $p2, $p3, $end = 1)
  {
    if (strpos($p1, $p2) !== false) {
      $num1 = strpos($p1, $p2); //48
      $string = substr($p1, $num1 + strlen($p2));
      if (strpos($string, $p3) !== false) {
        $r98 = substr($string, 0, strpos($string, $p3));
        if ($end == 0) {
          return substr($string, 0, strlen($string));
        } else {
          return $r98;
        }
      } else {
        return 10;
      }
    } else {
      return 1;
    }
  }
  public static function get_cURL($link)
  {
    $cookie = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, 1);
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
}
