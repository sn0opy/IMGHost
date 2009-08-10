<?

/*

Da die Datenbank von jedem Problemlos heruntergeladen werden kann, empfehle 
folgende Methoden, sich davor zu schuetzen:

- .htaccess Datei im selben Ordner der Datenbank und folgendem Inhalt:

	<Files imghostdb>
	deny from all
	</Files>
	
Der Name muss natuerlich dem entsprechen, der unten angegeben ist.

- Ordner mit index.html darin. Hier sollte dazu noch eine Datenbank mit einem langem
Namen angegeben werden. Damit niemand durch ausprobieren auf den richtigen Link kommt.

- Datenbankfile in einen Ordner ausserhalb des eigentlichen html-Ordners platzieren,
sodass sowieso nur Script auf dem Server selbst drauf Zugriff haben.

*/

define("DBFILE", "inc/imghostdb");

?>