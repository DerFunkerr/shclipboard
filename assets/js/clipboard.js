// Sobald die Webseite vollständig geladen ist, wird die folgende Funktion
ausgeführt
document.addEventListener('DOMContentLoaded', async () => {
 // Das Button-Element mit der ID "create" wird geholt
 const createButton = document.getElementById('create');
 // Alle gespeicherten Clips werden geladen und angezeigt
 await fetchClips();
 // Falls der "Create"-Button existiert, wird ein Klick-Event hinzugefügt
 if (createButton) {
 createButton.addEventListener('click', async (event) => {
 event.preventDefault(); // Verhindert die Standardaktion des
Buttons (z. B. Form-Absenden)
 // Holt den Clip-Namen und den Inhalt aus den Eingabefeldern
 const clipName = document.getElementById('cname').value.trim();
 const clipContent = document.getElementById('clip-inputnew').value.trim();
 try {
 // Wenn das Eingabefeld die Klasse "editing" hat, wird ein
bestehender Clip aktualisiert,
 // ansonsten wird ein neuer Clip erstellt
 document.getElementById('clip-inputnew').classList.contains('editing')
 ? await updateClip(clipName, clipContent)
 : await createClip(clipName, clipContent);
 } catch (error) {
 console.error('Ein Fehler ist aufgetreten:', error);
 }
 });
 } else {
 console.error('Der "Create"-Button wurde im DOM nicht gefunden.');
 }
});
// Funktion zum Erstellen eines neuen Clips
async function createClip(name, content) {
 // Überprüfung, ob der Inhalt nicht leer ist
 if(content.trim().length === 0) {
 showInfo('Clip-Inhalt darf nicht leer sein!', 'warning');
 return;
 }
 // Überprüfung, ob der Name nicht leer ist
 if(name.trim().length === 0) {
 showInfo('Clip-Name darf nicht leer sein!', 'warning');
 return;
 }
 // Überprüfung, ob der Clip-Name bereits existiert
 if(Array.from(document.getElementsByClassName('clip'))
 .some(el => el.innerText === name)) {
 showInfo('Clip existiert bereits!', 'warning');
 return;
 }
 try {
 // Senden einer Anfrage an den Server, um einen neuen Clip zu
erstellen
 const response = await
fetch('https://steelmountain.ddns.net/api/clipboard.php', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 },
 body: JSON.stringify({
 name: name,
 content: btoa(content), // Der Inhalt wird in Base64 kodiert
 action: 'create'
 }),
 });
 const data = await response.json();
 if (response.ok && data.status) {
 showInfo('Clip erfolgreich erstellt!');
 await fetchClips(); // Aktualisiert die Liste der Clips
 } else {
 showInfo('Clip-Erstellung fehlgeschlagen!', 'error');
 console.error('Fehler bei der Clip-Erstellung:', data.error ||
'Unbekannter Fehler');
 }
 } catch (error) {
 console.error('Fehler beim Erstellen des Clips:', error);
 alert('Ein Netzwerkfehler ist aufgetreten. Bitte versuche es später
erneut.');
 }
}
// Funktion zum Abrufen aller Clips vom Server
async function fetchClips() {
 try {
 const response = await
fetch('https://steelmountain.ddns.net/api/clipboard.php', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 },
 body: JSON.stringify({ action: 'fetchall' }),
 });
 const data = await response.json();
 if(data.clipcount == 0) {
 // Falls keine Clips vorhanden sind, Eingabefelder leeren
 document.getElementById('cname').value = "";
 document.getElementById('clip-input-new').value = "";
 document.getElementById('callclips').innerHTML = "";
 exitEditingMode();
 exitSelectedMode();
 return;
 }
 if (response.ok && data.clips) {
 // Vorhandene Clips im UI anzeigen
 document.getElementById('cname').value = "";
 document.getElementById('clip-input-new').value = "";
 document.getElementById('callclips').innerHTML = "";
 const clipsContainer = document.getElementById('callclips');
 data.clips.forEach(clip => {
 var newClip = document.createElement('div');
 newClip.className = "clip";
 newClip.innerText = clip.name;
 clipsContainer.appendChild(newClip);
 });
 setClipPreviewEventListener();
 } else {
 console.error('Fehler beim Abrufen der Clips:', data.error ||
'Unbekannter Fehler');
 }
 } catch (error) {
 console.error('Fehler beim Abrufen der Clips:', error);
 alert('Ein Netzwerkfehler ist aufgetreten. Bitte versuche es später
erneut.');
 }
}
// Funktion zum Abrufen eines einzelnen Clips
async function fetchClip(name) {
 try {
 const response = await
fetch('https://steelmountain.ddns.net/api/clipboard.php', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 },
 body: JSON.stringify({ name: name, action: 'fetch' }),
 });
 const data = await response.json();
 if(data.clipcount == 0) return;
 if (response.ok && data.content) {
 // Entschlüsselung des Base64-codierten Inhalts
 document.getElementById('clip-input-new').value =
atob(data.content);
 } else {
 console.error('Fehler beim Abrufen des Clips:', data.error ||
'Unbekannter Fehler');
 }
 } catch (error) {
 console.error('Fehler beim Abrufen des Clips:', error);
 alert('Ein Netzwerkfehler ist aufgetreten. Bitte versuche es später
erneut.');
Nachdem wir nun alle Dateien erstellt haben und den assets/img/ Ordner erstellt haben und
die Bilder aus dem Teams-Ordner kopiert haben, müsste unsere finale Ordnerstruktur so
 }
}
// Funktion zum Kopieren des Clip-Inhalts in die Zwischenablage
function copyClip() {
 const element = document.getElementById('clip-input-new');
 if (element) {
 element.select();
 element.setSelectionRange(0, 99999); // Markiert den gesamten Text
 navigator.clipboard.writeText(element.value); // Kopiert den Text in
die Zwischenablage
 }
 showInfo('Clip-Inhalt wurde kopiert!');
}
// Funktion zum Anzeigen von Benachrichtigungen
function showInfo(infoText, level) {
 const infoBox = document.getElementById('infobox');
 infoBox.innerText = infoText;
 infoBox.classList.remove('hidden');
 infoBox.classList.add('visible');
 // Farbe der Benachrichtigung basierend auf der Meldungsstufe ändern
 switch(level) {
 case 'success':
 infoBox.style.color = 'lightgreen';
 break;
 case 'warning':
 infoBox.style.color = 'orange';
 break;
 case 'error':
 infoBox.style.color = 'red';
 break;
 default:
 infoBox.style.color = 'lightgreen';
 }
 // Die Meldung nach 5 Sekunden ausblenden
 setTimeout(() => {
 infoBox.classList.remove('visible');
 infoBox.classList.add('hidden');
 }, 5000);
}