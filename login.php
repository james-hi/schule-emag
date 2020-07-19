<?php
$path = '../wartung/sperren.php';
if (file_exists($path)) {require $path; }

if (file_exists("redir_unwanted.php")) {
	include "redir_unwanted.php";
}	

include_once("cleanup.php");

$ua = $_SERVER['HTTP_USER_AGENT'];
// echo $ua;
if (strpos($ua,'Trident/7.0') !== false || strpos($ua,'MSIE') !== false ) {
				
	echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=1' /><p style='color:#ff0;background:#00f; border: 2px solid #555;padding: 0 1em 1em 1em';font-size: 13px;><br />Sie verwenden einen veralteten und unsicheren Browser (Windows Internet Explorer). Die Nutzung des Chat mit diesem Browser stellt ein Sicherheitsrisiko dar, und ist aus diesem Grund leider nicht möglich. Bitte verwenden Sie einen beliebigen anderen, aktuellen Browser, um den Chat zu nutzen.</p>";
	exit;
}


//header('Content-type: text/html; charset=utf-8');
ini_set('display_errors', true);
error_reporting(E_ALL);
if (ob_get_level() == 0) ob_start();

if(!isset($_SESSION)){session_start();}

if (!file_exists("user/user.txt")) {
	$text = "Gast****fe01ce2a7fbac8fafaed7c982a04e229\n"; // Dateiinhalt
	$dateiname = "user/user.txt"; // Name der Datei
	$handler = fOpen($dateiname , "a+"); // Datei öffnen, wenn nicht vorhanden dann wird die Datei erstellt.
	fWrite($handler , $text); // Dateiinhalt in die Datei schreiben
	fClose($handler); // Datei schließen
}

/* BOM Warnhinweis und BOM entfernen */
$str = file_get_contents('chat_config.php');
$bom = pack("CCC", 0xef, 0xbb, 0xbf);
if (0 === strncmp($str, $bom, 3)) {
	$bom_warn = '<span style="color:red">HINWEIS: </span>Die Datei chat_config.php wurde als utf-8 mit BOM gespeichert. <br />Bitte speichere die Datei als utf-8 <span style="color:red">ohne</span> BOM!';
    echo $bom_warn."\n";
    $str = substr($str, 3);
	$config_handle = fopen("chat_config.php","w");
	fwrite($config_handle, $str);
	fclose($config_handle);
}

if (file_exists("personal_config_inc.php")) {
	$str2 = file_get_contents('personal_config_inc.php');
	$bom = pack("CCC", 0xef, 0xbb, 0xbf);
	if (0 === strncmp($str2, $bom, 3)) {
		$bom2_warn = '<br /><span style="color:red">HINWEIS: </span>Die Datei personal_config_inc.php wurde als utf-8 mit BOM gespeichert. <br />Bitte speichere die Datei als utf-8 <span style="color:red">ohne</span> BOM!';
	    echo $bom2_warn."\n";
	    $str2 = substr($str2, 3);
		$config_handle2 = fopen("personal_config_inc.php","w");
		fwrite($config_handle2, $str2);
		fclose($config_handle2);
	}
}


include("chat_config.php");


if (file_exists("chtver.php")) {
	include("chtver.php");
	$version = $chtver;
}


// Sprache abfragen und lang-file includen:
if (isset($_COOKIE["lang"])) {
	$lang = $_COOKIE["lang"];
} else {
      $lang = $languages[0];
}
$lang_file = 'lang'.strtoupper($lang).'_inc.php';
if (file_exists($lang_file)) {
	include ($lang_file);
}


if (isset($stil) && $stil <> 0) {
        $s = $stil;
} elseif (isset($_COOKIE["Style"])) {
	$s = $_COOKIE["Style"];
} else {
        $s = $default_skin;
}
if (isset($_COOKIE["LoginCookie"]) && isset($_COOKIE["nick"])) {
	$ppp = $_COOKIE["LoginCookie"];
//	goodlog(); // ergibt redir Schleife
}

if (isset($_COOKIE["schrift"])) {
	$schrift= $_COOKIE["schrift"];
} else {
	$schrift = 100;
}


if (!file_exists("user/user.txt")) {
	goodlog(); // weil nur im chat.php setup.php aufgerufen wird, um ggf.user.txt zu erstellen
}

