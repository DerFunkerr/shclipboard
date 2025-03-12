<?php
// Lädt die Konfigurationsdateien für Redis und die Datenbank
require 'config/redis.php';
require 'config/database.php';
// Prüft, ob ein Sitzungstoken als Cookie vorhanden ist und ob es gültig ist
if (!isset($_COOKIE['sessionToken']) ||
!validateSessionToken($_COOKIE['sessionToken'])) {
 // Falls kein gültiges Token vorhanden ist, wird der Benutzer zur LoginSeite weitergeleitet
 header("Location: login.php");
 exit(); // Beendet die weitere Ausführung des Codes
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initialscale=1.0">

 <!-- Favicon (kleines Symbol in der Browser-Registerkarte) -->
 <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">

 <!-- CSS-Stylesheet für das Layout der Seite -->
 <link rel="stylesheet" href="assets/css/index.css">

 <!-- Einbindung einer Schriftart von Google Fonts -->
 <link href='https://fonts.googleapis.com/css?family=JetBrains Mono'
rel='stylesheet'>
 <title>Self Hosted Clipboard</title>
</head>
<body>
 <div id="cmain"> <!-- Hauptcontainer -->

 <!-- Obere Leiste mit Benutzername, Uhrzeit und Logout-Button -->
 <div id="cutils">
 <div>
 <button class="util" id="username" disabled>-</button> <!--
Benutzername (noch nicht gesetzt) -->
 <button class="util" id="clock" disabled>-</button> <!--
Uhrzeit (wird durch JavaScript aktualisiert) -->
 </div>
 <button class="util" id="logout" type="button">Logout</button>
<!-- Logout-Button -->
 </div>
 <!-- Bereich zum Erstellen eines neuen Clips -->
 <div id="cnewclip">
 <h1>New Clip</h1>
 <div>
 <!-- Eingabefeld für einen neuen Clip -->
 <textarea type="text" name="Clipboard Input" id="clip-inputnew" placeholder="Paste your text here..."></textarea>

 <!-- Dialog zum Speichern und Bearbeiten des Clips -->
 <div id="clipcdialog">
 <form>
 <!-- Eingabefeld für den Namen des Clips (nur
Buchstaben und Zahlen erlaubt) -->
 <input type="text" id="cname" name="alphanumericinput"
 pattern="[A-Za-z0-9]+" title="Only letters and
numbers are allowed." placeholder="Clip name" required>

<!-- Button zum Erstellen eines Clips -->
<button class="util" id="create"
type="submit">Create Clip</button>
 </form>

 <!-- Aktionen für den Clip (Kopieren, Bearbeiten,
Löschen) -->
 <div>
 <div class="util action" id="copy"
style="background:#393d50;" onclick="copyClip()">
 <img src="assets/img/copy.png">
 </div>
<div class="util action" id="edit"
style="background:#393d50;" onclick="editClip()">
 <img src="assets/img/edit.png">
 </div>
<div class="util action" id="trash"
style="background:#393d50;" onclick="deleteClip()">
 <img src="assets/img/trash.png">
 </div>
 </div>
 </div>
 </div>
 </div>
 <!-- Trennlinie -->
 <div class="divider"></div>
 <!-- Bereich mit gespeicherten Clips -->
 <div id="cstoredclips">
 <h1>Clips</h1>
 <div id="callclips"></div> <!-- Hier werden gespeicherte Clips
angezeigt -->
 </div>
 </div>
 <!-- Info-Box für Benachrichtigungen oder Statusmeldungen -->
 <div id="infobox"></div>
</body>
<!-- Einbindung der JavaScript-Dateien für die Funktionalität -->
<script src="assets/js/index.js"></script>
1.2 login.php
Die login.php besteht aus zwei Hauptteilen, genau wie index.php.
Im PHP-Teil passiert genau das Gleiche, nur dass hier die Weiterleitung vertauscht ist. Wenn
der Benutzer einen validen Session-Token vorweist, wird er auf die Homepage (index.php)
weitergeleitet.
Der HTML-Teil stellt die Benutzeroberfläche für die Anmeldung dar. Sie enthält ein zentrales
Formular mit zwei Eingabefeldern eines für den Benutzernamen und eines für das Passwort
beide Felder sind als erforderlich gekennzeichnet, damit der Benutzer keine leeren Werte
absenden kann, unter den Eingabefeldern befindet sich ein Login-Button, mit dem der Benutzer
die Anmeldung absendet.
Am Ende des HTML-Dokuments wird eine JavaScript-Datei eingebunden, die für die interaktive
Funktionalität des Login-Formulars dient. Sie verarbeitet die Benutzereingaben und sendet eine
Anfrage an den Server, um die Anmeldeinformationen zu überprüfen.
WICHTIG: Der PHP Code muss VOR den HTML Code wie bei index.php auch.
<script src="assets/js/clipboard.js"></script>
</html>