<?php
// Lädt die Konfigurationsdatei für Redis (eine schnelle In-MemoryDatenbank, die oft für Sitzungen genutzt wird)
require 'config/redis.php';
// Prüft, ob der Benutzer bereits eingeloggt ist
if(isset($_COOKIE['sessionToken']) &&
validateSessionToken($_COOKIE['sessionToken'])) {
 // Falls das Sitzungstoken existiert und gültig ist, wird der Benutzer
zur Hauptseite weitergeleitet
 header("Location: /");
 exit(); // Beendet die Ausführung des Codes, um sicherzustellen, dass
keine weiteren Befehle ausgeführt werden
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initialscale=1.0">

 <!-- Favicon (kleines Symbol in der Browser-Registerkarte) -->
 <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">

 <!-- CSS-Stylesheet für die Login-Seite -->
 <link rel="stylesheet" href="assets/css/login.css">

 <!-- Einbindung einer speziellen Schriftart von Google Fonts -->
 <link href='https://fonts.googleapis.com/css?family=JetBrains Mono'
rel='stylesheet'>
 <title>Self Hosted Clipboard - Login</title> <!-- Titel der Seite --
>
</head>
<body>
 <div id="cmain"> <!-- Hauptcontainer für das Login-Formular -->

 <!-- Formular für die Anmeldung -->
 <form id="login-form">

 <!-- Eingabefeld für den Benutzernamen -->
 <input type="text" name="username" id="username"
placeholder="Username" required>

 <!-- Eingabefeld für das Passwort -->
 <input type="password" name="password" id="password"
placeholder="Password" required>

 <!-- Login-Button, um die Anmeldung abzusenden -->
 <button type="submit">Login</button>

 </form>
 </div>
 <!-- Bereich für Benachrichtigungen oder Fehleranzeigen -->
 <div id="infobox"></div>
</body>
<!-- Lädt die JavaScript-Datei, die sich um die Login-Funktionalität
kümmert -->
<script src="assets/js/login.js"></script>
</html>