if (file_exists("admin/mods.txt") && file_exists("admin/admin_mods.php")) {
	$mods = file("admin/mods.txt",FILE_IGNORE_NEW_LINES); 
}



// so kann man Optionen setzen per http://pfad-zum-chat/login.php?datum=aus&uhr=aus&sound=aus
if  (isset($_REQUEST['datum']) && $_REQUEST['datum'] == "aus") {
	setcookie("datum", "aus", time()+3600*24*30);	
}
if  (isset($_REQUEST['uhr']) && $_REQUEST['uhr'] == "aus") {
	setcookie("uhr", "aus", time()+3600*24*30);	
}
if  (isset($_REQUEST['sound']) && $_REQUEST['sound'] == "aus") {
	setcookie("sound", "aus", time()+3600*24*30);	
}


$guest_write = "";

$angemeldet = "user/user.txt";
$lines = file($angemeldet);
array_unshift($lines,"Gast****fe01ce2a7fbac8fafaed7c982a04e229"); // macht Gastlogin nur noch von $allowguest abhängig machen, auch wenn der erste User in der user.txt gelöscht wird.

$meldung = 	_LOGIN_FORM; 
$meldung1 = _LOGIN_FORM1;

$meldung2 = _LOGIN_GUEST;

$alluser[0] = ""; // sollte jemand alle User löschen
// Prüfung ob Nick in der reg-DB:
foreach ($lines as $line_num => $line) {
     $user = explode("****",$line);
     $alluser[] = $user[0];
}



function goodlog() {
	global $standard, $admins, $show_hello,$room, $ip_write;
	
	$host  = $_SERVER['HTTP_HOST'];

	if (isset($_SERVER['HTTP_REFERER'])) {
		$referer = parse_url($_SERVER['HTTP_REFERER']);

		$referer = $referer['host'];
			
		$host  = $_SERVER['HTTP_HOST'];
	
		if ($referer != $host) { // Eintritt mit Userübergabe nur von gleicher Domain
			echo "Userübergabe nur von gleicher Domain!"; exit;
		}

	}
	
	$uri  = rtrim(dirname(htmlspecialchars($_SERVER['PHP_SELF'])), '/\\');
	if (isset($room)) {
		$extra = 'chat.php?room='.$room;
	} else {
		$extra = 'chat.php';
	}
	
	$_SESSION['login'] = 1;
	
	
	
	// jetzt festhalten, wann und von welcher IP login.php aufgerufen wird
	$regtime = time()."****".$_SERVER['REMOTE_ADDR']."\n";

	$file8 = 'user/user_login.txt';	
	
	$lines = file($file8);
	foreach ($lines as $line_num => $line) { // aelter als 1 h loeschen
		$part = explode("****",$line);
		if ((time() - $part[0]) < 60*60 && $ip_write === true) {
			$regtime .= $line;
		}
	}
	
	$open8 = fopen($file8, "w");
	fwrite($open8,$regtime);
	fclose($open8);
	
	// Login cookie setzen
	if (isset($_POST['merken']) && $_POST['merken'] == 'on') {
		$Gueltigkeit = time()+86400*30;	// cookie gilt 30 Tage
		setcookie("LoginCookie", $_POST['password'] , $Gueltigkeit);
	}
	
//	if (!isset($guest_login_block_time)) {$guest_login_block_time = 60; }
//	if ($howlonglogged >= $guest_login_block_time ) {
	// Die Eintrittsmeldung
	if (file_exists('profile/'.$_SESSION["chatuser"].'.jpg')) {
		$prof_link ='<span class="av_link"><a href="popprof.php?profil='.$_SESSION["chatuser"].'" onclick="ProfilOeffnen(this.href); return false;" title="Profil von '.$_SESSION["chatuser"].' anschauen (Popup)"  > <img class="av" src="profile/'.$_SESSION["chatuser"].'.jpg" alt="" height="32"></a></span>';
	} else {
		$prof_link = "";
	}

	if (file_exists('ip_inc.php')) {
		include ('ip_inc.php');
	} else {
		$ip_login = '</span> <span class="ip">'.$_SERVER['REMOTE_ADDR'].'</span>';
	}
		
	$herein ='<p><span class="dt">'.date("d\.m\.").'</span> <span class="uz">'.date("H:i:s").'</span><span class="tr"> | </span>'.$prof_link.' <span class="hello" oncontextmenu="ads(\'/erase '.date("H:i:s").'\'); return false;" onclick="ads(\'\u0040'.$_SESSION["chatuser"].'\'); return false; "  title ="'.date("d\.m\. H:i:s").'" style="color:#888; cursor: pointer">['.$_SESSION["chatuser"].'</span>  <span class="hello">'._NEWUSER.'] </span> </span> '.$ip_login.'</p>'.PHP_EOL;	

	if (!in_array($_SESSION["chatuser"], $admins) && $show_hello == "yes"
	)  {

		$file5 = 'rooms/'.$standard;
		$open5 = fopen($file5, "a");
		fwrite($open5,$herein);
		fclose($open5);
	}

	if (file_exists("telegram_bot.php")) {
		if (!in_array($_SESSION["chatuser"], $admins)) {	
			include("telegram_bot.php");
		}
	}

	if (file_exists("mail2sms.php") && isset($sms_empfaenger)) {
		if (!in_array($_SESSION["chatuser"], $admins)) {	
			include("mail2sms.php");
		}
	}
		
    // Redirect zum Chat:
	// echo $host$uri/$extra; exit;
	header("Location: //$host$uri/$extra");
	exit();
}


