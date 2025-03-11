<?php
// Erstellt eine neue Redis-Verbindung
$redis = new Redis();
$host = ""; // VNet Container IP
$port = 6379;
// Verbindet sich mit dem Redis-Server unter der angegebenen IP-Adresse und
dem Port 6379
$redis->connect($host, $port);
/**
* Holt die Benutzer-ID (uId) anhand eines Session-Tokens aus Redis.
*
* @param string $sessionToken Das Session-Token des Benutzers.
* @return string|null Gibt die Benutzer-ID zurück oder null, falls nicht
gefunden.
*/
function getUserIdFromSessionToken($sessionToken) {
 global $redis; // Verwendet die globale Redis-Verbindung
 // Ruft die Benutzer-ID basierend auf dem gespeicherten Session-Token
aus Redis ab
 return $redis->get("session:$sessionToken"); // Struktur: session:
<sessionToken> -> <userId>
}
/**
* Überprüft, ob ein Session-Token in Redis existiert und gültig ist.
*
* @param string $sessionToken Das Session-Token, das überprüft werden soll.
* @return bool Gibt `true` zurück, wenn das Token gültig ist, sonst
`false`.
*/
function validateSessionToken($sessionToken) {
 global $redis; // Verwendet die globale Redis-Verbindung
 if (isset($sessionToken)) {
 // Prüft, ob das Session-Token in Redis existiert
 if ($redis->exists("session:$sessionToken")) {
 return true; // Token ist gültig
 }
 }
 return false; // Token ist ungültig oder existiert nicht
}
/**
* Speichert ein Session-Token in Redis und verknüpft es mit einer BenutzerID.
*
* @param string $sessionToken Das zu speichernde Session-Token.
* @param int $uid Die Benutzer-ID, die mit dem Token verknüpft wird.
*/
function storeSessionToken($sessionToken, $uid) {
 global $redis; // Verwendet die globale Redis-Verbindung
 // Speichert die Benutzer-ID unter dem Schlüssel "session:
<sessionToken>"
 $redis->set("session:$sessionToken", $uid);
 // Setzt eine Ablaufzeit von 86400 Sekunden (24 Stunden), nach der das
Token automatisch gelöscht wird
 $redis->expire("session:$sessionToken", 86400);
}
/**
* Löscht ein Session-Token aus Redis.
*
* @param string $sessionToken Das zu löschende Session-Token.
*/
function deleteSessionToken($sessionToken) {
 global $redis; // Verwendet die globale Redis-Verbindung
 // Entfernt das gespeicherte Session-Token aus Redis
 $redis->del("session:$sessionToken");
}
?>