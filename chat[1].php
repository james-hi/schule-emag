<?php
################################################################################################
# Der Chat von webdesign.weisshart.de                                                          #
# Diese Datei nicht aendern!                                                                   #
# Alle Einstellungen koennen in der chat_config.php und in den CSS Dateien vorgenommen werden. #
# Weitere Hinweise in der readme.txt                                                           #
################################################################################################



if (file_exists("notr_inc.php")) include("notr_inc.php");
if (file_exists("nodl_inc.php")) include("nodl_inc.php");
header('Content-type: text/html; charset=utf-8');
ob_start("ob_gzhandler");


if(!isset($_SESSION)){session_start();}
if (!isset($popup)) {$popup = "disable";}
if(!isset($max_length)) $max_length = 500;


if (isset($_SESSION['chatuser'])) {$nick= $_SESSION['chatuser'];}
include("chat_config.php");

if (extension_loaded('gd')) {
	$gd = true;
} else {
	$gd = false;
}


// ein paar Variablen initialisieren:
$nickname = '';
$admintrue = '';
$modtrue = '';



// Setup: div Ordner erstellen usw.
if(file_exists("setup.php")) {include ("setup.php");} 

// Setup nur einmal aufrufen:
if (file_exists("setup.php") && file_exists("admin/.htaccess")) {
	@rename("setup.php","setup.bak");
}



// Variable fuer shortlink() setzen abhaengig vom per cleanup.php erstellten file:
$is_fp = false;
if (file_exists("fp.txt") && file_get_contents("fp.txt") == "yes") $is_fp = true;

// Sprache abfragen:
if (isset($_COOKIE["lang"])) {
	$lang = $_COOKIE["lang"];
} else {
      $lang = $languages[0];
}

function isMobile() {   
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
	    		if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT'])) {
	    	return true;
		} else {
	    	return false;
		}
	} else {
		echo "<p style='color:red'><br /><br />Dein Browser schickt keinen User Agent String. Du kannst den Chat mit dieser Einstellung leider nicht oder nur sehr eingeschränkt verwenden.<br />";
		return false;
	}
}


$lang_file = 'lang'.strtoupper($lang).'_inc.php';

$in_regdb = false;

include ($lang_file);


if (file_exists("admin/admins.txt") && file_exists("admin/admin_admins.php")) {
	$admins2 = file("admin/admins.txt",FILE_IGNORE_NEW_LINES); 
	$admins = array_merge($admins, $admins2); // und was, wenn kein $admins?
}

if (file_exists("admin/mods.txt") && file_exists("admin/admin_mods.php")) {
	$mods = file("admin/mods.txt",FILE_IGNORE_NEW_LINES); 
}



############# Anzeigeoptionen abfragen ##########
if (isset($stil) && $stil <> 0) {
        $s = $stil;
} elseif (isset($_COOKIE["Style"])) {
	$s = $_COOKIE["Style"];
} else {
        $s = $default_skin;
}

$con = time()+3600*24*360;
$coff = time()-3600*24*360;

################# reverse ###################
if (!isset($_COOKIE["rev"]))  {
	$rev = "0";
	$display_reverse = false;
} elseif (isset($_COOKIE["rev"]) && $_COOKIE["rev"] == "1")  {
	$rev = "1";
	$display_reverse = true;
}
if (isset($_POST['rev']) && $_POST['rev'] == "1") {
	$rev = "1";
	$display_reverse = true;
	setcookie('rev','1',$con);
} elseif (isset($_POST['rev']) && $_POST['rev'] == "0") {
	$rev = "0";
	$display_reverse = false;
	setcookie('rev','1',$coff);
}

################# mehrzeilig ###################
if (!isset($_COOKIE["mz"]))  {
	$m = "1";
} elseif (isset($_COOKIE["mz"]) && $_COOKIE["mz"] == "0")  {
	$m = "0";
}
if (isset($_POST['m']) && $_POST['m'] == "0") {
	$m = "0";
	setcookie('mz','0',$con );
} elseif (isset($_POST['m']) && $_POST['m'] == "1") {
	$m = "1";
	setcookie('mz','0',$coff);
}




################# Datum im Chatfenster ###################
if (!isset($_COOKIE["dc"]))  {
	$dc = "1";
} elseif (isset($_COOKIE["dc"]) && $_COOKIE["dc"] == "0")  {
	$dc = "0";
}
if (isset($_POST['dc']) && $_POST['dc'] == "0") {
	$dc = "0";
	setcookie('dc','0',$con );
} elseif (isset($_POST['dc']) && $_POST['dc'] == "1") {
	$dc = "1";
	setcookie('dc','0',$coff);
}

################# Uhr im Chatfenster ###################
if (!isset($_COOKIE["uhr"]))  {
	$uhr = "1";
} elseif (isset($_COOKIE["uhr"]) && $_COOKIE["uhr"] == "0")  {
	$uhr = "0";
}
if (isset($_POST['uhr']) && $_POST['uhr'] == "0") {
	$uhr = "0";
	setcookie('uhr','0',$con );
} elseif (isset($_POST['uhr']) && $_POST['uhr'] == "1") {
	$uhr = "1";
	setcookie('uhr','0',$coff);
}

################# Bilder ###################
if (!isset($_COOKIE["pic"]))  {
	$pic = "1";
} elseif (isset($_COOKIE["pic"]) && $_COOKIE["pic"] == "0")  {
	$pic = "0";
}
if (isset($_POST['pic']) && $_POST['pic'] == "0") {
	$pic = "0";
	setcookie('pic','0',$con );
} elseif (isset($_POST['pic']) && $_POST['pic'] == "1") {
	$pic = "1";
	setcookie('pic','0',$coff);
}

################# User-online-Anzeige ###################
if (!isset($_COOKIE["uoa"]))  {
	$uoa = "1";
} elseif (isset($_COOKIE["uoa"]) && $_COOKIE["uoa"] == "0")  {
	$uoa = "0";
}
if (isset($_POST['uoa']) && $_POST['uoa'] == "0") {
	$uoa = "0";
	setcookie('uoa','0',$con );
} elseif (isset($_POST['uoa']) && $_POST['uoa'] == "1") {
	$uoa = "1";
	setcookie('uoa','0',$coff);
}


################# Avatare ###################
if (!isset($_COOKIE["avt"]))  {
	$avt = "1";
} elseif (isset($_COOKIE["avt"]) && $_COOKIE["avt"] == "0")  {
	$avt = "0";
}
if (isset($_POST['avt']) && $_POST['avt'] == "0") {
	$avt = "0";
	setcookie('avt','0',$con );
} elseif (isset($_POST['avt']) && $_POST['avt'] == "1") {
	$avt = "1";
	setcookie('avt','0',$coff);
}

################# Sounds ###################
if (!isset($_COOKIE["sound"]))  {
	$sound = "1";
} elseif (isset($_COOKIE["sound"]) && $_COOKIE["sound"] == "0")  {
	$sound = "0";
}
if (isset($_POST['sound']) && $_POST['sound'] == "0") {
	$sound = "0";
	setcookie('sound','0',$con );
} elseif (isset($_POST['sound']) && $_POST['sound'] == "1") {
	$sound = "1";
	setcookie('sound','0',$coff);
}

################# Popup ###################
if (!isset($_COOKIE["pop_up"]))  {
	$pops = "aus";
} elseif (isset($_COOKIE["pop_up"]) && $_COOKIE["pop_up"] == "aus")  {
	$pops = "zeigen";
}
if (isset($_POST['pop_up']) && $_POST['pop_up'] == "zeigen") {
	$pops = "zeigen";
	setcookie('pop_up','aus',$con );
} elseif (isset($_POST['pop_up']) && $_POST['pop_up'] == "aus") {
	$pops = "aus";
	setcookie('pop_up','aus',$coff);
}

################# Fettschrift ###################
if (!isset($_COOKIE["nickfarben"]))  {
	$nickfarben = "1";
} elseif (isset($_COOKIE["nickfarben"]) && $_COOKIE["nickfarben"] == "0")  {
	$nickfarben = "0";
}
if (isset($_POST['nickfarben']) && $_POST['nickfarben'] == "0") {
	$nickfarben = "0";
	setcookie('nickfarben','0',$con );
} elseif (isset($_POST['nickfarben']) && $_POST['nickfarben'] == "1") {
	$nickfarben = "1";
	setcookie('nickfarben','0',$coff);
}

################# Fettschrift ###################
if (!isset($_COOKIE["nickfarben2"]))  {
	$nickfarben2 = "1";
} elseif (isset($_COOKIE["nickfarben2"]) && $_COOKIE["nickfarben2"] == "0")  {
	$nickfarben2 = "0";
}
if (isset($_POST['nickfarben2']) && $_POST['nickfarben2'] == "0") {
	$nickfarben2 = "0";
	setcookie('nickfarben2','0',$con );
} elseif (isset($_POST['nickfarben2']) && $_POST['nickfarben2'] == "1") {
	$nickfarben2 = "1";
	setcookie('nickfarben2','0',$coff);
}