// Max. Anzahl User beim Login abfangen
$all_users = 0;
$user1=0;
$userdir = 'user/';
if ($handle = opendir($userdir)) {
	// Das ist der korrekte Weg, ein Verzeichnis zu durchlaufen:
	while (false !== ($file = readdir($handle))) {
		if(strpos($file,"p_") == 1){
			$user1 = count(file($userdir.$file));
			$all_users += $user1; 
		}
	}
	closedir($handle);
}


if (isset($maxuser) && $all_users >= $maxuser) {
    echo '<p>('.$all_users.') Maximale Anzahl User im Chat erreicht - please try later</p>';
    exit;
}


// cookie nick holen fuer ban und maulkorb:
$nickname ="";
if (isset($_COOKIE["nick"])) {
	$nickname = $_COOKIE["nick"];
}


// kein Login fuer gebannte User

if (file_exists("user/ban.txt")) {
	$banned = file("user/ban.txt");
	foreach ($banned as $c) {
		$part1 = explode("****",$c);
		$part2 = explode("++++",$part1[1]);
		if (strlen($c) > 1 && $part2[0] > time()) {
			$rip = $_SERVER['REMOTE_ADDR'];

      		if ($part1[0] == $nickname || (trim($rip) == trim($part2[1]) ))  {
				echo "You're banished";
				exit();
			}
		}
	}
}

// kein Login fuer muzzled User
if (file_exists("user/maulkorb.txt")) {
	$banned = file("user/maulkorb.txt");
	foreach ($banned as $c) {
		$part1 = explode("****",$c);
		$part2 = explode("++++",$part1[1]);
		if (strlen($c) > 1 && $part2[0] > time()) {
			$rip = $_SERVER['REMOTE_ADDR'];

      		if ($part1[0] == $nickname || (trim($rip) == trim($part2[1]) ) )  {
				echo "You're muzzled";
				exit();
			}
		}
	}
}

// nur alle $guest_login_block_time ein login zulassen
$closed = 0;
if (!isset($guest_login_block_time)) {$guest_login_block_time = 60; }
$reg_closed = false;

if (isset($_POST['username']))    $uebernahme = htmlspecialchars($_POST['username']);

if (isset($uebernahme)) {$reg_closed = false;}
elseif (file_exists("user/user_login.txt")) {
	$prev_reg = file("user/user_login.txt");
	foreach ($prev_reg as $c) {
		$part = explode("****",$c);
		$frei = time() - $part[0];

		if (strlen($c) > 1 && $frei < $guest_login_block_time) {
			$rip = $_SERVER['REMOTE_ADDR'];

			$closed = $guest_login_block_time-$frei; 


			if (trim($rip) == trim($part[1])) {
			
			        $meldung2 = '<span>'._LOG_FORM.'<span aria-live="assertive" aria-relevant="all"  id="countdwn"></span></span>';

					if(file_exists('nopw.php') && isset($nopw) && $nopw == true ) {
					$meldung= $meldung2;
					}

					
				$reg_closed = true;
			}
		}
	}
}



