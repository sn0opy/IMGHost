<?php

if(!file_exists('inc/config.inc.php')) {
    $dbname = rand_str(20);

    if ($db = sqlite_open('inc/'.$dbname, 0666)) { 
        // SQLite DB erstellen
        $sql = "
        CREATE TABLE 'img_users' (
		   userID integer primary key,
		   userName varchar(15) not null,
		   userMail varchar(30) not null,
		   regDate integer(11) not null,
		   userPass varchar(32) not null
		);

		CREATE TABLE 'img_images' (
		   imageID integert primary key,
		   imageName varchar(30) not null,
		   numClicks integer(11) null,
		   insertDate integer(11) not null,
		   deleteString varchar(6) not null,
		   uploadedBy integer(11) null
		);";
        sqlite_query($db, $sql);
        
        // config.inc.php erstellen        
        $confcontent = '<?php
define("DBFILE", "inc/'.$dbname.'");
?>';
        
        $fh = fopen('inc/config.inc.php', 'w');
        fputs($fh, $confcontent);
        fclose($fh);        
        
        success('Datenbank wurde erstellt.', 1);
    } else {
        error('Kann Datenbank nicht erstellen. Schreibrechte?');
    }    
    die();
    
} elseif(file_exists('inc/config.inc.php')) {
    include 'inc/config.inc.php';  // Name der SQLite DB wird hier festgelegt
	include 'inc/sqlite.class.php';
	
    if(file_exists(DBFILE)) {
        $db = new db; // Datenkbank öffnen
    } else {
        error('Datenbank existiert nicht. Bitte Config löschen und Seite neu laden');
		die();
    }
} 

?>