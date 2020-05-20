<?php
//require_once('guidex.php');

require_once('setup.php');
@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('expose_php = off', false);
$allowed_regions = array(40000, 40001, 40003, 40004, 40005, 40006, 40006, 40007, 40009);

if (isset($_POST['password'])) {
  if ($_POST['password'] === $SERVICE_PASS) {
    if (isset($_POST['yandex'])) {
      guidex::yandex(trim($_POST['yandex']));
      exit;
    }
    if (isset($_POST['mailru'])) {
      guidex::mailru(trim($_POST['mailru']));
      exit;
    }
    if (isset($_POST['region'])) {
      guidex::region(trim($_POST['region']));
      exit;
    }
  }
}

if (isset($_GET['reg'])) {
  $reg = (int) $_GET['reg'];
} else {
  $reg = 0;
}

if (isset($_GET['friendly'])) {
  $friendly = true;
} else {
  $friendly = false;
}


if (isset($_GET['channel'])) {
  $channel = $_GET['channel'];
} else {
  $channel = 0;
}

if (isset($_GET['day'])) {
  $day = (int) $_GET['day'];
} else {
  $day = -1;
}

switch ($reg) {
  case 40000:;
    require_once('classTeleguide.php');
    $Teleguide = new classTeleguide($channel, $day);
    break;
  case 40001:;
    require_once('classSTV.php');
    $Teleguide = new classSTV($channel, $day);
    break;
  case 40003:;
    require_once('classVSETV.php');
    $Teleguide = new classVSETV($channel, $day);
    break;
  case 40004:;
    require_once('classSKY.php');
    $Teleguide = new classSKY($channel, $day);
    break;
  case 40005:;
    require_once('classTELEMANTV.php');
    $Teleguide = new classTELEMANTV($channel, $day);
    break;
  case 40006:;
    require_once('classSKY_DE.php');
    $Teleguide = new classSKY_DE($channel, $day);
    break;
  case 40007:;
    require_once('classsPielfilm.php');
    $Teleguide = new classsPielfilm($channel, $day);
    break;
  case 40009:;
    require_once('classSbBy.php');
    $Teleguide = new classSbBy($channel, $day);
    break;
  default:;
    die();
}
$Teleguide->Get();

if (!$friendly) {
  echo $Teleguide->Content;
} else {
  echo $Teleguide->UserFriendlyContent();
}
if (!in_array($reg, $allowed_regions) || $channel == 0) {
  $nz_time = new DateTime(null);
  $t = $nz_time->format('Y-m-d H:i:s');
  @file_put_contents('TeleguideRequests', $t . ' "WRONG REQUEST!" "' . $_SERVER['QUERY_STRING'] . '" "' . $_SERVER['REMOTE_ADDR'] . '" "' . $_SERVER['HTTP_USER_AGENT'] . '"' . "\r\n", FILE_APPEND);
  die();
}
