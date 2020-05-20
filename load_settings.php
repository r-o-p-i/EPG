<?php
require_once('setup.php');
require_once('CleanUp.php');
@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', true);
@ini_set('expose_php = off', false);
$ver = phpversion();
$data =  date('Y');
$pass = $_POST['pass'];
$pass1 = $_POST['pass1'];
$script_name = 'teleguide.php';
$CURDIR = str_replace("//", "/", dirname($_SERVER['SCRIPT_NAME']) . '/');
$arrLiNav = array(
	'OPTIONS' => 'tuning.php',
	'40000' => 'TeleguideHelper.php',
	/* '40001' => 'TeleguideHelper2.php', */
	'40003' => 'TeleguideHelper3.php',
	'40004' => 'TeleguideHelper4.php',
	'40005' => 'TeleguideHelper5.php',
	/* 	'40006' => 'TeleguideHelper6.php', */
	'40007' => 'TeleguideHelper7.php',
	/*'40008' => 'TeleguideHelper8.php',*/
	'40009' => 'TeleguideHelper9.php',
	/*	'40010' => 'TeleguideHelper10.php',*/
);

if (!isset($_SERVER['SERVER_PORT'])) {
	$PORT = '';
} else {
	$PORT = $_SERVER['SERVER_PORT'];
	if ($PORT <> '80') {
		$PORT = ':' . $PORT;
	} else {
		$PORT = '';
	}
}
if (isset($_POST['age_restr'])) {
	$age_restr = 1;
} else {
	$age_restr = 0;
}
if (isset($_POST['channel'])) {
	$cur_id = $_POST['channel'];
} else {
	$cur_id = -1;
}
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
if (isset($_POST['timeshift'])) {
	$cur_timeshift = $_POST['timeshift'];
} else {
	$cur_timeshift = 0;
}
function navLi($REG)
{
	global $arrLiNav;
	foreach ($arrLiNav as $val => $key) {
		$res .= ($REG == $val) ? '<li class="active"><a href="' . $key . '">' . $val . '</a></li>' . PHP_EOL : '<li><a href="' . $key . '">' . $val . '</a></li>' . PHP_EOL;
	}
	return $res;
}
function GetLogo($channelid)
{
	global $proxy;
	return $proxy . urlencode('https://d2n0069hmnqmmx.cloudfront.net/epgdata/1.0/newchanlogos/100/40/skychb' . $channelid . '.png');
}
function headerHtml($Title, $REG)
{
	global $CURDIR;
	print '<!DOCTYPE html>
<html lang="en">
<head>
  <title>' . $Title . $REG . '</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="' . $CURDIR . 'js/bootstrap.min.css">
  <script src="' . $CURDIR . 'js/jquery.min.js"></script>
  <script src="' . $CURDIR . 'js/bootstrap.min.js"></script>
<style>
	html {
  position: relative;
  min-height: 100vh;
  }
  body {
  font-family: "Gilroy-Light", sans-serif !important 14px/12px ;
  background-image:url(' . $CURDIR . 'images/exchange_bg_space.jpg);
  margin-bottom: 4em;
  }
  .footer {
  position: absolute;
  bottom: 0;
  width: 100%;
  height: 60px;
  background-color:black;
  }
  .container {
  width: auto;
  max-width: 680px;
  padding: 0 15px;
	}
	.accordion h3{
  background:#e9e7e7 url(' . $CURDIR . 'images/arrow-square.gif) no-repeat right -51px;
  padding:8px 15px;
  margin:0;
  font:bold 90%/100% Arial, Helvetica, sans-serif;
  border:solid 1px #c4c4c4;
  border-bottom:none;
  cursor:pointer;
  color: black;
  }
	.accordion h3:hover{
  background-color:#e3e2e2;
  }
	.accordion h3{
  padding:8px 12px
  }
	.goog-zippy-expanded{
  background:#e9e7e7 url(' . $CURDIR . 'images/arrow-square.gif) no-repeat right 10px
  }
	.goog-zippy-collapsed{
  background:#e9e7e7 url(' . $CURDIR . 'images/arrow-square.gif) no-repeat right -46px
	}
 	h4 {
	font-size: 20px;
	text-shadow: 1px 1px 7px #e70e4b;
	color: #ffffff;
	} 
	.accordion h3.active{
  background-position:right 5px;
  }
	.accordion p{
  background:#f7f7f7;margin:0;
  padding:10px 15px 20px;
  border-left:solid 1px #c4c4c4;
  border-right:solid 1px #c4c4c4;
  }
	.alert {
	max-width: 300px;
	margin-left: auto;
	margin-right: auto;
	}
 </style>
</head>
<body>
<nav class="navbar navbar-inverse">
  <div class="navbar-header" >
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
     <span class="icon-bar"></span>
      <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
       <a class="navbar-brand"><img style="max-width:50px; margin-top: -2px;"
             src="' . $CURDIR . 'images/well.png"></a>
     <p class="navbar-text" style="font-size: 20px; text-shadow: 1px 1px 1px #f2f6fc"><font color=red><b>' . $Title . ' РЕГИОН ' . $REG . '</b></font></p>
    </div>
 <div class="collapse navbar-collapse" id="myNavbar">
  <ul class="nav navbar-nav" style="font-size: 15px; text-shadow: 1px 1px 1px #f2f6fc">
	 ';
	echo navLi($REG);
	print  '
   </ul>
 </div>
 </nav>
<div class="container-fluid">    
  <div class="row content">
    <div class="col-sm-2">
      </div>
     <div class="col-sm-8" id="texts">';
}
function alerton()
{
	print '<script type="text/javascript"> document.location.reload(); </script>';
}
function footerHtml($SCRIPT)
{
	global $CURDIR;
	global $data;
	echo '
	<script type="text/javascript">
 $(document).ready(function(){
	 $(".accordion h3:first").addClass("active");
	 $(".accordion p:not(:first)").hide();
	 $(".accordion h3").click(function(){
			 $(this).next("p").slideToggle("slow").siblings("p:visible").slideUp("slow");
			 $(this).toggleClass("active");
			 $(this).siblings("h3").removeClass("active");
		});
	$(".close").click(function(){
		 $(this).parent().alert("close");
		});
});
	</script><br>
	<form class="form-inline" action="' . $CURDIR . $SCRIPT . '" method="POST">
	 	<div class="input-group input">
			<span class="input-group-addon">
					<span class="glyphicon glyphicon-lock"></span>
			</span>
		 	<label class="sr-only" for="pass">Password:</label>
		 	<input type="hidden" name="cach" value="cach">
		 	<input type="password" class="form-control" id="pass" placeholder="Password"  name="pass"  required oninvalid="this.setCustomValidity(\'Введите пароль\')" oninput="setCustomValidity(\'\')">
	 	</div>
		<button class="btn btn-success" type="submit" >Очистить кеш</button>
 	</form>
 		</br>
 	<form class="form-inline" action="' . $CURDIR . $SCRIPT . '" method="POST">
		<div class="input-group input">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-lock"></span>
			</span>
			<label class="sr-only" for="pass1">Password:</label>
			<input type="hidden" name="news" value="news">
			<input type="password" class="form-control" id="pass1" placeholder="Password"  name="pass1" required oninvalid="this.setCustomValidity(\'Введите пароль\')" oninput="setCustomValidity(\'\')">
		</div>
	 	<button class="btn btn-success" type="submit" >Обновить каналы</button>
	</form></div></div>
	<div class="col-sm-2">
			 </div>
		</div>
	</div>
		<br><br>
		<footer class="footer text-center" id="footer">
			<div class="container">
			 <p class="text-muted"><br>&copy; by well ' . $data . ' all rights reserved.</p>
		 </div>
 </footer>
</body>
</html>';
}
function CleanHtml($CACHEDIR)
{
	global $pass;
	global $SERVICE_PASS;
	if (isset($pass)) {
		if ($pass === $SERVICE_PASS) {
			CleanUp::Clean($CACHEDIR);
			echo '<div class="alert alert-success"  role="alert">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Удалены файлы свыше 10 дней!</strong> </div>';
		} else {
			echo '<div class="alert alert-danger"  role="alert">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Внимание!</strong> Неверный пароль !</div>';
		}
	}
}
function successBaseUpdateHtml()
{
	print '<div class="alert alert-success"  role="alert">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>База обновлена !</strong> </div>';
}
function failBaseUpdateHtml()
{
	print '<div class="alert alert-warning"  role="alert">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>Проблема с обновлением списка каналов !</strong> </div>';
}
function badPasswordHtml()
{
	print '<div class="alert alert-danger"  role="alert">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>Внимание!</strong> Неверный пароль !</div>';;
}
