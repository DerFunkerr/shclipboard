<?php
// Erlaubt Anfragen nur von der spezifischen Domain
header("Access-Control-Allow-Origin: http://localhost");
// Erlaubt nur POST- und OPTIONS-Anfragen
header("Access-Control-Allow-Methods: POST, OPTIONS");
// Erlaubt bestimmte Header
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Bearbeitet Preflight OPTIONS-Anfragen (für CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
 http_response_code(204); // Kein Inhalt (No Content)
 exit();
}
// Einbinden der Konfigurationsdateien für die Datenbank und Redis
require '../config/database.php';
require '../config/redis.php';
// Startet die Verarbeitung von POST-Anfragen
handlePost();
/**
* Sendet eine JSON-Antwort mit dem angegebenen Statuscode
*/
function sendResponse($data, $statusCode = 200) {
 http_response_code($statusCode);
 header('Content-Type: application/json');
 echo json_encode($data);
 exit();
}
/**
* Hauptfunktion zur Verarbeitung von POST-Anfragen
*/
function handlePost() {
 // Liest die JSON-Daten aus dem Anfrage-Body
 $request = json_decode(file_get_contents('php://input'), true);
 // Überprüft, ob die Anfrage gültig ist (enthält 'action' und 'sessionToken')
 if (!$request || !isset($request['action']) ||
!isset($_COOKIE['sessionToken'])) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 $sessionToken = $_COOKIE['sessionToken'];
 // Überprüfung, ob der Session-Token gültig ist
 if(!validateSessionToken($sessionToken)) {
 sendResponse(['message' => 'Invalid token'], 200);
 }
 // Benutzer-ID aus dem Session-Token abrufen
 $uId = getUserIdFromSessionToken($sessionToken);
 // Verarbeitung der Aktion basierend auf der Anfrage
 switch ($request['action']) {
 case 'create':
 handleCreate($uId, $request['name'], $request['content']);
 break;
 case 'update':
 handleUpdate($uId, $request['name'], $request['content']);
 break;
 case 'delete':
 handleDelete($uId, $request['name']);
 break;
 case 'fetch':
 handleFetch($uId, $request['name']);
 break;
 case 'fetchall':
 handleFetchAll($uId);
 break;
 default:
 sendResponse(['error' => 'Unknown action'], 400);
 }
}
/**
* Erstellt einen neuen Clip
*/
function handleCreate($uId, $name, $content) {
 // Überprüfung auf gültige Parameter
 if (!isset($name, $content)) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 try {
 // Clip wird erstellt und Status zurückgegeben
 sendResponse(['status' => createClip($uId, $name, $content)], 200);
 } catch(Exception $e) {
 // Fehlerbehandlung
 sendResponse(['error' => 'Message: '.$e->getMessage()]);
 }
};
/**
* Aktualisiert einen bestehenden Clip
*/
function handleUpdate($uId, $name, $content) {
 // Überprüfung auf gültige Parameter
 if (!isset($name, $content)) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 try {
 // Clip wird aktualisiert und Status zurückgegeben
 sendResponse(['status' => updateClip($uId, $name, $content)], 200);
 } catch(Exception $e) {
 // Fehlerbehandlung
 sendResponse(['error' => 'Message: '.$e->getMessage()]);
 }
};
/**
* Löscht einen vorhandenen Clip
*/
function handleDelete($uId, $name) {
 // Überprüfung auf gültige Parameter
 if (!isset($uId, $name)) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 try {
 // Clip wird gelöscht und Status zurückgegeben
 sendResponse(['status' => deleteClip($uId, $name)], 200);
 } catch(Exception $e) {
 // Fehlerbehandlung
 sendResponse(['error' => 'Message: '.$e->getMessage()]);
 }
};
/**
* Ruft einen bestimmten Clip ab
*/
function handleFetch($uId, $name) {
 // Überprüfung auf gültige Parameter
 if (!isset($uId) || !isset($name)) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 try {
 // Clip-Daten abrufen
 $data = fetchClip($uId, $name);
 // Falls Daten gefunden wurden, zurücksenden
 if (!empty($data)) {
 sendResponse($data, 200);
 } else {
 sendResponse(['clipcount' => 0], 200);
 }
 } catch (Exception $e) {
 // Fehlerbehandlung
 sendResponse(['error' => 'Message: ' . $e->getMessage()], 500);
 }
};
/**
* Ruft alle Clips eines Benutzers ab
*/
function handleFetchAll($uId) {
 // Überprüfung auf gültige Parameter
 if (!isset($uId)) {
 sendResponse(['error' => 'Invalid request'], 400);
 }
 try {
 // Alle Clips des Benutzers abrufen
 $data = fetchClips($uId);
 // Falls Daten gefunden wurden, zurücksenden
 if (!empty($data)) {
 sendResponse(['clips' => $data], 200);
 } else {
 sendResponse(['clipcount' => 0], 200);
 }
 } catch (Exception $e) {
 // Fehlerbehandlung
 sendResponse(['error' => 'Message: ' . $e->getMessage()], 500);
 }
}
?>