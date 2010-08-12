<?php

// Error - Gibt formatierte Error-Message aus
function error($msg) {
	print '<div class="error">' .$msg. '</div>';
}

// Success - Gibt formatierte Erfolgsmeldung aus
function success($msg, $redirect = 0, $page = './', $time = 3000) {
	if($redirect)
	print '	<script type="text/javascript">
			<!--
			setTimeout("self.location.href=\'' .$page. '\'",' .$time. ');
			//-->
			</script>';

	print '<div class="success">' .$msg. '</div>';
}

// getServer - Gibt den aktuellen Servernamen zurueck
function getServer() {
    if(dirname($_SERVER['REQUEST_URI']) == "/")
        return $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
    else
        return $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."/";
}

// Funktion für den API Call von is.gd
function isgd($link) {
        $fp = @fsockopen("www.is.gd", 80, $errno, $errstr, 30);
    if (!$fp) {
        error('Fehler beim erstellen des <a href="http://is.gd"><u>is.gd</u></a> Links. Nutze normalen Link.');
        return "error";
        } else {
            $out = "GET /api.php?longurl=$link HTTP/1.1\r\n";
               $out .= "Host: www.is.gd\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
        
        while (!feof($fp)) {
                    return substr(strstr(fread($fp, 300), 'http://'), 0, -5);
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

// Passwortverschluesselung
function encryptpass($password) {
	if(!empty($password)) {
		$key = substr(DBFILE, -20).'HB7nxFsrqfJVGqQIo4pkv4KHmDwIRdhqv8Izc2B7dYerMS7LnTYTWbLgtIwao0WEzqYje2';	
		$hash1 = sha1(md5($key));
		$hash2 = sha1(md5($password));
		$password = md5(sha1($hash1 . $hash2));
		
		return $password;
	}
}

// schreibt Mail an neuen User
function writemail($email, $username, $pass, $title) {
	$servername = $_SERVER['SERVER_NAME'];
	$headers = 'Content-type: text/plain; charset=UTF-8' . "\n".
	'From: info@'.$servername."\r\n".
    'X-Mailer: PHP/' . phpversion();
	
	$text = "Hallo $username,
	
du hast dich erfolgreich bei " .$title. " registriert. Hier sind deine Logindaten.
	
   Username: $username
   Passwort: $pass
		
Das Passwort wird logischerweise verschlüsselt in der Datenbank abgelegt.";
	
	mail($email, "Willkommen bei ".$title, $text, $headers);
}

// Prueft ob benutzer bereits eingeloggt ist
function islogged() {
	if(isset($_COOKIE['img_username']) && isset($_COOKIE['img_password']) && isset($_COOKIE['img_userid'])) {
		$db = new db;		
	
		$username = sqlite_escape_string($_COOKIE['img_username']);
		$pass = sqlite_escape_string($_COOKIE['img_password']);
		$userID = (int) $_COOKIE['img_userid'];

		$db->query("SELECT * FROM 'img_users' WHERE userName = '" .$username. "' AND userPass = '" .$pass. "' AND userID = " .$userID);
		
		if($db->numRows())
			return true;
	}
}

function genName($useRand, $endung, $thename) {
    if($useRand == true)
        return rand_str(12, 'abcdefghijklmnopqrstuvwxyz0123456789').$endung; 
    else 
        return $thename;
		
	
}

?>

