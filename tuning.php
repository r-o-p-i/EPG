<?php
require_once('guidex.php');
require_once('setup.php');
require_once('CleanUp.php');
require_once('Up.php');
require_once('load_settings.php');
$ver = phpversion();
$SCRIPT = 'tuning.php';
$Title = 'OPTIONS';
if (isset($_POST['yandex'])) {
  guidex::yandex(trim($_POST['yandex']));
  exit;
}
if (isset($_POST['mailru'])) {
  guidex::mailru(trim($_POST['mailru']));
  exit;
}
if (isset($_POST['password'])) {
  if ($_POST['password'] === $SERVICE_PASS) {
    if (isset($_POST['delets'])) {
      $out4 = '';
      $del = guidex::Clean();
      if ($del === true) {
        $out4 = '<div class="alert alert-success" role="alert"  style="max-width: 600px;margin-left: auto;margin-right: auto;">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Полная очистка закончена, обновлены базы каналов!</strong> </div>';
      }
    }
  } else {
    $out4 = '<div class="alert alert-danger" role="alert">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Внимание!</strong> Неверный пароль !</div>';
  }
}
?>
<?php
$out2 = '<form class="form-inline" action="' . $_SERVER["PHP_SELF"] . '"  method="POST" >
<h4><b> Версия php: ' . $ver . '</b></h4><hr><br>
  <h4><b>Создание базы данных yandex:</b></h4>
    <div class="form-group">
      <select class="form-control form-control-sm" name="yandex">
<option value="all">Регион</option>
<option value="213">Москва </option>
<option value="157">Белорусь </option>
<option value="187">Украина </option>
<option value="54">Екатеринбург </option>
<option value="22">Калининград </option>
<option value="51">Самара </option>
<option value="66">Омск </option>
<option value="62">Красноярск </option>
<option value="63">Иркутск </option>
<option value="74">Якутск </option>
<option value="75">Владивосток </option>
<option value="79">Магадан </option>
<option value="78">Камчатка </option>
<option value="183">Азия </option>
<option value="225">Россия </option>
<option value="17">Северо-Западный округ </option>
<option value="10857">Калининградская область </option>
<option value="10897">Мурманская область </option>
<option value="10933">Республика Карелия </option>
<option value="10174">Санкт-Петербург и область</option>
<option value="10926">Псковская область </option>
<option value="10904">Новгородская область </option>
<option value="10819">Тверская область </option>
<option value="10795">Смоленская область </option>
<option value="10650">Брянская область </option>
<option value="10693">Калужская область </option>
<option value="10705">Курская область </option>
<option value="10772">Орловская область </option>
<option value="10832">Тульская область </option>
<option value="1">Московская область </option>
<option value="10645">Белгородская область </option>
<option value="10712">Липецкая область </option>
<option value="10841">Ярославская область </option>
<option value="10658">Владимирская область </option>
<option value="10687">Ивановская область </option>
<option value="10776">Рязанская область </option>
<option value="10802">Тамбовская область </option>
<option value="10672">Воронежская область </option>
<option value="26">Южный округ </option>
<option value="11029">Ростовская область </option>
<option value="10995">Краснодарский край </option>
<option value="11004">Республика Адыгея </option>
<option value="11020">Карачаево-Черкесская р-а </option>
<option value="11013">Кабардино-Балкарская р-а </option>
<option value="11021">Северная Осетия </option>
<option value="11012">Республика Ингушетия </option>
<option value="11024">Чеченская республика </option>
<option value="11010">Республика Дагестан </option>
<option value="11069">Ставропольский край </option>
<option value="11015">Республика Калмыкия </option>
<option value="10946">Астраханская область </option>
<option value="10950">Волгоградская область </option>
<option value="11146">Саратовская область </option>
<option value="11095">Пензенская область </option>
<option value="11117">Республика Мордовия </option>
<option value="11153">Ульяновская область </option>
<option value="11131">Самарская область </option>
<option value="11156">Чувашская республика </option>
<option value="11077">Республика Марий Эл </option>
<option value="11079">Нижегородская область </option>
<option value="11070">Кировская область </option>
<option value="10699">Костромская область </option>
<option value="10853">Вологодская область </option>
<option value="10842">Архангельская область </option>
<option value="10176">Ненецкий  округ </option>
<option value="10939">Республика Коми </option>
<option value="11148">Удмуртская республика </option>
<option value="11119">Республика Татарстан </option>
<option value="11108">Пермский край </option>
<option value="11111">Республика Башкортостан </option>
<option value="11084">Оренбургская область </option>
<option value="52">Уральский округ </option>
<option value="11225">Челябинская область </option>
<option value="11158">Курганская область </option>
<option value="11162">Свердловская область </option>
<option value="11176">Тюменская область </option>
<option value="11193">Ханты-Мансийский округ </option>
<option value="59">Сибирский округ </option>
<option value="11318">Омская область </option>
<option value="11316">Новосибирская область </option>
<option value="11353">Томская область </option>
<option value="11232">Ямало-Ненецкий округ </option>
<option value="11235">Алтайский край </option>
<option value="10231">Республика Алтай </option>
<option value="11282">Кемеровская область </option>
<option value="11340">Республика Хакасия </option>
<option value="10233">Республика Тыва </option>
<option value="11309">Красноярский край </option>
<option value="11266">Иркутская область </option>
<option value="11330">Республика Бурятия </option>
<option value="21949">Забайкальский край </option>
<option value="73">Дальневосточный округ </option>
<option value="11443">Республика Саха </option>
<option value="11375">Амурская область </option>
<option value="10243">Еврейская область </option>
<option value="11409">Приморский край </option>
<option value="10251">Чукотский округ </option>
<option value="11398">Камчатский край </option>
<option value="11403">Магаданская область </option>
<option value="11450">Сахалинская область </option>
<option value="11457">Хабаровский край </option>
</select>
    </div>
    <button class="btn btn-success" type="submit" >Создать базу</button>
  </form>
  <hr>
  <form class="form-inline" action="' . $_SERVER["PHP_SELF"] . '" method="POST">
  <h4><b>Создание базы mail ru:</b></h4>
    <div class="form-group">
      <select class="form-control form-control-sm" name="mailru">
