<?

$globvar = array();
$globvar['title'] = 'IMGhost'; 
$globvar['2ndtitle'] = 'Host your images';
$globvar['maxsize'] = '1536'; // Angabe in Kilobyte
$globvar['thumbwidth43'] = 180;  	// neu resize 
$globvar['thumbheight43'] = 135; 	// für 16/9/
$globvar['thumbwidth169'] = 240; 	// und 4/3
$globvar['thumbheight169'] = 135; 
$globvar['visiblecopyright'] = ' &copy;2009 <a href="http://www.somegas.de">Sascha Ohms</a>'; // Darf frei editiert werden
$globvar['use_randomname'] = true; // Zufallsname oder alten Dateinamen übernehmen
$globvar['twitter'] = true; // schaltet die Ausgabe des Twitterlinks an / aus
$globvar['showformafterup'] = true; // Option um das Uploadformular nach dem Upload auszublenden
$globvar['validimages'] = array(".jpg", ".gif", ".png", ".JPG", ".GIF", ".PNG"); // Valide bildformate hier eintragen

ob_start();
include('tpl/header.tpl.php');

if(!file_exists('inc/config.inc.php')) {
	$dbname = rand_str(20);

	if ($db = sqlite_open('inc/'.$dbname, 0666)) { 
		// SQLite DB erstellen
		$sql = "
		CREATE TABLE 'img_images' (
		   imageName varchar(30) not null,
		   numClicks integer(11) not null,
		   insertDate integer(11) not null,
		   deleteString varchar(6) not null
		)";
		sqlite_query($db, $sql);
		
		// config.inc.php erstellen		
		$configcontent = "
<?
define('DBFILE', '$dbname');
?>";
		
		$fh = fopen('inc/config.inc.php', 'w');
		fputs($fh, $configcontent);
		fclose($fh);		
		
		echo '<p><img src="inc/img/success.png" alt="" /> Datenbank wurde erstellt.</p>';
	} else {
		echo '<p><img src="inc/img/zeichen.png" alt="" /> Kann Datenbank nicht erstellen. Schreibrechte?</p>';
	}
	
	die();
	
} else if(file_exists('inc/config.inc.php')) {
	include 'inc/config.inc.php';  // Name der SQLite DB wird hier festgelegt
	include 'inc/sqlite.class.php';
	$db = new db; // Datenkbank oeffnen
} 