################# Online Zeit ###################
if (!isset($_COOKIE["time_online"]))  {
	$time_online = "0";
} elseif (isset($_COOKIE["time_online"]) && $_COOKIE["time_online"] == "1")  {
	$time_online = "1";
}
if (isset($_POST['time_online']) && $_POST['time_online'] == "1") {
	$time_online = "1";
	setcookie('time_online','1',$con );
} elseif (isset($_POST['time_online']) && $_POST['time_online'] == "0") {
	$time_online = "0";
	setcookie('time_online','1',$coff);
}

################# Aktuelle Uhrzeit ###################
if (!isset($_COOKIE["time_real"]))  {
	$time_real = "0";
} elseif (isset($_COOKIE["time_real"]) && $_COOKIE["time_real"] == "1")  {
	$time_real = "1";
}
if (isset($_POST['time_real']) && $_POST['time_real'] == "1") {
	$time_real = "1";
	setcookie('time_real','1',$con );
} elseif (isset($_POST['time_real']) && $_POST['time_real'] == "0") {
	$time_real = "0";
	setcookie('time_real','1',$coff);
}

################# Chat Up Player ###################
if (!isset($_COOKIE["chat_up_player"]))  {
	$chat_up_player = "1";
} elseif (isset($_COOKIE["chat_up_player"]) && $_COOKIE["chat_up_player"] == "0")  {
	$chat_up_player = "0";
} else {
	$chat_up_player = "0";	
}
if (isset($_POST['chat_up_player']) && $_POST['chat_up_player'] == "0") {
	$chat_up_player = "0";
	setcookie('chat_up_player','0',$con );
} elseif (isset($_POST['chat_up_player']) && $_POST['chat_up_player'] == "1") {
	$chat_up_player = "1";
	setcookie('chat_up_player','0',$coff);
}

################# Radio Player ###################
if (!isset($_COOKIE["html5player"]))  {
	$html5player = "1";
} elseif (isset($_COOKIE["html5player"]) && $_COOKIE["html5player"] == "0")  {
	$html5player = "0";
} else {
	$html5player = "0";	
}

if (isset($_POST['html5player']) && $_POST['html5player'] == "0") {
	$html5player = "0";
	setcookie('html5player','0',$con );
} elseif (isset($_POST['html5player']) && $_POST['html5player'] == "1") {
	$html5player = "1";
	setcookie('html5player','0',$coff);
}

//echo $_SERVER['PHP_SELF'];
//$registered = true; //nur zum Validieren entkommentieren, dazu folgendes if else wegkommentieren

if(isset($_SESSION['login']) && $_SESSION['login']==1) {
    $registered = true;
} else {
	    
	$pos = strpos(strtolower($_SERVER['SERVER_SOFTWARE']), "nginx"); // Nginx Server haben mit dem redir evtl. Probleme
	if ($pos !== false) {
		echo 'Zum <a href="login.php">Login</a>'; exit();
	} else {
	    $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	    $extra1 = 'logout.php';

	    if (isset($_SERVER['HTTP_HOST'])) {
		    $host  = $_SERVER['HTTP_HOST'];
			if (strpos($_SERVER['PHP_SELF'],"chat.php") === false)  {	
				header("Location: //$host$uri/$extra1");				
				exit();
			} else {
				if (file_exists("cookiefail.php")) {include ("cookiefail.php");}
				
				// $ua = $_SERVER['HTTP_USER_AGENT'];
				// echo $ua;
				// if (strpos($ua,'Trident/7.0') === false) {
								
					echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=1' /><p style='color:#ff0;background:#00f; border: 2px solid #555;padding: 0 1em 1em 1em';font-size: 13px;><br />Bitte den Chat immer über <a style='color:#fff' href='login.php'>login.php</a> aufrufen.<br />Oder: Die Session wurde unerwartet geschlossen. Bitte ausloggen (Klick auf X ... Logout) und neu einloggen.<br />Oder: Dein Browser ist so restriktiv eingestellt, dass keine Cookies erlaubt werden. Mit dieser Einstellung kannst du den Chat leider nicht verwenden. Vielleicht hilft <a style='color:#fff' href = 'http://www.blogperle.de/wp-content/uploads/2013/12/optionsfenster-fuer-das-cookie-handling-im-bereich-erweitert-in-den-datenschutzeinstellungen-in-den-internetoptionen-des-internet-explorer-11.png'>dieser Tipp</a>.</p>";
					exit;
				// }
			}
		} else {
			echo 'Zum <a href="login.php">Login</a>'; exit();
		}
	}
}


if (phpversion() < '4.3.0') {
	echo "<p>Dieses Script erfordert PHP Version 4.3.0 oder h&ouml;her!<br />";
	echo "auf Deinem Server l&auml;uft aber PHP Version ".phpversion().".</p>";
	exit();
}


require("Sajax.php");
include("chat_inc.php");


$wo = trim(substr($room,6));


if (isset($fix_css_room) && isset($fix_css_css) && count($fix_css_room) == count($fix_css_css)) {
	for($i = 0; $i < count($fix_css_room); $i++) {
		if ($wo == $fix_css_room[$i]) {
			$s = $fix_css_css[$i];
			$skin_fix = true;
		}
	}
}


// ein arrival cookie beim Betreten des Raums setzen:
$woarrival = $wo.'arrival';
setcookie($woarrival, time());

// das Stop cookie beim reload loeschen:
setcookie("stop", "go");


// Saubermachen (max. alle 100 sec)
$filename = "clear.txt";
if (file_exists($filename)) {
      $filealter = time()-filemtime($filename);
      if ($filealter > 100) {
         $fprot = @fopen($filename, "w+");
         @fwrite($fprot, "x");
         @fclose($fprot);

         include_once("cleanup.php");
      }
} else {
      $fprot = fopen($filename, "w+");
      fwrite($fprot, "x");
      fclose($fprot);
}




if (isset($_COOKIE["color"])) {
	$color = $_COOKIE["color"];
} else {
	$color = "";
}


// Letzer Login von registrierten Usern in die user.txt schreiben:
if ( $in_regdb === true) { // Admin nicht
      $angemeldet = "user/user.txt";
      $lines = file($angemeldet);
      $reg_user = array();

      foreach ($lines as $line_num => $line) {
       if(strpos($line,"****") !== false) {
          $reguser = explode("****",$line);
          $regged = trim($reguser[1]);

          if (trim($reguser[0]) != "") {
                if (trim($reguser[0]) == $nickname) {
                if (strpos(@$reguser[2],"@") === false) { $reguser[2] = "mail_not_set";}

                   $last_visit= $reguser[0].'****'.$regged.'****'.$reguser[2].'****'.date("d.m.Y H:i:s")."\n";
                   $reg_user[] .= $last_visit;
                } else {
                   // die User Datenbank neu schreiben: nicht geânderte Zeilen:
                   $reg_user[] .= $line;
                }
          }
       }
     }

     // jetzt user.txt neu schreiben:
     $open5 = fopen($angemeldet, "w");
     flock($open5,LOCK_EX);
     foreach($reg_user as $values) fputs($open5, $values);
     flock($open5,LOCK_UN);
     fclose($open5);
}


$show_dir = 'rooms/';
$log_dir=opendir($show_dir);

$d = 0;
while (false !== ($raeume = readdir($log_dir))) {
      if($raeume != "."
          && $raeume != ".."
          && $raeume != ".htaccess"
          && $raeume != ".htusers"
          && $raeume != ".htpasswd"
          && (strpos($raeume,"_pr") === false || $admintrue !== false)
          && ($raeume != "Offline"  || $admintrue !== false)) {
             $files[] = $raeume;
             $d++;
      }
}
closedir($log_dir);


if ($chat_light == "yes") $anz_rooms = 1;



?>
<!DOCTYPE html>
<html lang="<?php echo $lang;?>">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="description" content="Der Chat von webdesign weisshart" />
<meta name="keywords" content="@bots: please look for keywords in document body ;-)" />
<meta name="author" content="Dipl.-Ing.(FH) Fritz Weisshart und Co. GbR" />
<meta name="generator" content="notepad ;-)" />
<meta name="robots" content="noindex, nofollow" /> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1" /> <!-- iPhone -->

<title><?php echo $titel; ?> - Script von webdesign.weisshart.de </title>


<link rel="shortcut icon" href="img/favicon.ico">
<link rel="apple-touch-icon" sizes="57x57" href="img/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="144x144" href="img/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="60x60" href="img/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="120x120" href="img/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="76x76" href="img/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="152x152" href="img/apple-touch-icon-152x152.png">
<link rel="icon" type="image/png" href="img/favicon-196x196.png" sizes="196x196">
<link rel="icon" type="image/png" href="img/favicon-160x160.png" sizes="160x160">
<link rel="icon" type="image/png" href="img/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16">
<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32">
<!-- <link rel="icon" type="image/gif" href="img/smile.gif"> -->
<meta name="msapplication-TileColor" content="#ffc40d">
<meta name="msapplication-TileImage" content="img/mstile-144x144.png">
<meta name="msapplication-config" content="img/browserconfig.xml">