<option value="not">Регион</option>
<option value="70">Москва </option>
<option value="285">Белорусь </option>
<option value="265">Украина </option>
<option value="32">Екатеринбург </option>
<option value="43">Калининград </option>
<option value="106">Самара </option>
<option value="92">Омск </option>
<option value="54">Красноярск </option>
<option value="40">Иркутск </option>
<option value="146">Якутск </option>
<option value="18">Владивосток </option>
<option value="62">Магадан </option>
<option value="98">Камчатка </option>
<option value="378">Германия </option>
</select>
    </div>
    <button class="btn btn-success" type="submit" >Создать базу</button>
  </form>
  <hr>
  
<form class="form-inline" action="' . $_SERVER["PHP_SELF"] . '" method="POST">
<h4><b>Полная очистка и обновление</b></h4>
    <div class="input-group input">
      <span class="input-group-addon">
       <span class="glyphicon glyphicon-lock"></span>
        </span>
       <input type="hidden" name="delets" value="trues">
      <input type="password" class="form-control"  placeholder="Password"  name="password" required oninvalid="this.setCustomValidity(\'Введите пароль\')" oninput="setCustomValidity(\'\')">
    </div>
    <button class="btn btn-success" type="submit" >Очистка и обновление</button>
  </form>
  <hr>
  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Коды регионов</button>
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Временной диапазон <br>SERVER TIME:';
$dateTimeSERVER = new DateTime('now');
$out3 = $dateTimeSERVER->format('H:i') . "<br>SERVER TimeZone: " . date_default_timezone_get() . '</h4>
        </div>
        <div class="modal-body">
          <p>
<script type="text/javascript"> 
var t = new Date();
var e = new Date(t.setMilliseconds(2*60*60*1000)); 
var j = "<br>yandex_22  mailru_43     Калининградское время MSK–1 (UTC+2)     <b> " +  to(e.getUTCHours(),e.getUTCMinutes())  + "</b>";
var w = new Date(t.setMilliseconds(1*60*60*1000));
j += "<br>yandex_213 mailru_70        Московское время MSK (UTC+3)     <b> " + to(w.getUTCHours(),w.getUTCMinutes()) + "</b>";
var u = new Date(t.setMilliseconds(1*60*60*1000));
j +="<br>yandex_51 mailru_106        Самарское время MSK+1 (UTC+4)     <b>  " + to(u.getUTCHours(),u.getUTCMinutes()) + "</b>";
var i = new Date(t.setMilliseconds(1*60*60*1000));
j +="<br>yandex_54 mailru_32         Екатеринбургское время MSK+2 (UTC+5)     <b> " + to(i.getUTCHours(),i.getUTCMinutes()) + "</b>";
var o = new Date(t.setMilliseconds(1*60*60*1000));
j += "<br>yandex_66 mailru_92        Омское время MSK+3 (UTC+6)      <b> " + to(o.getUTCHours(),o.getUTCMinutes()) + "</b>";
var a = new Date(t.setMilliseconds(1*60*60*1000));
j +="<br>yandex_62  mailru_54         Красноярское время MSK+4 (UTC+7)    <b> " + to(a.getUTCHours(),a.getUTCMinutes()) + "</b>";
var g = new Date(t.setMilliseconds(1*60*60*1000));
j += "<br>yandex_63 mailru_40        Иркутское время MSK+5 (UTC+8)     <b> " + to(g.getUTCHours(),g.getUTCMinutes()) + "</b>";
var d  = new Date(t.setMilliseconds(1*60*60*1000));
j +="<br>yandex_74 mailru_146        Якутское время MSK+6 (UTC+9)      <b> " + to(d.getUTCHours(), d.getUTCMinutes()) + "</b>";
var f = new Date(t.setMilliseconds(1*60*60*1000));
j +="<br>yandex_75 mailru_18         Владивостокское время MSK+7 (UTC+10)     <b> " + to(f.getUTCHours(),f.getUTCMinutes()) + "</b>";
var l = new Date(t.setMilliseconds(1*60*60*1000));
j +="<br>yandex_79 mailru_62         Магаданское время MSK+8 (UTC+11)    <b>" + to(l.getUTCHours(), l.getUTCMinutes()) + "</b>";
var m= new Date(t.setMilliseconds(1*60*60*1000));
j +="<br>yandex_78 mailru_98         Камчатское время MSK+9 (UTC+12)   <b> " + to(m.getUTCHours(),m.getUTCMinutes()) + "</b>";
function to(h, m) {
    return ((h > 9 ? h : "0" + h) + ":" + (m > 9 ? m : "0" + m));
};
document.write (j);
</script> </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
     </div>
   </div>
</div><br><br>
   <div class="col-sm-2">
        </div>
      </div>
     </div><hr>
     <footer class="footer text-center">
       <div class="container">
       <p class="text-muted"><br>&copy; by well ' . $data . ' all rights reserved.</p>
       </div>
       </footer>
      </body>
      </html>';
headerHtml($Title, $REG);
print_r($out4);
print_r($out2 . $out3);
?>