if(isset($_GET['d'])) {
	if(isset($_GET['c'])) {
		if(empty($_GET['d']) || empty($_GET['c'])) {
			print '<p><img src="inc/img/zeichen.png" alt=""/> Nicht gen&uuml;gend Parameter angegeben</p>';
		} elseif(!file_exists('./i/' .$_GET['d']) && !is_valid_filename($_GET['d'], $globvar['validimages'])) {
			echo '<p><img src="inc/img/zeichen.png" alt=""/> Datei existiert nicht oder ist unzul&auml;ssig.</p>';
		} else {
			$imageName = sqlite_escape_string($_GET['d']);
			$deleteString = sqlite_escape_string($_GET['c']);
			
			// pruefen, ob Bildname und Code in der Datenbank vorhanden sind
			$db->query("SELECT imageName, deleteString FROM img_images WHERE imageName = '" .$imageName. "' AND deleteString = '" .$deleteString. "'");
			
			if($db->numRows()) {
				$db->query("DELETE FROM img_images WHERE imageName = '" .$imageName. "' AND deleteString = '" .$deleteString. "'");
				unlink("./i/" .$imageName); // Bild loeschen
				unlink("./i/t/" .$imageName); // Thumbnail loeschen
				echo '<p><img src="inc/img/success.png" alt=""/> Grafik erfolgreich gel&ouml;scht.</p>';
			} else {
				echo '<p><img src="inc/img/zeichen.png" alt=""/> Code und Bildname passen nicht zusammen</p>';
			}
		}
	}
} else if(isset($_GET['s'])) {
	// Fragt ab, ob ueberhaupt der Bildname mitgeliefert wurde
	if(empty($_GET['s'])) {
		print '<p><img src="inc/img/zeichen.png" alt=""/> Kein Bild angegeben.</p>';
	// Prueft, ob das Bild existiert und ob der Bildname auch valide ist
	} elseif(!file_exists('./i/' .$_GET['s']) && !is_valid_filename($_GET['s'], $globvar['validimages'])) {
		echo '<p><img src="inc/img/zeichen.png" alt=""/> Datei existiert nicht oder ist unzul&auml;ssig.</p>';
	} else {
		$img = sqlite_escape_string($_GET['s']);
		$imgausgabe = './i/' .$img;
		
		// Clickanzahl des Bildes laden
		$db->query("SELECT rowid, imageName, numClicks FROM img_images WHERE imageName = '".$img."'");
				
		if($db->numRows()) {
			$db->fetch();
			$clicks = $db->row('numClicks'); // wird ausgegeben
			$rowid = $db->row('rowid');
			
			// Clickcounter um eins erhoehen
			$db->query("UPDATE 'img_images' SET 'numClicks' = numClicks+1 WHERE rowid = " .$rowid);
		
			include('tpl/einzel.tpl.php');		
		}
	}		
} else {
	if(isset($_POST['nsubmit'])) { 
		if($globvar['showformafterup']) 
			include('tpl/index.tpl.php');
		
		$submitted = true;
	} else {
		include('tpl/index.tpl.php');
		$submitted = false;	
	}		

	if($submitted) {
		$tempname = $_FILES['nfile']['tmp_name']; 
		$type = $_FILES['nfile']['type'];		
		$thename = $_FILES['nfile']['name'];
		
		$endung = substr($thename, -4);
		
		// Abfrage, ob Bildnamen nach dem Zufall gewaehlt werden sollen. Wenn nicht wird der eigentlich Bildname verwendet
		if($globvar['use_randomname'] == true)
			$name = rand_str(12, 'abcdefghijklmnopqrstuvwxyz0123456789').$endung; 
		else 
			$name = $thename;
		
		// Bildgroesse in kb umrechnen
		$size = $_FILES['nfile']['size']; 
		$size = round($size / 1024, 2);
		
		// Weitere pruefung, ob Bild auch wirklich ein Bild ist
		if(!in_array($endung, $globvar['validimages'])) {
			echo '<p><img src="./inc/img/zeichen.png" alt="" \> Invalides Bildformat</p>';
		// Abfrage, ob Bild  die maximale groesse ueberschritten hat
		} else if($size > $globvar['maxsize']) {
			echo '<p><img src="./inc/img/zeichen.png" alt=""/> Das Bild ist gr&ouml;&szlig;er als ' .$globvar['maxsize']. ' kb</p>';
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
				print '<p><img src="inc/img/zeichen.png" alt=""/> Dateityp wird nicht unterst&uuml;tzt.</p>';
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
			if(dirname($_SERVER['REQUEST_URI']) == "/")
				$serverurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
			else
				$serverurl = $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/";
			
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
			
			// Eintrag in der Datenbank hinzufügen
			$db->query("INSERT INTO 'img_images' ('imageName', 'numClicks', 'insertDate', 'deleteString') VALUES ('".sqlite_escape_string($name)."', '0', '".time()."', '".$deletestring."')");
			
			include('tpl/output.tpl.php');
		}
	}
}
include('tpl/footer.tpl.php');

// Funktion zum ueberpruefen, ob die Datei auch valide ist
function is_valid_filename($filename, $extensions) {
    $regex = '/^\w\.(' .implode('|', $extensions). ')$/';
    return preg_match($regex, $filename);
}

// Funktion fuer den API Call von is.gd
function isgd($link) {
        $fp = @fsockopen("www.is.gd", 80, $errno, $errstr, 30);
	if (!$fp) {
		echo '<p><img src="./inc/img/zeichen.png" alt=""/> Fehler beim erstellen des <a href="http://is.gd"><u>is.gd</u></a> Links. Nutze normalen Link.<br/></p>';
		return "error";
        } else {
	        $out = "GET /api.php?longurl=$link HTTP/1.1\r\n";
       		$out .= "Host: www.is.gd\r\n";
        	$out .= "Connection: Close\r\n\r\n";
        	fwrite($fp, $out);
		
		while (!feof($fp)) {
 	       		return substr(strstr(fread($fp, 300), 'http://'), 0, -5); // echt bescheiden geloest, aber es funktioniert :p
        	}
		fclose($fp);
	}
}

// Random Delete String erstellen - http://www.php.net/manual/de/function.rand.php#90773
function rand_str($length = 6, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
    $chars_length = (strlen($chars) - 1);
    $string = $chars{rand(0, $chars_length)};
   
    for ($i = 1; $i < $length; $i = strlen($string)) {
        $r = $chars{rand(0, $chars_length)};       
        if ($r != $string{$i - 1}) $string .=  $r;
    }
    return $string;
}

?>