//session_start();
$_SESSION['login'] = 0;
unset($_SESSION['chatuser']);


// nick per POST oder GET Parameter übernehmen, z.B. aus einem Forum:
// if (isset($_POST['username']) || isset($_GET['username']) && isset($maxuser) && $user1 < $maxuser) {
// oder, wem GET zu unsicher erscheint, dann folgenden Code:
// GET ist vor allem erforderlich, um den Chat in einem Popup zu öffen
if (isset($_POST['username']) && isset($maxuser) && $user1 < $maxuser) {
    // Parameter säubern:
    	
    if (isset($_POST['username']))    $uebernahme = htmlspecialchars($_POST['username']);
    if (isset($_GET['username']))    $uebernahme = htmlspecialchars($_GET['username']);
    $uebernahme = str_replace(" ","_", $uebernahme);
	
    if (isset($_POST['room']))    $room = htmlspecialchars($_POST['room']);

	
	
    // Prüfung auf erlaubte usernames:
    if ($uebernahme == "")  { // wenn übergebener Nick leer, d.h. User nicht im Forum registriert
            $meldung = '<span style="color:red;font-weight:bold;">'._LOGIN_FORM_FORUM1.'</span>';
	
	} elseif (in_array($uebernahme, $alluser) && !in_array($uebernahme, $admins))  { // wenn Nick schon registriert, Admins kommen direkt rein
        $meldung = '<span style="color:red;font-weight:bold;">'._LOGIN_FORM_FORUM2.'</span>';

    } elseif (in_array(strtolower($uebernahme), $nicknotallowed))  { // andere verbotene Nicks (chat_config.php)
        $meldung = '<span style="color:red;font-weight:bold;">'._LOGIN_FORM_FORUM3.'</span>';

    } elseif (preg_match("/:|;|,|\.|%|\"|<|>|\?|\/|&| |\+|\*|@|'/",$uebernahme)) {
        $meldung = '<span style="color:red;font-weight:bold;">'._LOGIN_FORM_FORUM4.'</span>';

	} elseif ($reg_closed == true) {
        // do nothing $meldung = '<span style="color:red;font-weight:bold;">'._LOG_FORM.'</span>';

	} elseif (in_array($uebernahme, $admins)) {
        // do nothing $meldung = '<span style="color:red;font-weight:bold;">Admin-Login</span>';

	} elseif (in_array($uebernahme, $mods)) {
        // do nothing $meldung = '<span style="color:red;font-weight:bold;">Mod-Login</span>';


    } else {
			$_SESSION['chatuser'] = $uebernahme;
			setcookie("nick",$uebernahme, time()+86400*30); // nick cookie speichern fuer ban
			goodlog();
    }
}

$nopass = false;
if (isset($nopw) && $nopw === true && file_exists("nopw.php")) {$nopass = true;}
    
