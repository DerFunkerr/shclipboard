<?php
// CORS (Cross-Origin Resource Sharing) Header erlauben Anfragen von einer bestimmten Domain
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Erlaubt nur POST und OPTIONS Anfragen
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Definiert erlaubte Header

// OPTIONS Preflight-Anfragen verarbeiten (für CORS-Anfragen nötig)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
 http_response_code(204); // "No Content" Antwort für Preflight-Anfragen
 exit();
}
// Verhindert GET-Anfragen, nur POST-Anfragen sind erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
 http_response_code(403); // "Forbidden"
 echo "403 Forbidden";
 exit();
}
// Datenbank- und Redis-Verbindungsdateien einbinden
require '../config/database.php';
require '../config/redis.php';
// Hauptfunktion zum Verarbeiten von POST-Anfragen
handlePost();
/**
* Sendet eine JSON-Antwort mit einem optionalen Statuscode
*
* @param array $data Die zu sendenden Daten
* @param int $statusCode HTTP-Statuscode (Standard: 200)
*/
function sendResponse($data, $statusCode = 200) {
 http_response_code($statusCode);
 header('Content-Type: application/json'); // Antwort als JSON
 echo json_encode($data);
 exit();
}
/**
* Verarbeitet eingehende POST-Anfragen und ruft die entsprechende Funktion auf.
*/
function handlePost() {
 // JSON-Daten aus dem Anfrage-Body lesen
 $request = json_decode(file_get_contents('php://input'), true);
 // Überprüfen, ob die Anfrage gültig ist und eine Aktion enthält
 if (!$request || !isset($request['action'])) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 // Entscheidung, welche Aktion ausgeführt wird
 switch ($request['action']) {
 case 'login':
 handleLogin($request);
 break;
 case 'logout':
 handleLogout();
 break;
 case 'fetchUsername':
 fetchUsername($_COOKIE['sessionToken']);
 break;
 default:
 sendResponse(['error' => 'Unknown action'], 400);
 }
}
/**
* Behandelt den Login-Prozess.
*
* @param array $request Die übermittelten Logindaten (Benutzername & Passwort)
*/
function handleLogin($request) {
 // Überprüfen, ob Benutzername und Passwort übermittelt wurden
 if (!isset($request['username'], $request['password'])) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 // Benutzerinformationen aus der Datenbank abrufen
 $userDetails = fetchUserDetails($request['username']);

 // Überprüfen, ob der Benutzer existiert und das Passwort korrekt ist
 if (!$userDetails || hash('sha512', $request['password']) !==
$userDetails['password']) {
 sendResponse(['error' => 'Invalid username or password'], 401);
 }
 // Generieren eines zufälligen Session-Tokens
 $sessionToken = bin2hex(random_bytes(32));

 // Speichern des Session-Tokens in Redis
 storeSessionToken($sessionToken, $userDetails['uid']);

 // Session-Cookie setzen
 initiateSession($sessionToken);
 sendResponse(['message' => 'Login successful'], 200);
}
/**
* Behandelt den Logout-Prozess.
*/
function handleLogout() {
 // Überprüfen, ob ein Session-Cookie existiert
 if (!isset($_COOKIE['sessionToken'])) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 // Löscht das Session-Token aus Redis
 deleteSessionToken($_COOKIE['sessionToken']);
 sendResponse(['message' => 'Logout successful'], 200);
}

/**
* Setzt ein sicheres Session-Cookie für den Benutzer.
*
* @param string $sessionToken Das generierte Session-Token
*/
function initiateSession($sessionToken) {
 setcookie('sessionToken', $sessionToken, [
 'expires' => time() + 86400, // Cookie läuft nach 24 Stunden ab
 'path' => '/', // Gilt für die gesamte Domain
 // 'domain' => 'domain.com', // Erlaubt Cookie-Zugriff nur von dieser Domain
 'httponly' => true, // Verhindert Zugriff durch JavaScript (Schutz gegen XSS)
 'samesite' => 'Strict', // Schutz vor CSRF (keine Cookie-Übertragung bei Cross-Site-Anfragen)
 ]);
}
/**
* Holt den Benutzernamen basierend auf dem Session-Token.
*
* @param string $sessionToken Das Session-Token des Benutzers
*/
function fetchUsername($sessionToken) {
 // Überprüft, ob das Session-Token gültig ist
 if (!validateSessionToken($sessionToken)) {
 sendResponse(['message' => 'Invalid token'], 200);
 }
 // Holt die Benutzer-ID aus Redis
 $uid = getUserIdFromSessionToken($sessionToken);
 // Holt den Benutzernamen anhand der Benutzer-ID
 sendResponse(getUsernameFromUid($uid), 200);
}
?>