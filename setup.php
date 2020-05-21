<?php
@session_start();
@error_reporting(7);
@ini_set('display_errors', false);
@ini_set('html_errors', false);
@ini_set('expose_php = off', true);
$debug = false;
function debug($text)
{
  global $debug;
  if ($debug == true) {
    print_r('<pre style="bakcground:#000;">'
      . $text .
      '</pre>');
  }
}
$ver = phpversion();
$proxy = 'http://proxy.ayz.pl/browse.php?u='; // Прокси сервер 
$DEFAULT_TZ = 'Europe/Minsk'; // часовой пояс для 40001,40003,40009,40010
$DEFAULT_TZ40005 = 'Europe/Warsaw'; // часовой пояс для 40005
$DEFAULT_TZBerlin = 'Europe/Kiev'; // часовой пояс для 40006,40007
$SERVICE_PASS = 'test'; // обновление  и очистка
$YANDEX_PASS = 'test'; // для получения базы на стр. OPTIONS
$TIMESET = 604800; // автоматическое обновление списка каналов через  неделю
  /*
1 день 86400 секунд
1 неделя 604800 секунд
1 месяц (30.44 дней)  2629743 секунд
1 год (365.24 дней)   31556926 секунд
*/
