<?php

$globvar = array();
$globvar['title'] = 'IMGhost'; 
$globvar['2ndtitle'] = 'Host your images';
$globvar['maxsize'] = '1536'; // Angabe in Kilobyte
$globvar['resizemode'] = 3; // 1 = feste Thumbbreite + Hoehe; 2 = Prozentual; 3 = Feste Breite + dynamische Hoehe
$globvar['thumb_percent'] = 40;		// Prozentwert welcher Bilder um X% verkleinert
$globvar['thumb_fixed'] = 150;		// Wert der festen Breite im resizemode 3
$globvar['thumbwidth43'] = 180;      // neu resize 
$globvar['thumbheight43'] = 135;     // fuer 16/9/
$globvar['thumbwidth169'] = 240;     // und 4/3
$globvar['thumbheight169'] = 135; 
$globvar['visiblecopyright'] = ' &copy;2009 <a href="http://www.somegas.de">Sascha Ohms</a>'; // Darf frei editiert werden
$globvar['use_randomname'] = true; // Zufallsname oder alten Dateinamen uebernehmen
$globvar['twitter'] = true; // schaltet die Ausgabe des Twitterlinks an / aus
$globvar['showformafterup'] = true; // Option um das Uploadformular nach dem Upload auszublenden
$globvar['validimages'] = array(".jpg", ".gif", ".png"); // Valide bildformate hier eintragen
$globvar['needpassword'] = false; // Zum Hochladen wird ein Passwort benoetigt
$globvar['password'] = "";
$globvar['allowreg'] = true; // Duerfen user sich registrieren?
$globvar['allowlogin'] = true; // Duerfen bereits registrierte Benutzer sich anmelden?

// Includes
include('inc/functions.inc.php');
include('inc/checkdb.php');

ob_start();
include('tpl/header.tpl.php');

