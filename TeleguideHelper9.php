<?php
require_once('classSbBy.php');
require_once('load_settings.php');
//ini_set('memory_limit', '512M');
$ver = phpversion();
$REG = 40009;
$Title = 'SBBY';
$SCRIPT =  'TeleguideHelper9.php';
$CACHEDIR = $_SERVER['DOCUMENT_ROOT'] . $CURDIR . 'PROGCACHE_GUIDE/';
$TEXT = 'CHANNELS_LISTS/' . $Title . '.txt';
headerHtml($Title, $REG);
CleanHtml($CACHEDIR);


if (isset($pass1)) {
  if ($pass1 === $SERVICE_PASS) {
    Up::sbby($TEXT);
    if (@file_exists($TEXT)) {
      successBaseUpdateHtml();
    } else {
      failBaseUpdateHtml();
    }
  } else {
    badPasswordHtml();
  }
}
if (@file_exists($TEXT)) {

  global $TIMESET;
  $ftime = filemtime($TEXT);
  $arr = @file($TEXT, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
}
if (!file_exists($TEXT) || ($ftime + $TIMESET) < time()) {
  Up::sbby($TEXT);
  if (@file_exists($TEXT)) {
    successBaseUpdateHtml();
    alerton();
  } else {
    failBaseUpdateHtml();
  }
}
print '<b style="font-size: 20px; text-shadow: 1px 1px 7px #e70e4b;color: #ffffff;">  Всего каналов: <font style="font-size: 25px; text-shadow: 1px 1px 1px #f2f6fc;color:#ea0a07">' . count($arr) . '</font>     Последнее обновление скрипта:  <font style="font-size: 25px; text-shadow: 1px 1px 1px #f2f6fc;color:#ea0a07">' . date("Y-m-d H:i", ($ftime + 3600 * $TIMES)) . '</font></b><br><br>
	   <b style="font-size: 20px; text-shadow: 1px 1px 7px #e70e4b;color: #ffffff;"> <form  class="form-inline"  action="' . $SCRIPT . '" method="POST">  Выберите канал: </b>';

print '<select class="form-control form-control-sm" name="channel">';
for ($i = 0; $i < count($arr); $i++) {
  preg_match('#^(.*?)\|(.*?)\|(.*?)\|#is', $arr[$i], $matches);
  $channels[$i][1] = $matches[1];
  $channels[$i][2] = $matches[2];
  $channels[$i][3] = $matches[3];
  if ($i == $cur_id) {
    print '<option selected value="' . $i . '">' . $channels[$i][1] . '</option>';
  } else {
    print '<option value="' . $i . '">' . $channels[$i][1] . '</option>';
  }
}
print '</select> <br><br>';
if (isset($_POST['stream'])) {
  $stream = trim($_POST['stream']);
  $stream_text = $stream;
} else {
  $stream = '';
  $stream_text = 'ЗДЕСЬ АДРЕС ПОТОКА';
}
if ($stream == "") {
  $stream_text = 'ЗДЕСЬ АДРЕС ПОТОКА';
}
$cur_timeshift = "";
echo '<div class="form-group">
      <label class="sr-only" for="stream">Адрес потока (опционально):</label>
      <input style="margin-left: auto; margin-right: auto; width: 100%;" type="text" class="form-control" id="stream" placeholder="Адрес потока (опционально):" value="' . $stream . '" name="stream" size="100">  </div><br><br><b style="font-size: 20px; text-shadow: 1px 1px 7px #e70e4b;color: #ffffff;"> Сдвиг по времени (часов): </b>  <select class="form-control form-control-sm" name="timeshift">';
for ($i = -12; $i <= 12; $i++) {
  if ($i == $cur_timeshift) {
    echo '<option selected value="' . $i . '">' . $i . '</option>';
  } else {
    echo '<option value="' . $i . '">' . $i . '</option>';
  }
}
echo '</select><br>';
if (isset($_POST['timeshift'])) {
  $cur_timeshift = $_POST['timeshift'];
} else {
  $cur_timeshift = 0;
}
echo '<input class="btn btn-primary" type="submit" value="Получить код!"> </form> <hr>
<div class="accordion">';
if (isset($_POST['channel'])) {
  $Logo = $channels[$_POST['channel']][3];
  if (isset($Logo)) {
    echo '<br><img src=' . $Logo . ' width="70" height="70"><br><br>';
    $LogoXML = "     <logo_30x30><![CDATA[" . $Logo . "]]></logo_30x30>";
    $LogoM3U = ' tvg-logo="' . $Logo . '"';
  } else {
    $LogoXML = '';
    $LogoM3U = '';
  }
  if (isset($cur_timeshift) && $cur_timeshift != 0) {
    $shift_code = '     <timeshift>' . $cur_timeshift . '</timeshift>' . "\r\n";
    $shift_code2 = " tvg-shift=" . $cur_timeshift;
  } else {
    $shift_code = "";
    $shift_code2 = "";
  }
  $script_name = 'teleguide.php';
  $cont = "<channel>
     <title><![CDATA[" . $channels[$_POST['channel']][1] . "]]></title>\r\n" . $LogoXML . "
     <description><![CDATA[epg_url:" . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $PORT . $CURDIR . $script_name . '|' . $REG . '|' . ($channels[$_POST['channel']][2]) . "]]></description>\r\n     <stream_url><![CDATA[" . $stream_text . "]]></stream_url>\r\n";
  $cont .= $shift_code;
  $cont .= "</channel>\r\n";
  $cont2 = '#EXTINF:0' . $shift_code2 . $LogoM3U . ' tvg-name="epg_url:' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $PORT . $CURDIR . $script_name . '|' . $REG . '|' . ($channels[$_POST['channel']][2]) . '", ' . str_replace('_', ' ', $channels[$_POST['channel']][1]) . "\r\n" . $stream_text . "\r\n";
  echo '<h3>  M3U </h3>  
    <p><textarea class="form-control"  rows="3">' . $cont2 . '</textarea></p>';
  echo '<h3>  XML  </h3>  
    <p><textarea class="form-control"  rows="8">' . $cont . '</textarea></p>';
  $cont3 = "";
  if ($age_restr == 1) {
    $Teleguide = new classSbBy($channels[$_POST['channel']][2], -1, true);
  } else {
    $Teleguide = new classSbBy($channels[$_POST['channel']][2], -1, false);
  }

  $cont3 = $Teleguide->Get();
  $cont4 = $Teleguide->UserFriendlyContent($_POST['timeshift']);
  $p = ($cont4 != "") ? '<h3> Тест </h3>' : '<h4> Данные не получены</h4>';
  echo  $p . '
     <p><textarea class="form-control" name="Text3"  rows="15">' . $cont4 . '</textarea></p>';
  $k = ($cont3 != "") ? '<h3> Вывод на телевизор </h3>' : '<h4> Данные не получены </h4>';
  echo $k . ' 
     <p><textarea class="form-control" name="Text3"  rows="15">' . $cont3 . '</textarea></p>';
}
footerHtml($SCRIPT);