if (isset($_POST['regname'])) {  // wenn login abgeschickt

$regname = trim(str_replace(" ","_",$_POST['regname']));

	if ($allowguest != "yes" && $regname == "Gast") {  
		echo "No Guests allowed"; // wenn jmd trotz ausgeblendetem Gast-Login Gast/demo eingibt
		exit();        
	}


	// Zugang ohne Registrierung, aber mit Name (nur mit Lizenz):

	if ($_POST['password'] =='' && $nopass === true) {
			include("nopw.php");


	} else { // wenn Nickname und PW übereinstimmen:
	     foreach ($lines as $line_num => $line) {
	       if(strpos($line,"****") !== false) {
	          $reguser = explode("****",$line);
	          $regged = trim($reguser[1]);

	          if (trim($reguser[0]) != "") {
	                if (trim($reguser[0]) == $regname && $regged == md5($_POST['password'])) {
		
						
	                      // Gaeste durchnummerieren:
	                      if ($regname == "Gast") {
									
	                            $gast = "user/gast.txt";
	                            if (file_exists($gast)) {
	                                $gastnr = file($gast);
	                                $gastnr = $gastnr[0] +1;
	                            } else {
	                                $gastnr = 100;
	                            }

	                            $open3 = fopen($gast, "w");
	                            flock($open3,LOCK_EX);
	                            fwrite($open3,"$gastnr");
	                            flock($open3,LOCK_UN);
	                            fclose($open3);
	                            $reguser[0] .= '_'.$gastnr;
								$_SESSION['chatuser'] = $reguser[0];
								setcookie("nick",$reguser[0], time()+86400*30); // nick cookie speichern fuer ban
							
							
							} else {
								$_SESSION['chatuser'] = $reguser[0];
								setcookie("nick",$reguser[0], time()+86400*30); // nick cookie speichern fuer ban
								
							}

							if ($reg_closed == true && $regname == "Gast") {
							        // do nothing $meldung = '<span style="color:red;font-weight:bold;">'._LOG_FORM.'</span>';
							} else {	
	                    		goodlog();
							}						
					
	                } elseif (trim($reguser[0]) == $regname && $regged != md5($_POST['password'])) {
	                      $meldung = '<span style="color:red;font-weight:bold;">'._LOGIN_FORM2.'</span>';
	                }
	          }
	        }
	     }
	     if (!in_array($regname, $alluser))  { // wenn Nick nicht registriert
	         $meldung = '<span style="color:red;font-weight:bold;">'._LOGIN_FORM3.'</span>';
	     }
	}
}	
	
	
// http://aktuell.de.selfhtml.org/artikel/css/mobile-endgeraete/
if(file_exists("check_mobile.php")) {
       require('check_mobile.php');
       if(check_mobile() === true) {
        	$s = 7;
        	echo '<style type="text/css">';
            echo 'a.footerlink{color:#222}';
            echo '</style>';

       } elseif ($s == 7) {
                echo '<style type="text/css">';
                echo 'body {width: 23em;}';
                echo '</style>';
       }
}


 

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang;?>">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="description" content="Der barrierefreie Chat von webdesign weisshart" />
<meta name="keywords" content="@bots: please look for keywords in document body ;-)" />
<meta name="author" content="Dipl.-Ing.(FH) F. Weisshart" />
<meta name="generator" content="notepad ;-)" />
<meta name="robots" content="index, follow" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />

