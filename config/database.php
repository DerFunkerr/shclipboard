<?php
/**
* Stellt eine Verbindung zur PostgreSQL-Datenbank her.
*
* @return PDO Eine neue PDO-Instanz für die Verbindung zur Datenbank.
*/
function getConnection() {
$host = "192.168.0.121"; // VNet Container IP
$port = "5432";
// Folgende drei Variablen wurden bei der Erstellung des Containers definiert. Siehe Blatt: "2 - Setup Projekt Container Stack"
$dbname = "shclipboard";
$username = "admin";
$password = "Mopo18871312!";
 // Definiert die Verbindungszeichenfolge (DSN) für PostgreSQL
 $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
 // Erstellt eine neue PDO-Verbindung mit Fehlerbehandlung
 return new PDO($dsn, $username, $password, [
 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Fehler sollen als Exception geworfen werden
 ]);
}
/**
* Holt den Benutzernamen anhand der Benutzer-ID (uid).
*
* @param int $uid Die eindeutige Benutzer-ID.
* @return array|null Gibt ein assoziatives Array mit dem Benutzernamen zurück oder null, falls nicht gefunden.
*/
function getUsernameFromUid($uid) {
 $db = getConnection();

 // Vorbereitung der SQL-Abfrage zum Abrufen des Benutzernamens anhand der Benutzer-ID
 $stmt = $db->prepare("SELECT username FROM users WHERE uid = :uid");
 $stmt->execute(['uid' => $uid]);
 // Gibt den gefundenen Benutzernamen zurück (oder null, falls nicht vorhanden)
 return $stmt->fetch(PDO::FETCH_ASSOC);
}
/**
* Ruft Benutzerdetails basierend auf dem Benutzernamen ab.
*
* @param string $username Der Benutzername.
* @return array|null Gibt ein assoziatives Array mit uId und Passwort zurück oder null, falls nicht gefunden.
*/
function fetchUserDetails($username) {
 $db = getConnection();
 // Vorbereitung der SQL-Abfrage zum Abrufen der Benutzer-ID und des Passworts
 $stmt = $db->prepare("SELECT uId, password FROM users WHERE username = :username");
 $stmt->execute(['username' => $username]);
 return $stmt->fetch(PDO::FETCH_ASSOC);
}
/**
* Erstellt einen neuen Clip für einen Benutzer.
*
* @param int $uId Die Benutzer-ID.
* @param string $name Der Name des Clips.
* @param string $content Der Inhalt des Clips.
* @return bool Gibt true zurück, falls das Einfügen erfolgreich war, andernfalls false.
*/
function createClip($uId, $name, $content) {
 $db = getConnection();
 // Vorbereitung der SQL-Abfrage zum Einfügen eines neuen Clips
 $stmt = $db->prepare("INSERT INTO clips (uId, name, content) VALUES (:uid, :name, :content)");

 return $stmt->execute(['uid' => $uId, 'name' => $name, 'content' => $content]);
}
/**
* Aktualisiert den Inhalt eines vorhandenen Clips.
*
* @param int $uId Die Benutzer-ID.
* @param string $name Der Name des Clips.
* @param string $content Der neue Inhalt des Clips.
* @return bool Gibt true zurück, falls das Update erfolgreich war, andernfalls false.
*/
function updateClip($uId, $name, $content) {
 $db = getConnection();
 // Aktualisiert den Clip-Inhalt und setzt das Aktualisierungsdatum
 $stmt = $db->prepare("UPDATE clips SET content = :content, updated = CURRENT_TIMESTAMP WHERE uId = :uid AND name = :name");

 return $stmt->execute(['uid' => $uId, 'name' => $name, 'content' => $content]);
}
/**
* Löscht einen Clip eines Benutzers.
*
* @param int $uId Die Benutzer-ID.
* @param string $name Der Name des Clips.
* @return bool Gibt true zurück, falls das Löschen erfolgreich war, andernfalls false.
*/
function deleteClip($uId, $name) {
 $db = getConnection();
 // Löscht den Clip anhand der Benutzer-ID und des Clip-Namens
 $stmt = $db->prepare("DELETE FROM clips WHERE name = :name AND uId = :uid");

 return $stmt->execute(['uid' => $uId, 'name' => $name]);
}
/**
* Holt den Inhalt eines spezifischen Clips eines Benutzers.
*
* @param int $uId Die Benutzer-ID.
* @param string $name Der Name des Clips.
* @return array|null Gibt ein assoziatives Array mit dem Clip-Inhalt zurück oder null, falls nicht gefunden.
*/
function fetchClip($uId, $name) {
 $db = getConnection();
 // Abfrage zum Abrufen des Clip-Inhalts anhand der Benutzer-ID und des Clip-Namens
 $stmt = $db->prepare("SELECT content FROM clips WHERE uId = :uid AND name = :name");
 $stmt->execute(['uid' => $uId, 'name' => $name]);
 return $stmt->fetch(PDO::FETCH_ASSOC);
}
/**
* Ruft eine Liste aller Clips eines Benutzers ab.
*
* @param int $uId Die Benutzer-ID.
* @return array Gibt ein assoziatives Array mit den Clip-Namen zurück.
*/
function fetchClips($uId) {
 $db = getConnection();
 // Holt alle Clip-Namen eines Benutzers, sortiert nach Aktualisierungsdatum (neueste zuerst)
 $stmt = $db->prepare("SELECT name FROM clips WHERE uId = :uid ORDER BY updated DESC");
 $stmt->execute(['uid' => $uId]);
 return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>