<?php
require_once('setup.php');
require_once('Up.php');
session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('expose_php = off', true);


if (isset($_POST['pass']) && isset($_POST['all'])) {
  if (trim($_POST['pass']) <> $SERVICE_PASS) {
    return;
  }

  if ($_POST['pass'] === $SERVICE_PASS) {
    if (isset($_POST['all'])) {
      $all = true;
    } else {
      $all = false;
    }
    $C = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_';
    $name = array("GUIDE", "STV", "VSETV", "TELEMANTV", "SKY_DE", "SKY", "SPIELFILM", "SBBY");
    for ($i = 0; $i < count($name); $i++) {
      CleanUp::Clean($C . $name[$i] . '/');
    }
    return;
  }
}
class CleanUp
{

  public static function TimeClean()
  {
    $all = true;
    $C = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']) . '/PROGCACHE_';
    $name = array("GUIDE", "STV", "VSETV", "TELEMANTV", "SKY_DE", "SKY", "SPIELFILM", "SBBY");
    for ($i = 0; $i < count($name); $i++) {
      CleanUp::Clean($C . $name[$i] . '/');
    }
    Up::dream('http://www.teleguide.info/kanals.html', '#id=\"programm_logo3\"><a href=\"\/kanal(.*?)\.html\" title=\"(.*?)\"#is', 'CHANNELS_LIST/TELEGUIDE.txt');
    // Up::stv();
    Up::dream('http://www.vsetv.com/channels.html', '/\<option value=channel_([0-9]{1,})\>([^\<]{1,})\<\/option>/', 'CHANNELS_LIST/VSETV.txt');
    //Up::skydelist();
    Up::teleman('CHANNELS_LIST/TELEMAN.txt');
    Up::SPIELFILM('CHANNELS_LIST/SPIELFILM.txt');
    Up::sbby('CHANNELS_LIST/SBBY.txt');
    return true;
  }
  public static function Clean($directory)
  {
    global $all;
    if (!is_dir($directory)) {
      mkdir($directory);
      @file_put_contents($directory . 'index.html', '');
      @file_put_contents($directory . 'index.php', "<?php\r\n?>");
      @file_put_contents($directory . 'robots.txt', "User-agent: *\r\nDisallow: \/\r\n");
    }
    $arr = glob($directory . "*.[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]");
    $nz_time = new DateTime(null);
    $nz_time->modify('-10 day');
    $t = $nz_time->format('Ymd');
    if ($all) {
      for ($i = 0; $i < count($arr); $i++) {
        $fn = pathinfo($arr[$i], PATHINFO_BASENAME);
        unlink($arr[$i]);
      }
    } else {
      for ($i = 0; $i < count($arr); $i++) {
        $ext = pathinfo($arr[$i], PATHINFO_EXTENSION);
        if ($ext <= $t) {
          $fn = pathinfo($arr[$i], PATHINFO_BASENAME);
          unlink($arr[$i]);
        }
      }
    }
    return;
  }
}