<title>Zugang zum Chat von webdesign weisshart - Login</title>


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
<meta name="msapplication-TileColor" content="#ffc40d">
<meta name="msapplication-TileImage" content="img/mstile-144x144.png">
<meta name="msapplication-config" content="img/browserconfig.xml">
<?php
if ($s != 7) {
echo '	 
<link rel="stylesheet" type="text/css" media="screen" href="helpcss'.$s.'.css" />
';
} else {
echo '
<style> p, label, input, legend {color:#000;} </style>
';	
}
?>

<link rel="stylesheet" type="text/css" media="screen" href="login.css" />

<script src ="anw_anz_noonload.js"></script>
<script src="wait.js"></script>
<script src="chat_js.php"></script>


<script>

var zeit = <?php echo $closed; ?>;      

function ZeitAnzeigen() {
if (document.getElementById('countdwn')) {
	if (zeit >= 1) {
		document.getElementsByTagName('legend')[0].style.color="red";
		document.getElementById('countdwn').innerHTML = 'in&nbsp;' + zeit + '<?php echo _SEC; ?>';
		zeit = zeit - 1;
		setTimeout("ZeitAnzeigen()", 1000);
	}
	else {
		document.getElementsByTagName('legend')[0].style.color="green";
		document.getElementById('countdwn').innerHTML = '';
	}
}
}


// function laden() {
// //    noshowWait();
// 	document.getElementById('regname').focus();
// }
//
// if (navigator.cookieEnabled == true) {
//       window.onload=laden;
// }

</script>

<style>
/*#wdw_logo {height: 50px}*/
a.datenschutz{color:#fff;}
a:hover.datenschutz, a:focus.datenschutz {color:#fff;background:transparent;text-decoration:none;}
#output {font-size: 0.8em !important;}
#output li {list-style-type:none;}
<?php
if ($s != 12 && $s != 7 ) {
	echo 'body {font-size:'.$schrift.'% !important;}';
}
?>
</style>

</head>
<body>
<section role="main">
<?php 
echo "<!-- Info: Server = ".$_SERVER['SERVER_SOFTWARE']." - Chat-Version: ".$version." - PHP-Version: ".phpversion()." -->"; 
?>


<!-- die folgende Zeile ist nur fuer die Demo, und kann ohne Folgen geloescht oder auch belassen werden: -->
<!-- Wer will, kann hier ein eigenes Logo einbinden -->
<?php if(file_exists("wdw_inc.php") && $s != 12 && $s != 7 ) {
	echo '<div style="margin:auto; height:55px; max-width: 200px;">';
	include ("wdw_inc.php"); 
	echo '</div>';
}
?>

<h1><?php echo _LOGIN_H1; ?></h1>


<noscript><p style="color:red; font-weight:bold;"><br />Zur Benutzung des Chat muss Javascript aktiviert sein.<br /><br /></p></noscript>

<?php if(file_exists("lang_switcher_inc.php")) {include("lang_switcher_inc.php");} ?>


<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" id="login">
<fieldset>
<legend><?php echo $meldung; ?></legend>

<input type="checkbox" id="merken" name="merken" <?php if (isset($ppp)) echo "checked='checked'"; ?>/><label for="merken"> <?php echo _LOGIN_FORM4; ?></label>
<br />
<label for="regname" class="left"><?php echo _LOGIN_GUEST2; ?></label>
<input type="text" size="20" maxlength="15" id="regname" name="regname" value="<?php if(isset($nickname) && strpos($nickname,'Gast') === false ) echo $nickname; ?>" autofocus /><br />

<label for="password" class="left"><?php echo _LOGIN_GUEST3; ?> </label>
<input type="password" id="password" name="password" value="<?php if(isset($ppp)) echo $ppp; ?>"/><br />
<input type="submit" class="right" value="Login" />

<p style="text-align:left;">
<?php 
if (!isset($ppp)) {
	if ($nopass === true) {
		if (isset($allowguest) && $allowguest == "yes") {
			echo '<span style="font-size:.85em"><br />'._NO_REG.'</span>';
		} elseif (isset($allowguest) && $allowguest == "no") {
			echo '<span style="font-size:.85em"><br />'._NO_REG_NO_GUEST.'</span>';
		}
	}
}
?>
</p>

</fieldset>
</form>



<?php
// @include("chat_config.php"); // wg guest_write

// Gastlogin ohne PW-Eingabe <legend>'._LOGIN_GUEST.'</legend>

if ($nopass === true) {$allowguest = "no"; }
if (isset($allowguest) && $allowguest == "yes") {
echo '
<form method="post" action="login.php" id="guest_login">
<fieldset>

<legend>'.$meldung2.'</legend>

<input name="regname" id="regname1" type="hidden" value="Gast" />          
<input name="password" id="password1" type="hidden" value="demo"/>
';

// Zugang als nummerierter Gast
if (isset($allowguest) && $allowguest == "yes") {
   
   if (isset($guest_write) && $guest_write == "no") {
		echo '<p style="font-size:.75em;" id="nur_lesen">'._LOGIN_GUEST5.'</p>';
   } else {
		// echo '<p style="font-size:.75em; text-align:center;">'._LOGIN_GUEST4.'</p>'; // ueberfluessige Info
   }
}

echo '
<input type="submit" class="right" value="Enter" />
</fieldset>
</form>
';
}

// zur Registrierung

$show_reg = true;
if (isset($_COOKIE["nick"])
|| (isset($nopass) && $nopass === true)
) {
	if (file_exists("user/user.txt")) {
		$registered = file("user/user.txt");
		foreach ($registered as $r) {
			$part1 = explode("****",$r);
	      	if (isset($_COOKIE["nick"]) && ($_COOKIE["nick"] == trim($part1[0]))) {
				//echo $_COOKIE["nick"];
				$show_reg = false;
			} 	
		}
	}
}


	
if ($show_reg === true && file_exists('reg.php'))	{
	
	echo '
	<form action="reg.php" id="reg">
	<fieldset>
	<legend>'._TO_REG3.'</legend>
	<p style="font-size: .75em">';
	
	echo _TO_REG;


	if (isset($create_profile) && $create_profile == "yes"){ echo _TO_REG2;}

	echo '.</p>
	<input class="right" value="'._TO_REG3.'" type="submit" />
	</fieldset>
	</form>
	';
}



echo '<p class="lnk"><a href="//webdesign.weisshart.de">Chat by webdesign weisshart</a></p>';

if (file_exists("jmstv.php")) {
include("jmstv.php");
if ($jmstv_start <> $jmstv_end) {
echo '
<p style="font-size: .7em; clear:left;">Hinweis:
<br />User generated content! Es kann nicht ausgeschlossen werden, dass im Chat jugendgef&auml;hrdende Fotos hochgeladen oder verlinkt werden.
Fotos werden daher tags&uuml;ber verpixelt, und nur zwischen '.$jmstv_start.':00 Uhr und '.$jmstv_end.':00 Uhr unverpixelt angezeigt.</p>
';
}
}
?>


<?php
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
echo '
<p  style="font-size: .7em;">Über eine <a href="https://'.htmlspecialchars($_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]).'">geschützte (verschlüsselte) SSL/TLS Verbindung</a> chatten.
';
} else {
echo '
<!-- <p  style="font-size: .7em;">Hinweis: Die Verbindung zum Chat ist mit SSL/TLS geschützt / verschlüsselt. -->
';
}

if ($show_user == "yes") {
	echo '<p style="font-size:.7em"><span id="user_online"></span> (max.&nbsp;'.$maxuser.')</p>';
}

// if ($_SERVER['SERVER_NAME'] == "webdesign.weisshart.de") {
// 	echo '<p id="support_online" style="font-size:.7em"></p>';
// }


?>


</section>


<?php 

ob_flush();
flush();
ob_end_flush();

?>

<div id="preload">
<?php
// das dir img durchsuchen und die smileys und bg preloaden:

if ($s != 7) {

	$verz = 'img';

	$bilder = array();

	$dir=opendir($verz);
	while (false !== ($file = readdir($dir))) {
	    if(strpos($file,".gif") !== false || strpos($file,".jpg") !== false) {
	        $bilder[] = $file;
	    }
	}

	foreach($bilder as $wert) {
	  $smiley = '<img src="'.$verz.'/'.$wert.'" alt="" />';
	  // echo '<a href="#" >'.$smiley.'</a>';
	  echo $smiley;
	}

	closedir($dir);
}
?>
</div>

<script src="popbild.js"></script>

<script>
// http://ichwill.net/chapter4.html

function addEvent(obj, evType, fn){
 if (obj.addEventListener){
   obj.addEventListener(evType, fn, false);
   return true;
 } else if (obj.attachEvent){
   var r = obj.attachEvent("on"+evType, fn);
   return r;
 } else {
   return false;
 }
}
addEvent(window, 'load', ZeitAnzeigen);

/* Für Support Chat: Zeigt die Support-Verfügbarkeit */


var testObj = null;
    testObj = new XMLHttpRequest();

	function eineFunktion() {
		if (document.getElementById("support_online")) {
			if (testObj.readyState == 4) {
				if (testObj.responseText == 1) {
					document.getElementById("support_online").innerHTML = '<span style="font-size:16px;vertical-align: -.2em">☞</span><?php echo _SUPPORT ?>';
					document.getElementById("support_online").style.color = "green";
				} else {
					document.getElementById("support_online").innerHTML = "<?php echo _NOSUPPORT ?>";
					document.getElementById("support_online").style.color = "red";
				}
			}
		}
	}

function update() {
    testObj.open("GET", "support_online.php");
    testObj.onreadystatechange = eineFunktion;
    testObj.send(null);
	
	window.setTimeout(update,5000);
}

// window.onload = update();
addEvent(window, 'load', update); //addEvent steht mobil nicht zur Verfügung?

/* Zeigt die User dynamisch */
var xhttp = null;
var xhttp = new XMLHttpRequest();

function eineFunktion2() {
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("user_online").innerHTML = this.responseText;
		}
	};
}


function update2() {
	xhttp.open("GET", "user_online.php", true);
	xhttp.onreadystatechange = eineFunktion2;
	xhttp.send(null);
	
	window.setTimeout(update2,4000);
}

addEvent(window, 'load', update2); //addEvent steht mobil nicht zur Verfügung?
</script>



<?php if (file_exists("footer_inc.php")) include("footer_inc.php"); ?>

</body>
</html>