if(isset($_GET['d'])) {
    if(isset($_GET['c'])) {
        if(empty($_GET['d']) || empty($_GET['c'])) {
            error('Nicht gen&uuml;gend Parameter angegeben.');
        } elseif(!file_exists('./i/' .$_GET['d']) && !is_valid_filename($_GET['d'], $globvar['validimages'])) {
            error('Datei existiert nicht oder ist unzul&auml;ssig.');
        } else {
            $imageName = sqlite_escape_string($_GET['d']);
            $deleteString = sqlite_escape_string($_GET['c']);
            
            // pruefen, ob Bildname und Code in der Datenbank vorhanden sind
            $db->query("SELECT imageName, deleteString FROM img_images WHERE imageName = '" .$imageName. "' AND deleteString = '" .$deleteString. "'");
            
            if($db->numRows()) {
                $db->query("DELETE FROM img_images WHERE imageName = '" .$imageName. "' AND deleteString = '" .$deleteString. "'");
                unlink("./i/" .$imageName); // Bild loeschen
                unlink("./i/t/" .$imageName); // Thumbnail loeschen
				
				if(islogged())
					$url = './?profile';
				else
					$url = './';
				
                success('Bild erfolgreich gel&ouml;scht.', 1, $url);
            } else {
                error('Code und Bildname passen nicht zusammen.');
            }
        }
    }
} elseif(isset($_GET['reg']) && $globvar['allowreg'] == true) {
	if(isset($_POST['reg_submit'])) {		
		$email = sqlite_escape_string($_POST['email']);
		$username = sqlite_escape_string($_POST['username']);
		$pass = $_POST['pass'];
		$pass_enc = encryptpass($_POST['pass']);
		$passrepeat_enc = encryptpass($_POST['passrepeat']);
		$regdate = time();
		
		$db->query('SELECT * FROM img_users WHERE userName = "' .$username. '" OR userMail = "' .$email. '"');
		
		if($db->numRows())
			$error[] = 'Benutzername oder E-Mail bereits vorhanden.';		

		if(empty($email) || empty($username) || empty($pass_enc) || empty($passrepeat_enc))
			$error[] = 'Bitte alle Felder ausf&uuml;llen.';
			
		if($pass_enc != $passrepeat_enc)
			$error[] = 'Passw&ouml;rter stimmen nicht &uuml;berein.';
			
		if(!isset($error)) {
			$db->query("INSERT INTO 'img_users' (userName, userMail, regDate, userPass) VALUES ('" .$username. "', '" .$email. "', '" .$regdate. "', '" .$pass_enc. "')");
			$db->query("SELECT last_insert_rowid() AS lastID");
			$db->fetch();
			
			writemail($email, $username, $pass, $globvar['title']);
			setcookie("img_username", $username, time()+3600*24*356);
			setcookie("img_password", $pass_enc, time()+3600*24*356);
			setcookie("img_userid", $db->row('lastID'), time()+3600*24*356);
			success('Erfolgreich registriert.', 1);
		} else {
			foreach($error as $err) 
				error($err);
			
			die();
		}
	} else {
		include 'tpl/register.tpl.php';
	}
} elseif(isset($_GET['login']) && $globvar['allowlogin'] == true) {
	if(isset($_POST['login_submit'])) {
		$username = sqlite_escape_string($_POST['username']);
		$pass = encryptpass($_POST['password']);
	
		$db->query("SELECT * FROM 'img_users' WHERE userName = '" .$username. "' AND userPass = '" .$pass. "'");
		
		if(!$db->numRows())
			$error[] = 'Logindaten nicht korrekt.';		

		if(empty($username) || empty($pass))
			$error[] = 'Bitte alle Felder ausf&uuml;llen.';	
			
		if(!isset($error)) {
			$db->fetch();
			$getUser = $db->row('userName');
			$getPass = $db->row('userPass');
			$getUserID = $db->row('userID');
			
			setcookie("img_username", $getUser, time()+3600*24*356);
			setcookie("img_password", $getPass, time()+3600*24*356);
			setcookie("img_userid", $getUserID, time()+3600*24*356);
			success('Erfolgreich eingeloggt.', 1);
		} else {
			foreach($error as $err) 
				error($err);
			
			die();
		}
	} else {
		include 'tpl/login.tpl.php';
	}
} elseif(isset($_GET['logout']) && islogged()) {
	setcookie("img_username", "", time()-1000);
	setcookie("img_password", "", time()-1000);
	setcookie("img_userid", "", time()-1000);
	success('Erfolgreich ausgeloggt.', 1);
} elseif(isset($_GET['profile']) && islogged()) {
	$userID = (int) $_COOKIE['img_userid'];
	$serverurl = getServer();
	
	$epp = 10; // Eintraege pro Seite
	$p = !empty($_GET['p']) ? (int) $_GET['p'] : 1;
	$gft = $p*$epp-$epp;
	
	$db->query("SELECT * FROM 'img_images' WHERE uploadedBy = " .$userID);
	$picCount = $db->numRows();
	
	// Seitenzahl berechnen
	if($picCount % $epp == 0) 
		$pageCount = $picCount / $epp;
	else
		$pageCount = ($picCount + ($epp - ($picCount % $epp))) / $epp;
	
	
	if($pageCount < $p) {
		error('Seite existiert nicht.');
	} else {	
		$db->query("SELECT * FROM 'img_images' WHERE uploadedBy = " .$userID. " ORDER BY insertDate DESC LIMIT " .$epp. " OFFSET " .$gft);

		while($db->fetch()) {
			$output[] = array($db->row('imageName'), $db->row('numClicks'), date("d.m.y H:i", $db->row('insertDate')), $db->row('deleteString'));
		}
		
		include 'tpl/profile.tpl.php';
	}
	
} elseif(isset($_GET['s'])) {
    // Fragt ab, ob ueberhaupt der Bildname mitgeliefert wurde
    if(empty($_GET['s'])) {
        error('Kein Bild angegeben.');		
    } else {
        $img = sqlite_escape_string($_GET['s']);
        $imgausgabe = './i/' .$img; 
		
        // Clickanzahl des Bildes laden
        $db->query("SELECT imageID, imageName, numClicks FROM img_images WHERE imageName = '".$img."'");
                
        if($db->numRows()) {
            $db->fetch();
            $clicks = $db->row('numClicks'); // wird ausgegeben
            $imageID = $db->row('imageID');
            
            // Clickcounter um eins erhoehen
            $db->query("UPDATE 'img_images' SET 'numClicks' = numClicks+1 WHERE imageID = " .$imageID);
        
            include('tpl/einzel.tpl.php');        
        } else {
			error('Datei existiert nicht.');
		}
    }        
} else {
	if(isset($_POST['nsubmit'])) { 
		if($globvar['showformafterup']) {
			include('tpl/index.tpl.php');
		}
					
		if($globvar['needpassword'])  {
			if($_POST['password'] != $globvar['password']) {
				echo '<p><img src="./inc/img/zeichen.png" alt="" \> Falsches Passwort</p>';
				exit;
			}
		}				
			
		$submitted = true;			
	}  

	if(!isset($submitted)){
		include('tpl/index.tpl.php');
		$submitted = false;	
	}	

    if($submitted) {
        $tempname = $_FILES['nfile']['tmp_name']; 
        $type = $_FILES['nfile']['type'];        
        $thename = $_FILES['nfile']['name'];
        
        $endung = strtolower(substr($thename, -4));
        
		
        // Abfrage, ob Bildnamen nach dem Zufall gewaehlt werden sollen. Wenn nicht wird der eigentlich Bildname verwendet
		$name = genName($globvar['use_randomname'], $endung, $thename);
        
		// einmalige ueberpruefung, ob Datei bereits existiert
		if(file_exists('i/' . $name)) {
			if($globvar['use_randomname'] == true) {
				$name = genName();
			} else {
				$filename = basename($thename, $endung);
				$name = $filename.rand(1000, 9999).$endung;
			}
		}
		
        // Bildgroesse in kb umrechnen
        $size = $_FILES['nfile']['size']; 
        $size = round($size / 1024, 2);
        
		
        // Weitere pruefung, ob Bild auch wirklich ein Bild ist
        if(!in_array($endung, $globvar['validimages'])) {
            error('Invalides Bildformat.');
        // Abfrage, ob Bild  die maximale groesse ueberschritten hat
        } else if($size > $globvar['maxsize']) {
            error('Das Bild ist gr&ouml;&szlig;er als ' .$globvar['maxsize']. ' kb');
        } else {    
            $thumbdir = './i/t/';                        
            $thumb_width = $globvar['thumbwidth43'];
            $thumb_height = $globvar['thumbheight43'];
            
			
            // Datei wird verschoben
            move_uploaded_file($tempname, './i/' .$name);
            $imginfo = @getimagesize('./i/' .$name); // Wichtige Abfrage fuer Bildtyp und Seitenverhaeltnisse
            $height = $imginfo[1];
            $width = $imginfo[0];
            
			
            // Entsprechend dem Bildnamen Funktionen ausaehlen
            if($imginfo[2] == 2) {
                $src = imagecreatefromjpeg('i/' .$name);
                $typeausgabe = '.jpg';
            } elseif($imginfo[2] == 3) {
                $src = imagecreatefrompng('i/' .$name);
                $typeausgabe = '.png';
            } elseif($imginfo[2] == 1) {
                $src = imagecreatefromgif('i/' . $name);
                $typeausgabe = '.gif';
            } else {
                error('Dateityp wird nicht unterst&uuml;tzt.');
                @unlink($name); // Wichtig! Falls Dateityp nicht unterstuetzt wird, wird die Datei wieder geloescht
                unset($src);
                include('tpl/footer.tpl.php');
                exit;                
            }
            
			
            // 16/9 check und wenn kleiner als thumb resulotion kein resize
            if($width / $height >= 16 / 9) {
                $thumb_width = $globvar['thumbwidth169'];
                $thumb_height = $globvar['thumbheight169'];
            } elseif($width <= $globvar['thumbwidth169'] && $width <= $globvar['thumbwidth169']) {
                $thumb_width = $width;
                $thumb_height = $height;
            } 
            
			
            if($width <= $globvar['thumbwidth43'] && $width <= $globvar['thumbwidth43']) {
                $thumb_width = $width;
                $thumb_height = $height;
            }
			
			
			// Thumbnails anhand eines Prozentwertes erstellen
			if($globvar['resizemode'] == 2) {
				$thumb_width = $width * $globvar['thumb_percent'] / 100;
				$thumb_height = $height * $globvar['thumb_percent'] / 100;
			}
			
			
			// Thumbnails haben feste Breite. Hoehe wird prozentual berechnet
			if($globvar['resizemode'] == 3) {
				$thumb_width = $globvar['thumb_fixed'];				
				$percent_width = $globvar['thumb_fixed'] * 100 / $width;
				
				$thumb_height = $height * $percent_width / 100;
			}
            
			
            // Thumbnil generieren
            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
            
			
            // Thumbnail mit richtigem Typ im Thumbnails-Ordner abspeichern
            if($imginfo[2] == 2) 
                imagejpeg($thumb, 'i/t/' .$name);
            elseif($imginfo[2] == 3) 
                imagepng($thumb, 'i/t/' .$name);
            elseif($imginfo[2] == 1) 
                imagegif($thumb, 'i/t/' .$name);                            
            
			
            // Rechte auf Bild setzen. Muss nicht bei jedem Server gemacht werden, nur als Vorsichtsmassnahme
            chmod('i/' .$name, 0644);
            chmod('i/t/' .$name, 0644);
            
			
            // ServerURL auslesen
			$serverurl = getServer();
            
			
            // is.gd Link fuer Twitter generieren, wenn Twitter aktiviert ist
            if($globvar['twitter'] == true) {
                $isgdlink = isgd('http://' .$serverurl. 'i/' .$name);
                if($isgdlink != "error")
                    $twitterausgabe = 'http://twitter.com/timeline/?status=' .$isgdlink. ' - ' .$thename;
                else
                    $twitterausgabe = 'http://twitter.com/timeline/?status=http://' .$serverurl. 'i/' .$name;
            }
            
            $deletestring = rand_str(); //  Random Delete String erzeugen
            
            // Ausgabe Links definieren
            $htmlcodeausgabe = '<img src="http://' .$serverurl. 'i/' .$name. '" alt="" />';
            $bbcodeausgabe = '[img]http://' .$serverurl. 'i/' .$name. '[/img]';
            $fullausgabe = 'http://' .$serverurl. 'i/' .$name;
            $thumbausgabe = 'http://' .$serverurl. 'i/t/' .$name;    
            $htmlcodeausgabethumb = '<a href="http://' .$serverurl. 'i/' .$name. '" target="_blank"><img src="http://' .$serverurl. 'i/t/' .$name. '" alt="" /></a>';
            $bbcodeausgabethumb = '[url=http://' .$serverurl. 'i/' .$name. '][img]http://' .$serverurl. 'i/t/' .$name. '[/img][/url]';
            $fullausgabeclick = 'http://' .$serverurl. '?s=' .$name;
            $deletelink = 'http://' .$serverurl. '?d=' .$name. '&amp;c='.$deletestring;
            
            // Eintrag in der Datenbank hinzufuegen
			if(islogged())
				$uploadedBy = (int) $_COOKIE['img_userid'];
			else
				$uploadedBy = 0;
			
			$db->query("INSERT INTO 'img_images' ('imageName', 'numClicks', 'insertDate', 'deleteString', 'uploadedBy') VALUES ('".sqlite_escape_string($name)."', '0', '".time()."', '".$deletestring."', ".$uploadedBy.")");
            
            include('tpl/output.tpl.php');
        }
    }
}

include('tpl/footer.tpl.php');

?>