<?php if (file_exists('flirt.css')) {echo '<link rel="stylesheet" media="screen" href="flirt.css" />';}?>


<style>
#r_player, #player {max-width: 100%;}
#r_player ul, #player ul { border-left: 1px solid #aaa; border-right: 1px solid #aaa; border-bottom: 1px solid #aaa; max-height: 15em; overflow:auto; margin-left:0}
#r_player ul::-webkit-scrollbar, #player ul::-webkit-scrollbar { background:transparent;width:7px}
#r_player ul::-webkit-scrollbar-thumb, #player ul::-webkit-scrollbar-thumb {background:#888;width:6px;border-radius:4px}
#r_player li a, #player li a {text-decoration: none; display:block; padding:3px; font-weight:400;}
#r_player li, #player li {border-top: 1px solid #aaa; width:100%}
#r_player audio, #player audio {margin:6px 0 3px 0; width: 99%; font-size: 0; outline:none;}
#r_player li a:focus, #r_player li a:hover, #player li a:focus, #player li a:hover {background:#ccc;color:#000;outline:0}
.star {color:red;font-size:1.6em;line-height:.1em}
#topic, #topic_wdw {font-weight:bold;font-size:.8em;line-height: 1.2em}
#schrift {font-size:1em;}
#webradio audio {font-size: 0; width:98%; margin: 10px 0 0 2px;  outline: 0; }
#logout {max-width:23em;}
a.stop{display:none;}
input:focus::-webkit-input-placeholder { opacity:.9; }
input:focus::-moz-placeholder { opacity:.9; } /* FF 19+ */
input:focus:-ms-input-placeholder { opacity:.9; } /* IE 10+ */
textarea:focus::-webkit-input-placeholder { opacity:.9; }
textarea:focus::-moz-placeholder { opacity:.9; } /* FF 19+ */
textarea:focus:-ms-input-placeholder { opacity:.9; } /* IE 10+ */
input:hover::-webkit-input-placeholder { opacity:.9; }
input:hover::-moz-placeholder { opacity:.9; } /* FF 19+ */
input:hover:-ms-input-placeholder { opacity:.9; } /* IE 10+ */
textarea:hover::-webkit-input-placeholder { opacity:.9; }
textarea:hover::-moz-placeholder { opacity:.9; } /* FF 19+ */
textarea:hover:-ms-input-placeholder { opacity:.9; } /* IE 10+ */
a.datenschutz{color:#fff !important;}
a:hover.datenschutz, a:focus.datenschutz {color:#fff;background:transparent;text-decoration:none;}
.ip {font-size:.75em;opacity:.5;}
span.ip a {text-decoration: none;}
video {text-indent: 0;max-width:300px;}
#wall .pop {position:relative; cursor:zoom-in;}
#wall .pop::after {
	content: "";
	    background-image: url(img/mag_glass_128.png);
	    background-size: 100% 100%;
	    display: inline-block;
	    height: 1.4em;
	    width: 1.4em;
	    position: absolute;
	    bottom: 6px;
	    right: 6px;
}
iframe:not(#curr_tit) {display:none;}
#einaus_ae:hover {cursor:pointer;}

/* Make it a marquee https://stackoverflow.com/questions/15128716/html-5-marquee-tag-error-in-w3c-validation */
.marquee {
	position:relative;
	display:block;
	width: 99%;
	height:1.4em;
	white-space: nowrap;
	overflow: hidden;
	box-sizing: border-box;
}

.marquee span {
	position:absolute;
	display: inline-block;
	padding-left: 100%;
	text-indent: 0;
	animation: marquee 30s linear infinite; 
}

.marquee span:hover {
	animation-play-state: paused
}

/* Make it move */
@keyframes marquee{
	0%   { transform: translateX(0); }
	100% { transform: translateX(-100%); }
}

#curr_tit {max-width:95%; height:215px; border:0; background:transparent;}

@media (prefers-color-scheme: dark) {
	html {background:#000 !important;}
	body,img, #popout_icon, video{filter:invert(1);}
}



</style>

<?php

// http://aktuell.de.selfhtml.org/artikel/css/mobile-endgeraete/
if(file_exists("check_mobile.php")) {
	require('check_mobile.php');
	if(check_mobile()) {
		$s = 7;
		echo '<style>';
		echo '#menu2 {display:none}';
		echo '</style>';

	} elseif ($s == 7) {
		echo '<style>';
		echo 'body {max-width: 23em;}';
		echo '</style>';
	}
}

// die folgende Zeile ist nur fuer die Demo und kann ohne Folgen geloescht oder auch belassen werden.
// if(file_exists("bklink.php")) include ("bklink.php");

?>
<link rel="stylesheet" type="text/css" media="screen" href="chatcss<?php echo $s; ?>.php" />


<?php
if (isset($_POST['schrift'])) {	
	if ( is_numeric($_POST['schrift']) && $_POST['schrift'] >= 80 && $_POST['schrift'] <= 175 ) { 
		$schrift= intval($_POST['schrift']);
		setcookie("schrift", $schrift, time()+3600*24*360);
	} elseif (isset($_COOKIE["schrift"])) {
		$schrift = $_COOKIE["schrift"];
	} else {
		$schrift = 100;
	}
} elseif (isset($_COOKIE["schrift"])) {
		$schrift = $_COOKIE["schrift"];
} else {
	$schrift = 100;
}

// das Cookie für SR ARIA löschen, das in css12 gesetzt wurde
if ($s !=12) {
	setcookie('bf_linear','1',time()-3600);
	setcookie('reverse','rev',time()-3600);
}


// if ($s != 12 && $s != 17 ) {
if ($s != 12 ) {
	echo ' 
	<style>
	body {font-size:'.$schrift.'% !important;}
	</style> ';
}



if ($s == 12) {
	$ip = "off";
	$time_online = "0";
	$time_real = "0";  
	$dc = "0";
	$uhr = "0";
	$languages = array('de');
	//$smileys = "off"; 
	$display_reverse = true;
	setcookie("reverse", "rev", time() +3600*24);
}

if ($s != 12) {
	echo ' <style>
	html {height:98%;}
	body {height:92%;}
	#talk {height:98%;}
	#wrapper {height:90%;}
	#wall {height:calc(100vh - 20em);-webkit-overflow-scrolling: touch}
	#uo {min-height: 15em;}
	#uo ul{min-height: 7em;}

	</style> ';
}


if ($s == 11) {
	$ip = "off";
	$time_online = "0";
	$time_real = "0";  
	$dc = "0";
	$uhr = "0";
}


if ($s == 7) {
	$time_online = "0";
	$time_real = "0"; 
/*	$sound = "0"; */
}





if ($s == 1)  {$kontrastfarbe = '555'; $jawscol = '555';}
if ($s == 2)  {$kontrastfarbe = '555'; $jawscol = 'f8f8ee';}
if ($s == 3)  {$kontrastfarbe = '555'; $jawscol = 'fff5c7';}
if ($s == 4)  {$kontrastfarbe = 'aaa'; $jawscol = '000';}
if ($s == 5)  {$kontrastfarbe = 'aaa'; $jawscol = '000200';}
if ($s == 6)  {$kontrastfarbe = '555'; $jawscol = 'fff';}
if ($s == 7)  {$kontrastfarbe = '555'; $jawscol = 'fff';}
if ($s == 8)  {$kontrastfarbe = '555'; $jawscol = 'f8f8ee';}
//if ($s == 9)  {$kontrastfarbe = '555'; $jawscol = 'a8a8a8';}
if ($s == 10) {$kontrastfarbe = '555'; $jawscol = 'fff';}
if ($s == 11) {$kontrastfarbe = '008'; $jawscol = '000042';}
if ($s == 12) {$kontrastfarbe = '008'; $jawscol = '000042';}
if ($s == 13) {$kontrastfarbe = '555'; $jawscol = 'fff';}

echo '<style>';
	
	if ($wo == "Info") {echo '#wall p.bg {position: relative;left: 0;width: 100%;}';}
	
	if ($wo == "Flirtchat") { echo '#f, #addsmileys,a.away,#upload, #wall {display:none} /* #user_pro_room{max-height:none} */ ';}
	
	if ($wo == "Flirtchat" && $s==8) {echo '#talk {overflow: auto}';}
	
	
	echo 'textarea{font-family:Verdana, sans-serif;font-size: 1em;}
.button {position:relative;bottom:5px;}
.button, .away {font-size:1em; text-decoration:none;}';

	echo '#ae {display:none;}'; // um FOUC zu vermeiden

//	if (isset($usercolor) && $usercolor=="off") {echo '#menu1 {display:none;}';}
	
	if ($chat_up_player == "0") {echo '#opt3 {display:none;}';}
	if ($html5player == "0") {echo '#opt3 {display:none;}';}

	echo "\n".'audio {margin-top: .7em; min-width: 15em;}';
	
	if ($s == 12) {echo "\n".'audio {margin-bottom: 1.2em;} #opt2 h2:first-of-type{display:none !important;}';}

	if ($s != 12) {echo "\n".'.blink {animation: blink 1.5s steps(2, start) infinite;} @keyframes blink {to { visibility: hidden;}}';}

	if ($s != 7) {
		echo "\n".'#ip {max-width: 15em;text-overflow: ellipsis;overflow: hidden;}';
		echo "\n".'#addsmileys, #f, #einaus_ae {white-space: nowrap;}';
	
	}

	echo "\n".'.ip:before {content:"  IP:\00a0";}';

	echo "\n".'.flag {float:right; margin: 0 5px 0 0;}';
	echo "\n"."#switch, #switch1 {position:absolute;left:-9000px;width:0;height:0;overflow:hidden;display:inline;}";

	echo "\n".'input[type="radio"] {border:none 0;background:transparent;}';
	if(!isset($show_player) || $show_player !== true) {
		echo "\n"."#mp3 {position:absolute;left:-9000px;width:0;height:0;overflow:hidden;display:inline;}";
	}

	if ($pic == "0") 	echo "\n"."#wall img, #addsmileys, #wall .pop {display:none}";
	if ($uoa == "0") 	echo "\n"."#uo ul, .uo_head {display:none;} #uo{border:0;background:transparent;min-height:5em;}";
	if ($avt == "0") 	echo "\n".".av {display:none}";

	// if ($dc == "0") 	echo "\n".".dt, .datum {display:none}";
	// if ($uhr == "0") echo "\n".".uz, .uhrzeit {display:none}";

	$norooms ="";
	if ($show_rooms == "no" && $admintrue !== true && strpos($wo,"_pr") !== false ) {
		echo "\n"."#user_pro_room, .rooms h2 {display:none} .rooms form h2 {display:block}";
		$norooms = "no";
	}

	if ($uhr == "0" && $dc == "0") echo "\n".".tr, .trenner {display:none}";

	if ($dc == "0") echo "\n".".dt, .datum {display:none}";
	if ($uhr == "0") echo "\n".".uz, .uhrzeit {display:none}";
	if ($nickfarben2 == "0") echo "\n"."html,body,div,p,h3,h4,h5,ul,ol,form,span, li {color:#$kontrastfarbe !important}";
	if ($nickfarben == "0") echo "\n"."html,body,div,p,h3,h4,h5,ul,ol,form,span, li {font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;} body {font-weight:bold !important}";
	if (($dc == "0" && $uhr == "0") ||  ($uhr == "0" && $dc == "0") || ($dc == "0" && $uhr == "0")) echo "\n".".tr, .trenner {display:none}";

	if ($chat_light == "yes" || $show_user== "no") echo "\n"."#uo {display:none}";
	
	if ($supportchat == "yes" && $admintrue !== true) echo "\n"."#uo {display:none}";
			
	if ($anz_rooms == 1) echo "\n".".rooms {display:none;} #uo {clear:both; float:left;}";
	if ($anz_rooms != 1 && $s != 7) echo "\n"."#talk {float:left; width:73.9%;} ";
	
	if ($anz_rooms == 0 ) {
		if (strpos($wo,"_pr") === false) {
			echo "\n"."#user_pro_room, .rooms h2 {display:none !important} .rooms form h2 {display:block}";
		} else {
			echo "\n".".rooms h2 {display:none !important} .rooms form h2 {display:block}";
			echo "\n"."#user_pro_room {height: 1em;}";

		}
	}


	if (isset ($more_smileys) && $more_smileys != "on") {
		echo "\n".".more_smileys {display:none !important;}";
	}

	if ((isset ($hide_away_button) && $hide_away_button === true) || $show_user == no) {
		echo "\n".".away {display:none !important;}";
	}

	
	if ($stil == 0) echo "#menu1 {float:left}";

	if(isset($_SERVER["HTTP_USER_AGENT"]) && strpos($_SERVER["HTTP_USER_AGENT"], "KHTML") !== false) {
    	    echo '#file {min-width:300px;background:transparent !important;border:none !important}'; 
	}
	//  echo '#upload:empty {display:none}';       // http://diveintomark.org/archives/2003/11/12/safari

   $anz_zeig = false;
   if ($dc != "0" || $uhr != "0" || $sound != "0" || $nickfarben != "0" || $stil == 0 || $nickcolors != "off") $anz_zeig = true;
   if ($anz_zeig == false) echo "\n".'#einaus_ae {display:none !important}';

	// ARIA:
	echo "\n".'#jaws {position: absolute;left:-9000px; top:auto}';

	if (isset($admin_write_only_room)) {
		if ($wo == $admin_write_only_room && !$admintrue) {echo "\n".'#line, #f, #addsmileys, #Anzeige, .away {display:none; }';}
	}
	
	// fuer den radio.de Player noetig:
	// if ($new_win == "no") echo "\n".'#player_0 {margin-top: -17px;} *+html #player_0 {margin-top: 5px;} ';
	

	// wenn fester Skin konfigueriert, dann Auswahl nicht anzeigen
	if (isset($skin_fix) && $skin_fix === true) {echo' #menu2{display:none} #menu1{ margin: 5px 4px 5px 0}';}

	echo' .ytpreview{max-height:'.$maxheight.'px}';
   
echo "\n"."</style>";


// verhindert das Einbinden des Chat in ein fremdes Frameset (Traffic-Klau)
if (isset($_SERVER['HTTP_REFERER'])) $referrer = $_SERVER['HTTP_REFERER'];
$host = $_SERVER['HTTP_HOST'];

if (isset($referrer) && strpos($referrer,$host) === false && $noframe === true) {
	echo '<script>if (top != self) {top.location = self.location;}</script>';
}
?>


</head>
<body>

<!-- ########## oberhalb dieser Zeile nur aendern, wenn Du weisst, was Du tust! ######## -->
<!-- ######## hier kannst Du eigene Inhalte einfuegen, z.B. eine Kopfzeile, Navigation o.ae. ###### -->
	
<?php if($s=="4" && file_exists("bg_video.php")) { include("bg_video.php");} ?>

<!-- ######## die naechsten Abschnitte nicht aendern! ######## -->

<?php
if (file_exists("header_inc.php")) include("header_inc.php");

if ($titel != "") {
   echo "<h1>$titel</h1>";
}
?>


<?php

// echo $standard;

echo "<div class='helplinks'>";
if ($_SERVER['SERVER_NAME'] == "webdesign.weisshart.de" && $s != 7) {
   if (file_exists("../../wdw/chat.php")) 	echo "<p style =\"float:right; padding: 0 .2em .2em 0;\">|<a style = \"font-weight:bold\" href=\"../chat.php\">"._DOWNLOAD."</a></p>";
   echo "<p style =\"float:right; padding: 0 0 .2em 0;\">|<a style = \"font-weight:bold\" href=\"//forum.weisshart.de\" title =\"Support Forum\">Support&nbsp;Forum</a></p>";
   if (file_exists("../chat-faq.php")) 	echo "<p style =\"float:right; padding: 0 0 .2em 0;\">|<a style = \"font-weight:bold\" href=\"../chat-faq.php\"><abbr  lang=\"en\" xml:lang=\"en\" title=\"frequently asked questions\">FAQ</abbr></a></p>";
}


if($hilfetexte == "on") {
	if (file_exists("logs/reader.php") && $admintrue) 	echo " <p style =\"float:right; padding: 0 0 .2em 0;\">|<a style = \"font-weight:bold\" href=\"logs/reader.php\" title =\"Log Files\">L</a></p>";
	if (file_exists("admin/admin.php") && $admintrue) 	echo " <p style =\"float:right; padding: 0 0 .2em 0;\">|<a style = \"font-weight:bold\" href=\"admin/admin.php\" title =\"Administration\">A</a></p>";

	echo "<p> <span class=\"dot\">|</span><a style = \"float:right; font-weight:bold\" title = \""._HELPTITLE."\" href=\"";
	if ($lang=="en") {
		if ($new_win == "yes") {
			echo "helpEN.php\" target=\"_blank\"><span class=\"dot\">"._LNKNEWWIN.": </span>"._HELP."</a></p>";
		} else {
			echo "helpEN.php\">"._HELP."</a></p>";
		}
		echo "<p class=\"dot\" >|<a href=\"helpEN.php#sr\">"._BLINDHELP."</a></p>";
	} else {
		if ($new_win == "yes") {
			echo "help.php\" target=\"_blank\"><span class=\"dot\">"._LNKNEWWIN.": </span>"._HELP."</a></p>";
		} else {
			echo "help.php\">"._HELP."</a></p>";
		}
		echo "<p class=\"dot\" >|<a href=\"help.php#sr\">"._BLINDHELP."</a></p>";
	}
}

if(file_exists("lang_switcher_inc.php")) {include("lang_switcher_inc.php");}



echo "</div>";

if (file_exists("chtver.php")) {
	include("chtver.php");
	$version = $chtver;
} else {
	$version = "not defined";
}

// die folgenden Zeilen nicht loeschen:
if (file_exists("wdw_inc48.php")) include("wdw_inc48.php");
if (isMobile() === true) {echo'<audio  id="audio-sprite"><source src="sounds/sprite1.mp3" type="audio/mpeg" /></audio>';}

?>
<noscript><p style="color:red; font-weight:bold;"><br /><?php echo _NOJS; ?>.<br /><br /></p></noscript>

<?php
                                                                                                                                                                                                                                                                                                                                                                                                    $NCiR0aW1lX29sZCA9IDA7IC8vIGb8ciBkaWUgRm9ydHNjaHJpdHRzYW56ZWlnZQNCiR0aW1lX29sZCA9IDA7IC8vIGb8ciBkaWUgRm9ydHNjaHJpdHRzYW56ZWlnZQ="                                     										CQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCgkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkvLyBIaW53ZWlzOiBEaWUgRW50ZmVybnVuZyBkZXMgQ3JlZGl0bGlua3Mgb2huZSBnbHRpZ2UgTGl6ZW56IGlzdCBuaWNodCBnZXN0YXR0ZXQsIHVuZCB3aXJkIHN0cmFmLSB1bmQgeml2aWxyZWNodGxpY2ggdmVyZm9sZ3QuDQovLyBCaXR0ZSBiZWFjaHRlbiBTaWUgZGllIHJlYWRtZS50eHQNCg0KZWNobyAnPHNjcmlwdD4nOw0KZWNobyAnLyogPCFbQ0RBVEFbICovJzsNCnNhamF4X3Nob3dfamF2YXNjcmlwdCgpOw0KZWNobyAnLyogXV0+ICovJzsNCmVjaG8gJzwvc2NyaXB0Pic7DQppZiAoaXNzZXQoJF9TRVJWRVJbJ1NFUlZFUl9OQU1FJ10pICYmIGlzc2V0KCRwd2QpKSB7DQoJJHNlcnZlciA9IHN0cl9yZXBsYWNlICgid3d3LiIsIiIsICRfU0VSVkVSWydTRVJWRVJfTkFNRSddICk7DQoJJHNlcnZlciA9IHN0cl9yZXBsYWNlICgiKyIsIiIsICRzZXJ2ZXIpOw0KCSRzZXJ2ZXIgPSBzdHJfcmVwbGFjZSAoIi0iLCIiLCAkc2VydmVyKTsNCgkkZG9tID0gY3JjMzIoJHNlcnZlcik7DQogICAgICAkY2hhdmUgPSAkcHdkIC8gc3ByaW50ZigiJXUiLCAkZG9tKTsNCn0NCmlmIChpc3NldCgkdmVyc2lvbikpIHsNCgkkdmVycyA9ICI8c3BhbiBzdHlsZT1cImRpc3BsYXk6bm9uZVwiPjxhYmJyIHRpdGxlPVwiVmVyc2lvblwiPnZlcnMuPC9hYmJyPiAkdmVyc2lvbjwvc3Bhbj4iOw0KfSBlbHNlIHsNCgkkdmVycyA9IiI7DQp9DQoKaWYgKCFpc3NldCgkY3JlZGl0YmcpKSB7JGNyZWRpdGJnID0gJyNGRkZGRkYnO30KaWYgKHByZWdfbWF0Y2goJy9eI1swLTlBLUZdezEsNn0kL2knLCAkY3JlZGl0YmcpICYmIHN0cmxlbigkY3JlZGl0YmcpID09IDcpIHsKICAgICAgJHJlZEggPSBiYXNlX2NvbnZlcnQoc3Vic3RyKCRjcmVkaXRiZywgMSwgMiksIDE2LCAxMCk7CiAgICAgICRncmVlbkggPSBiYXNlX2NvbnZlcnQoc3Vic3RyKCRjcmVkaXRiZywgMywgMiksICAxNiwgMTApOwogICAgICAkYmx1ZUggPSBiYXNlX2NvbnZlcnQoc3Vic3RyKCRjcmVkaXRiZywgNSwgMiksICAxNiwgMTApOwogICAgICAkc3VtID0gJHJlZEgqMjk5ICsgJGdyZWVuSCo1ODcgKyAkYmx1ZUgqMTE0OwogICAgICBpZiAoJHN1bSA8IDEyNzAwMCApIHsKICAgICAgICAgICAkY29tcCA9ICIjRkZGRkZGIjsKICAgICAgfSBlbHNlIHsKICAgICAgICAgICAkY29tcCA9IiMwMDAwMDAiOwogICAgICB9Cn0gZWxzZSB7CiAgICAgICRjcmVkaXRiZyA9ICIjRkZGRkZGIjsKICAgICAgJGNvbXAgPSAiIzAwMDAwMCI7Cn0NCg0KaWYgKCFpc3NldCgkY2hhdmUpIHx8ICRjaGF2ZSA8PiAiMTI0Iikgew0KCWVjaG8gJw0KICAgICAgPHAgc3R5bGU9ImZvbnQtc2l6ZToxMXB4OyBjb2xvcjogJy4kY29tcC4nICFpbXBvcnRhbnQ7IGJhY2tncm91bmQ6ICcuJGNyZWRpdGJnLicgIWltcG9ydGFudDsgcGFkZGluZy1ib3R0b206IDNweDsiPg0KICAgICAgc2NyaXB0ICZjb3B5OyA8YSBzdHlsZT0iZm9udC1zaXplOjExcHg7IGNvbG9yOiAnLiRjb21wLicgIWltcG9ydGFudDsgYmFja2dyb3VuZDogJy4kY3JlZGl0YmcuJyAhaW1wb3J0YW50OyB0ZXh0LWRlY29yYXRpb246IHVuZGVybGluZTsiDQogICAgICBocmVmPSIvL3dlYmRlc2lnbi53ZWlzc2hhcnQuZGUvY2hhdC5waHAiPndlYmRlc2lnbiB3ZWlzc2hhcnQ8L2E+DQogICAgICBrb3N0ZW5sb3MgZiZ1dW1sO3IgbmljaHQga29tbWVyemllbGxlIFNlaXRlbi4gICcuJHZlcnMuJw0KICAgICAgJzsNCg0KICAgICAgaWYgKCFpc3NldCgkc3BvbnNvcmVkKSB8fCAkc3BvbnNvcmVkICE9IDc4OTApew0KICAgICAgICAgICBlY2hvICcNCiAgICAgICAgICAgPGEgaHJlZj0iLy93ZWJkZXNpZ24ud2Vpc3NoYXJ0LmRlL2NoYXQtbGl6ZW56LnBocCI+PGltZyBzdHlsZT0iZGlzcGxheTppbmxpbmUgIWltcG9ydGFudDsiIHNyYz0iaHR0cHM6Ly93ZWJkZXNpZ24ud2Vpc3NoYXJ0LmRlL2NoYXQvaW1nL2xpemVuel9rYXVmZW4ucG5nIiBhbHQ9InwgTGl6ZW56IGthdWZlbiIgd2lkdGg9IjEwNyIgaGVpZ2h0PSIyMSIgLz48L2E+DQogICAgICAgICAgJzsNCiAgICAgIH0NCiAgICAgIGVjaG8gJzwvcD4nOw0KfSBlbHNlIHsNCgllY2hvICc8cCBzdHlsZT0iZm9udC1zaXplOjExcHgiPicuJHZlcnMuJzwvcD4nOw0KfQ0KDQplY2hvICI8ZmllbGRzZXQgaWQ9J3dyYXBwZXIiOwoJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJ";eval(base64_decode($NCiR0aW1lX29sZCA9IDA7IC8vIGb8ciBkaWUgRm9ydHNjaHJpdHRzYW56ZWlnZQNCiR0aW1lX29sZCA9IDA7IC8vIGb8ciBkaWUgRm9ydHNjaHJpdHRzYW56ZWlnZQ));?>'><?php
// die folgende Zeile ist nur fuer die Demo, und kann ohne Folgen geloescht werden:
if (file_exists("nodl_header_inc.php")) include("nodl_header_inc.php");

// Topic anzeigen:
echo '<div id="topic">';
if (!isset($wo)) $wo = "";
$topicfile = 'topics/'.$wo.'_topic.txt';
if (file_exists($topicfile) && filesize($topicfile) > 2) {
   readfile ($topicfile);   
}
echo '</div>';
?>

<div id ="talk" style="clear:right">

<?php 
// Chat zeitabhaengig offline:
$tmp_closed = false;
if (isset($ab_offen) && isset($bis_offen)) {
	$uhrzeit = date("H:i");
	if ($ab_offen < $bis_offen) {
		if ($uhrzeit < $ab_offen || $uhrzeit >= $bis_offen) {
	 	   $tmp_closed = true;
		}
	} else {    
	    if ($uhrzeit < $ab_offen && $uhrzeit >= $bis_offen) {
			$tmp_closed = true;
		}
	}
}

if ($wo == "Flirtchat" && file_exists("profile_gallery.php") && file_exists('fchat_inc.php') && !file_exists("rooms/Offline")  && $tmp_closed !== true) {
	include('fchat_inc.php');
}
elseif (!isset($display_reverse) || $display_reverse !== true) {
	echo'
	<h2 class="dot">Die Nachrichten</h2>
	<div id="wall" style="clear:both">';
	if (file_exists("img/throbber4.gif")) {
		echo'<img id="throbber" src="img/throbber4.gif" height="100" width="100" style="position:relative;top:100px;left:calc(50% - 50px)" alt="">';
	}
	echo '</div>';
}
?>

<form  accept-charset="utf-8" id="f" name="f"  onsubmit="add(); return false;" >

<p>
<label class="dot" for="line"> <?php echo _MESSAGE; ?>: </label>

<?php
############ mehrzeilig ###############
if ((isset($m) && $m == "0") || $s == 12 || $s == 7 ) {
	echo'
		<input type="text" name="line" id="line"  size="67" maxlength='.$max_length.'
		title="'._MESSAGE.'" placeholder="'._MESSAGE.'" autofocus/>
	';
} else {
	echo'
		<textarea  name="line" id="line"   maxlength='.$max_length.'
		title="'._MESSAGE.'" placeholder="'._MESSAGE.'" autofocus ></textarea>
	';
}
?>

<button class="button" type="submit" onclick="closepopup()" ><?php echo _SUBMIT; ?></button>


<?php
echo '<input type="hidden" name="handle" id="handle" value="'.$nickname.'" />';
echo '<br><span class="nick">'._REGUSER.'</span>';
?>

<span id="nickname">

<?php 
if ($modtrue !== false) $nickname = $nickname.'&nbsp;[M]';
if ($admintrue !== false) $nickname = $nickname.'&nbsp;[A]';
echo $nickname; 
?>
</span>
</form>



<!-- ARIA -->
<div id="jaws"></div> 

<p style=" float:left; margin: 5px 5px 0 0;" ><a href="#" style="cursor:pointer;" title="Aktualisierung pausieren" class="away" onclick='away(); return false;'>Away</a></p>
<div id ="addsmileys">

<?php if ($smileys != "on") echo "<!--"; ?>
<h2 class="dot"><?php echo _SMIL; ?>:</h2>

<ul>

	<li><a id="a1" href="#a1" onclick="ads(':-)'); return false; "><img src="img/smile.gif" title=":)" alt="lacht" width="15" height="15" /></a></li>
	<li><a id="a2" href="#a2" onclick="ads(';-)'); return false; "><img src="img/smile_zwinker.gif" title=";-)" alt="zwinkert" width="15" height="15" /></a></li>
	<li><a id="a3" href="#a3" onclick="ads(':-('); return false; "><img src="img/sad.gif" title=":-(" alt="traurig" width="15" height="15" /></a></li>
	<li><a id="a4" href="#a4" onclick="ads(':evil'); return false; "><img src="img/evil.gif" title=":evil" alt="b&ouml;se" width="15" height="15" /></a></li>
	<li><a id="a7" href="#a7" onclick="ads(':p'); return false; "><img src="img/zunge.gif" title=":p" alt="verschmitzt" width="15" height="15" /></a></li>
	<li><a id="a8" href="#a8" onclick="ads('8-)'); return false; "><img src="img/cool.gif" title="8)" alt="cool" width="15" height="15" /></a></li>
	<li><a id="a9" href="#a9" onclick="ads(':oops'); return false; "><img src="img/redface_stat.gif" title=":oops" alt="verlegen" width="15" height="15" /></a></li>
	<li><a id="a10" href="#a10" onclick="ads(':=)'); return false;"><img src="img/mrgreen_stat.gif" title=":=)" alt="bleckt die Z&auml;hne" width="15" height="15" /></a></li>
	<li><a id="a11" href="#a11" onclick="ads(':cry'); return false;"><img src="img/crying2_stat.gif" title=":cry" alt="heult" width="15" height="15" /></a></li>
	<li><a id="a12" href="#a12" onclick="ads(':lol'); return false;"><img src="img/lol_stat.gif" title=":lol" alt="lacht&nbsp;schallend" width="15" height="15" /></a></li>
	<li><a id="a13" href="#a13" onclick="ads(':rofl'); return false;"><img src="img/kopf.gif" title=":rofl" alt="steht&nbsp;Kopf" width="15" height="15" /></a></li>
	<li><a id="a14" href="#a14" onclick="ads(':tel'); return false;"><img src="img/tel_stat.gif" title=":tel" alt="telefoniert" width="45" height="19" /></a></li>
	<li><a id="a15" href="#a15" onclick="ads(':gruebel'); return false;"><img src="img/gruebel_stat.gif" title=":gruebel" alt="gr&uuml;belt" width="19" height="17" /></a></li>
	<li><a id="a16" href="#a16" onclick="ads(':wink'); return false;"><img src="img/wink_stat.gif" title=":wink" alt="winkt" width="25" height="15" /></a></li> 



	<li class="more_smileys"><a style="text-decoration:none;" href="usr_smileys.php" onclick="FensterOeffnen(this.href); return false"><img  src="img/more.gif" title="more smileys (popup)" alt="more smileys" width="50" height="15" /><span>&nbsp;[more&nbsp;smileys]</span><span class="dot"> popup!</span></a></li>

</ul>

<?php if ($smileys != "on") echo "-->"; ?>

</div>



<span id="mp3"></span>


<?php 
if (isset($display_reverse) && $display_reverse === true) {

	echo'
	<h2 class="dot">Die Nachrichten</h2>
	<div id="wall" tabindex="0" style="clear:both">';
	if (file_exists("img/throbber4.gif")) {
		echo'<img src="img/throbber4.gif" height="100" width="100" style="position:relative;top:20px;left:calc(50% - 50px)" alt="">';
	}
	echo '</div>';

}
?>


<script>

  function end_upload2(data){
			document.getElementById('upload_2').innerHTML =  data;
  }
  function clear_upload2(){
			document.getElementById('upload_2').innerHTML =  '';
  }
    
</script>


<?php
$img = true;
if (strpos($wo,"_pr") !== false && $imginclude_pr != "yes") $img = false;


if ($mp3allow == "alle" || ($mp3allow=="clean" && (strpos($wo,"_pr") !== false || $blankrooms == "yes" || in_array($wo,$blankroom) || $admintrue !== false)) || ($mp3allow=="admins" && $admintrue !== false)) {
	$mp3upallow=1;
} else {
	$mp3upallow=0;
}; // fuer die Uebergabe an micoxUpload2 ist diese Umwandlung erforderlich


if (@$vidallow == "alle" || (@$vidallow=="clean" && (strpos($wo,"_pr") !== false || $blankrooms == "yes" || in_array($wo,$blankroom) || $admintrue !== false)) || (@$vidallow=="admins" && $admintrue !== false)) {
	$vidallow=1;
} else {
	$vidallow=0;
}; // fuer die Uebergabe an micoxUpload2 ist diese Umwandlung erforderlich



if (phpversion() > '7.0.0' && $gd === true) {
if (($img == true && isset($imginclude) && $imginclude=="yes" && $wo != "Info" && $wo != "Buglist"   && !file_exists("rooms/Offline")) || $admintrue !== false) {
echo '
<fieldset style="float:left;" id="upload">
<legend style="font-size: .8em;">&nbsp;'._UPLOADLEGEND.'&nbsp;</legend>
  <form action="upa.php?rm='.$wo.'" >
    <div>
       <label class="dot" for="file">'._UPLOADLABEL.':&nbsp;</label> 
       <input type="file" style="color:transparent;" id="file" name="file" onchange="micoxUpload2(this.form,0,\'Loading \',end_upload2,'.$maxsize.','.$mp3upallow.','.$vidallow.')"/>    </div>
    <div id="upload_2"></div>
  </form>
';

echo '</fieldset>';
}
}

if ($chat_light == "yes") {
	echo '<p id="logout"  style="clear:left"><a style="font-weight:bold; text-decoration:none;" title="Logout" href="logout.php" >X ... Logout!&nbsp;</a></p>
';
}

?>



</div>


<fieldset class="rooms">
<legend style="position: absolute;left:-9000px;">Anwesende User, Räume, und Anzeigeoptionen </legend>
<!-- die folgende Zeile ist nur fuer die Demo, und kann ohne Folgen geloescht oder auch belassen werden: -->
<!-- Wer will, kann hier ein eigenes Logo einbinden -->
<?php if(file_exists("wdw_inc.php")) include ("wdw_inc.php"); ?>




<?php if (isset($ip) && $ip == "off" ) echo "<!--"; ?>
    <p id="ip"><?php echo _YOUR_IP.$_SERVER['REMOTE_ADDR'] ?></p>   
	
<?php if (isset($ip) && $ip == "off" ) echo "-->"; ?>

<form method="post"  onsubmit="return room_name()">
<h2 class ="dot"><?php echo _WHERE; ?></h2>
<?php

$file3 = "rooms/$wo";



if (strpos($wo,"_pr") !== false) {
	echo "<p>"._PR." <span style = \"font-weight:bold; color:#f00\">$wo</span></p>";
	if ($anz_rooms != 1) echo '<div id="uo"> </div>';
} elseif (!$admintrue && !file_exists($file3)) {
	echo "<p>"._NOROOM."</p>";
} else {
	if ($d > 1) echo "<p>"._YOUROOM." <span style = \"font-weight:bold; color:#f00\">$wo</span></p>";
	if ($anz_rooms != 1) echo '<div id="uo"> </div>';
}

?>


</form>

<?php
if ($s != 12) {
	if ($d > 1 || strpos($room,"_pr") !== false || !file_exists($room)) {
		if ($admintrue !== false) {
			echo "<h2 style=\"margin-top: 8px;\">"._ALLROOMS."</h2>";
		} else {
			echo "<h2 style=\"margin-top: 8px;\">"._PUBLROOMS."</h2>";
		}
	}
}
?>
<div id="user_pro_room" role="navigation"> </div>


<h2  style="margin:1em 0 .5em 0; display:block !important;" ><span style="position: absolute;left:-9000px;">Optionen </span><a aria-hidden = "true"  tabindex="0" title="<?php echo _OPT_TITLE; ?>" style="text-decoration:none; background-color:transparent;" onclick="klapp('ae'); return false;" onkeypress="klapp('ae'); return false;"><span id="einaus_ae" ><?php _OPT ?></span></a></h2>



<div id="ae">


<form id ="opt1" method="post">

<?php
################# Schriftgröße ####################
// if ($s != 7 && $s != 12) {
if ($s != 12) {
	echo '<div class="opt"><p><label for="schrift"> '._FONTSIZE.' (80…175): </label><input type="number" min="80" max="175" name="schrift" id="schrift" style="width:3em" onchange=" CookieSetz(\'schrift\',this.form.schrift.value,360);this.form.submit();" value="'.$schrift.'" /></p></div>';
}

################## reverse ########################
if (isset($display_reverse_user) && $display_reverse_user === true && $wo != "Flirtchat") {		
	if ($rev == "1") {
		$chk2="checked";
		echo '<input type="hidden" name="rev" value="0">';
	} else {
		$chk2="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk2.' id="reverse" name="rev" value="1" onchange="this.form.submit();"/><label for="reverse" title="'._TIT_REVERSE.'" > '._REVERSE.'</label></p></div>';	
}

############### mehrzeilig ########################
if ($s != 7 && $s != 11) {
	if ($m == "1") {
		$chk3="checked";
		echo '<input type="hidden" name="m" value="0">';
	} else {
		$chk3="";
	}
	echo '<div class="opt" id="zeilen"><p><input type="checkbox" '.$chk3.' id="mehrzeilig" name="m" value="1" onchange="this.form.submit();"/><label for="mehrzeilig" title="'._TIT_TEXTAREA.'" > '._TEXTAREA.'</label></p></div>';	
}
############### Datum und Uhrzeit im Chatfenster ########################
if ($s != 11 && $s != 13 ) {
	if ($dc == "0") {
		$chk4="checked";
		echo '<input type="hidden" name="dc" value="1">';
	} else {
		$chk4="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk4.' id="datum" name="dc" value="0" onchange="this.form.submit();"/><label for="datum" title="'._TIT_DATEOFF.'" > '._DATEOFF.'</label></p></div>';	

	if ($uhr == "0") {
		$chk5="checked";
		echo '<input type="hidden" name="uhr" value="1">';
	} else {
		$chk5="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk5.' id="uhr" name="uhr" value="0" onchange="this.form.submit();"/><label for="uhr" title="'._TIT_TIMEOFF.'" > '._TIMEOFF.'</label></p></div>';	
}

############### Bilder ########################
if ($pic == "0") {
	$chk6="checked";
	echo '<input type="hidden" name="pic" value="1">';
} else {
	$chk6="";
}
echo '<div class="opt"><p><input type="checkbox" '.$chk6.' id="pic" name="pic" value="0" onchange="this.form.submit();"/><label for="pic" title="'._TIT_HIDE_PICS.'" > '._HIDE_PICS.'</label></p></div>';	

// ############### User-online-Anzeige ########################
if ($uoa == "0") {
	$chk16="checked";
	echo '<input type="hidden" name="uoa" value="1">';
} else {
	$chk16="";
}
echo '<div class="opt"><p><input type="checkbox" '.$chk16.' id="uoa" name="uoa" value="0" onchange="this.form.submit();"/><label for="uoa" title="'._TIT_HIDE_UO.'" > '._HIDE_UO.'</label></p></div>';


############### Avatare ########################
if ($pic == "1" && $s != 7 && $s != 12) {
	if ($avt == "0") {
		$chk7="checked";
		echo '<input type="hidden" name="avt" value="1">';
	} else {
		$chk7="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk7.' id="avt" name="avt" value="0" onchange="this.form.submit();"/><label for="avt" title="'._TIT_HIDE_AVATAR.'" > '._HIDE_AVATAR.'</label></p></div>';	
}

############### Sound ########################
	if ($sound == "0") {
		$chk8="checked";
		echo '<input type="hidden" name="sound" value="1">';
	} else {
		$chk8="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk8.' id="sound" name="sound" value="0" onchange="this.form.submit();"/><label for="sound" title="'._TIT_SOUNDOFF.'" > '._SOUNDOFF.'</label></p></div>';	
############### Popup ########################
if ($s != 7 && $s != 11 && isMobile() !== true) {
	if (isset($popup) && $popup == "enable") {
		if ($pops == "zeigen") {
			$chk9="checked";
			echo '<input type="hidden" name="pop_up" value="aus">';
		} else {
			$chk9="";
		}
		echo '<div id="popup" class="opt"><p><input type="checkbox" '.$chk9.' id="pop_up" name="pop_up" value="zeigen" onchange="this.form.submit();"/><label for="pop_up" title="'._TIT_SHOW_POPUP.'" > '._SHOW_POPUP.'</label></p></div>';	
	}
}

############### Fettschrift ########################
if ($s != 6) {
	if ($nickfarben == "0") {
		$chk10="checked";
		echo '<input type="hidden" name="nickfarben" value="1">';
	} else {
		$chk10="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk10.' id="nickfarben" name="nickfarben" value="0" onchange="this.form.submit();"/><label for="nickfarben" title="'._TIT_COLOROFF.'" > '._COLOROFF.'</label></p></div>';	
}

############### Schriftfarben ausschalten ########################
if (!isset($usercolor) || $usercolor=="auto") {
	if ($s != 11 && $s != 9) {
		if ($nickfarben2 == "0") {
			$chk11="checked";
			echo '<input type="hidden" name="nickfarben2" value="1">';
		} else {
			$chk11="";
		}
		echo '<div class="opt"><p><input type="checkbox" '.$chk11.' id="nickfarben2" name="nickfarben2" value="0" onchange="this.form.submit();"/><label for="nickfarben2" title="'._TIT_COLOR2OFF.'" > '._COLOR2OFF.'</label></p></div>';	
	}
}

############### Online und Uhrzeit ausschalten ########################
if ($s != 7 && $s != 11) {
	if ($time_online == "0") {
		$chk12="checked";
		echo '<input type="hidden" name="time_online" value="1">';
	} else {
		$chk12="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk12.' id="time_online" name="time_online" value="0" onchange="this.form.submit();"/><label for="time_online" title="'._TIT_ONLINEOFF.'" > '._ONLINEOFF.'</label></p></div>';	
	
	if ($time_real == "0") {
		$chk13="checked";
		echo '<input type="hidden" name="time_real" value="1">';
	} else {
		$chk13="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk13.' id="time_real" name="time_real" value="0" onchange="this.form.submit();"/><label for="time_real" title="'._TIT_HOUROFF.'" > '._HOUROFF.'</label></p></div>';	
}

############### Chat Up Player ########################
if (($admintrue || $modtrue || (isset($chat_up_player_all) && $chat_up_player_all === true)) && file_exists("chat_up_player_inc.php") && $s != 7 && $s !=12 && !isset($no_chat_up_player) ) {
	if ($chat_up_player == "0") {
		$chk14="checked";
		echo '<input type="hidden" name="chat_up_player" value="1">';
	} else {
		$chk14="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk14.' id="chat_up_player" name="chat_up_player" value="0" onchange="this.form.submit();"/><label for="chat_up_player" title="'._TIT_SHOW_UP_PLAYER.'" > '._SHOW_UP_PLAYER.'</label></p></div>';	
}

############### HTML5 Player ########################
if (($admintrue || $modtrue || (isset($chat_up_player_all) && $chat_up_player_all === true)) && file_exists("html5player_inc.php") && $s !=12 && !isset($no_chat_up_player)) {
	if ($html5player == "0") {
		$chk15="checked";
		echo '<input type="hidden" name="html5player" value="1">';
	} else {
		$chk15="";
	}
	echo '<div class="opt"><p><input type="checkbox" '.$chk15.' id="html5player" name="html5player" value="0" onchange="this.form.submit();"/><label for="html5player" title="'._TIT_SHOW_HTML5_PLAYER.'" > '._SHOW_HTML5_PLAYER.'</label></p></div>';	
}

?>

<!-- um bei Optionswechsel im Raum zu bleiben: -->
<p><input type="hidden" name="room" value ="<?php echo $wo ?>" /></p>


</form>


<div id ="opt2">

<h3 class="dot"><?php echo _NICKCOLOR; ?>:</h3>
<form id="form">
<p>
<label class="dot" for="menu1"><?php echo _NICKCOLORLABEL; ?>:</label>

	<select name="menu1" id="menu1" onchange="jump(this.form);klapp('ae');" >
	<option value="" selected="selected"><?php echo _NICKCOLOR; ?></option>
	<option style="background:#2222cc; color:#FFFFFF" value="/color #2222cc"><?php if(strtolower($color) == "2222cc") echo "* "; ?><?php echo _COL1; ?></option>
	<option style="background:#cc2222; color:#FFFFFF" value="/color #cc2222"><?php if(strtolower($color) == "cc2222") echo "* "; ?><?php echo _COL2; ?></option>
	<option style="background:#cc22cc; color:#FFFFFF" value="/color #cc22cc"><?php if(strtolower($color) == "cc22cc") echo "* "; ?><?php echo _COL3; ?></option>
	<option style="background:#57b431; color:#FFFFFF" value="/color #57b431"><?php if(strtolower($color) == "57b431") echo "* "; ?><?php echo _COL9; ?></option>
	<option style="background:#ccc622; color:#000000" value="/color #ccc622"><?php if(strtolower($color) == "ccc622") echo "* "; ?><?php echo _COL4; ?></option>
	<option style="background:#22c9cb; color:#000000" value="/color #22c9cb"><?php if(strtolower($color) == "22c9cb") echo "* "; ?><?php echo _COL6; ?></option>
	<option style="background:#cc8c22; color:#FFFFFF" value="/color #cc8c22"><?php if(strtolower($color) == "cc8c22") echo "* "; ?><?php echo _COL7; ?></option>
	<option style="background:#222222; color:#ffffff" value="/color #222222"><?php if(strtolower($color) == "222222") echo "* "; ?><?php echo _COL8; ?></option>
	</select>
</p>
</form>


<?php
if (!isset($stil) || $stil == 0) {
$star2 = $star3 = $star4 = $star5 = $star6 = $star7 = $star8 = $star10 = $star11= $star12 =$star13= "";
if ($s == 2) $star2 = "* ";
if ($s == 3) $star3 = "* ";
if ($s == 4) $star4 = "* ";
if ($s == 5) $star5 = "* ";
if ($s == 6) $star6 = "* ";
if ($s == 7) $star7 = "* ";
if ($s == 8) $star8 = "* ";
if ($s == 10) $star10 = "* ";
if ($s == 11) $star11 = "* ";
if ($s == 12) $star12 = "* ";
if ($s == 13) $star13 = "* ";

echo '
<h3 class="dot">'._SKIN.'</h3>
<form id="form2" method="post">
<p>
<label class="dot" for="menu2">'._SKINLABEL.':</label>
<select name="menu2" id="menu2" onchange="jump2(this.form);" >
<option value="" selected="selected" >'._SKINS.'</option>
';

// if (file_exists("chatcss2.php")) {echo '<option lang="en" xml:lang="en" style="background:#FAFAEE; color:#000000" value="2">'.$star2.'Boxes</option>';}
if (file_exists("chatcss8.php")) {echo '<option lang="en" xml:lang="en" style="background:#FFFFFF; color:#000000" value="8">'.$star8.'Firebox</option>';}
if (file_exists("chatcss10.php")) {echo '<option lang="en" xml:lang="en" style="background:#E2E2FF; color:#000000" value="10">'.$star10.'Sky</option>';}
if (file_exists("chatcss13.php")) {echo '<option lang="en" xml:lang="en" style="background:#DDDDDD; color:#000000" value="13">'.$star13.'Messenger</option>';}
if (file_exists("chatcss3.php")) {echo '<option lang="en" xml:lang="en" style="background:#FFFBCC; color:#000000" value="3">'.$star3.'Web 2</option>';}
if (file_exists("chatcss4.php")) {echo '<option lang="en" xml:lang="en" style="background:#000000; color:#FFFFFF" value="4">'.$star4.'Black</option>';}
if (file_exists("chatcss11.php")) {echo '<option style="background:#000088; color:#ffff00; font-weight:bold" value="11">'.$star11.'Invers</option>';}
if (file_exists("chatcss5.php")) {echo '<option lang="en" xml:lang="en" style="background:#446644; color:#FFFFFF" value="5">'.$star5.'Stage</option>';}
if (file_exists("chatcss6.php")) {echo '<option lang="en" xml:lang="en" style="background:#fff1fa; color:#cc0000" value="6">'.$star6.'Flower</option>';}
if (file_exists("chatcss12.php")) {echo '<option style="background:#000088; color:#ffff00; font-weight:bold" value="12">'.$star12.'BF-Linear</option>';}
if (file_exists("chatcss7.php")) {echo '<option style="background:#ffffff; color:#000000" value="7">'.$star7.'Mobil</option>';}
	
echo'	
</select>
</p>
</form>
';
}

if ($s == 12) {	
		
	$font_size ="1";
	if (isset($_COOKIE["size"]) && $_COOKIE["size"] == "200") {
		$font_size = "1.7";
	} elseif (isset($_COOKIE["size"]) && $_COOKIE["size"] == "300") {
		$font_size = "2.5";
	} elseif (isset($_COOKIE["size"]) && $_COOKIE["size"] == "100") {
		$font_size = "1";
	}
		
	echo '<div class="sizer" aria-hidden="true">
		<h3>Schriftgr&ouml;&szlig;e: </h3>';
		if ($font_size==1) {
			echo '<span style="font-size: 90%" title= "Schriftgr&ouml;&szlig;e 100%">A</span>';
		} else {
			echo'<a href="" style="font-size: 90%" title= "Schriftgr&ouml;&szlig;e 100%"  onclick="CookieSetz(\'size\',\'100\',360); window.location.reload();" >A</a>';
		}

		if ($font_size==1.7) {
			echo '<span style="font-size: 120%" title= "Schriftgr&ouml;&szlig;e 200%">A</span>';
		} else {
			echo'<a href="" style="font-size: 120%" title= "Schriftgr&ouml;&szlig;e 200%" onclick="CookieSetz(\'size\',\'200\',360); window.location.reload();" >A</a>';
		}
		
		if ($font_size==2.5) {
			echo '<span style="font-size: 150%" title= "Schriftgr&ouml;&szlig;e 300%">A</span>';
		} else {
			echo'<a href="" style="font-size: 150%" title= "Schriftgr&ouml;&szlig;e 300%" onclick="CookieSetz(\'size\',\'300\',360); window.location.reload();" >A</a>';
		}
				
	echo '</div>';
}	
?>		
		
		

</div>


</div>

<?php if (isset($time_online) && $time_online == "0" ) echo "<!--"; ?>
      <p class="uhr" style="clear:left"><?php echo _ONTIME; ?>: <span id="Anzeige2" >00:00:00</span></p>
<?php if (isset($time_online) && $time_online == "0" ) echo "-->"; ?>

<?php if (isset($time_real) && $time_real == "0" ) echo "<!--"; ?>
      <p class="uhr" style="clear:left"><?php echo _REALTIME; ?> : <span id="Uhr_Anzeige2" >00:00:00</span></p>
<?php if (isset($time_real) && $time_real == "0" ) echo "-->"; ?>

<?php
if (isset($create_profile) && $create_profile == "yes" && phpversion() > '5.0.0' && file_exists("profil.php") && !file_exists("rooms/Offline")  && $tmp_closed !== true && $s != 12) {
	echo' <p class="logout" style="margin-top: .5em;  clear:left;"><a style="font-weight:bold;" href="profil.php">'._PROFIL.'</a></p> ';
}
?>


<p id="logout"  style="clear:left"><a style="font-weight:bold; text-decoration:none;" title="Logout" href="logout.php" >X ... Logout!&nbsp;</a></p>


<!-- die Sounds -->
<?php if ($sound != "0") {echo' <div id="ton"></div>';} ?>


<script src="chat_js.php"></script>

<?php if (isMobile() !== true) {echo' <script src="popbild.js?'.time().'" defer></script> ';} ?>

<script src="micoxUpload2.js" defer></script>

<!-- Player Uploads -->
<?php if (($admintrue || (isset($chat_up_player_all) && $chat_up_player_all === true)) && file_exists("chat_up_player_inc.php") && $s != 7 && $s !=12 && $chat_up_player == "0") { 
	include ('chat_up_player_inc.php');
}
?>

<!-- Player HTML5 Radio -->
<?php if (file_exists("html5player_inc.php")  && $s !=12 && $html5player == "0") { 
	include ('html5player_inc.php');
} ?>

<!-- Webradio-Player -->
<?php
if ($_SERVER['SERVER_NAME'] == "webdesign.weisshart.de") {
	if (file_exists("webradio_inc.php")  && $s != 12 && $wo == $standard) { 
		include ('webradio_inc.php');
	}	
} else {
	if (file_exists("webradio_inc.php")  && $s != 12) { 
		include ('webradio_inc.php');
	}
} 
?>


</fieldset>


<?php 
if ($anz_rooms == 1) echo '<div id="uo"> </div>';
?>


</fieldset>


<?php if (file_exists("footer_inc.php")) include("footer_inc.php"); ?>


</body>
</html>
