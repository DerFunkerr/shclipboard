// Funktion zum Aktualisieren der Uhrzeit auf der Webseite
function updateClock() {
    // Holt das HTML-Element mit der ID "clock"
    const clockElement = document.getElementById("clock");
    // Erstellt ein neues Datum-Objekt, um die aktuelle Zeit zu erhalten
    const now = new Date();
    // Holt die aktuelle Stunde, Minute und Sekunde und stellt sicher, dass
   sie zweistellig sind
    const hours = now.getHours().toString().padStart(2, "0"); // Falls
   eine Zahl einstellig ist, wird eine Null davor gesetzt
    const minutes = now.getMinutes().toString().padStart(2, "0");
    const seconds = now.getSeconds().toString().padStart(2, "0");
    // Setzt den Text des Uhr-Elements auf die aktuelle Zeit
    clockElement.textContent = `${hours}:${minutes}:${seconds}`;
   }
   // Funktion zum Abrufen des Benutzernamens vom Server
   async function fetchUsername() {
    // Holt das HTML-Element mit der ID "username", in dem der Benutzername
   angezeigt wird
    const usernameField = document.getElementById("username");
    try {
    // Sendet eine POST-Anfrage an den Server, um den Benutzernamen
   abzurufen
    const response = await
   fetch('https://steelmountain.ddns.net/api/auth.php', {
    method: 'POST',
    headers: {
    'Content-Type': 'application/json',
    },
    body: JSON.stringify({
    action: 'fetchUsername' // Aktion, die auf dem Server
   ausgeführt wird
    }),
    });
    // Die Antwort vom Server wird als JSON interpretiert
    const data = await response.json();
    // Falls die Antwort erfolgreich ist, wird der Benutzername im HTMLElement angezeigt
    if (response.ok) {
    usernameField.textContent = data.username;
    } else {
    // Falls es ein Problem gibt, wird eine Fehlermeldung ausgegeben
    console.error('Fehler beim Abrufen des Benutzernamens:',
   data.error || 'Unbekannter Fehler');
    alert(`Fehler beim Abrufen des Benutzernamens: ${data.error ||
   'Unbekannter Fehler'}`);
    }
    } catch (error) {
    // Falls ein Netzwerkfehler auftritt, wird eine Fehlermeldung
   ausgegeben
    console.error('Ein Fehler ist beim Abrufen des Benutzernamens
   aufgetreten:', error);
    alert('Ein Netzwerkfehler ist aufgetreten. Bitte versuche es später
   erneut.');
    }
   }
   // Funktion zum Ausloggen des Benutzers
   async function logout() {
    try {
    // Sendet eine POST-Anfrage an den Server, um den Benutzer
   auszuloggen
    const response = await
   fetch('https://steelmountain.ddns.net/api/auth.php', {
    method: 'POST',
    headers: {
    'Content-Type': 'application/json',
    },
    body: JSON.stringify({
    action: 'logout' // Aktion für den Logout
    }),
    });
    // Antwort vom Server als JSON interpretieren
    const data = await response.json();
    // Falls die Abmeldung erfolgreich ist, wird eine Meldung angezeigt
   und der Benutzer weitergeleitet
    if (response.ok) {
    showInfo('Logout erfolgreich! Weiterleitung...');
    setTimeout(() => {
    window.location.href = '/login.php'; // Weiterleitung zur
   Login-Seite nach 2 Sekunden
    }, 2000);
    } else {
    // Falls ein Fehler auftritt, wird eine Fehlermeldung ausgegeben
    console.error('Fehler beim Logout:', data.error || 'Unbekannter
   Fehler');
    alert(`Fehler beim Logout: ${data.error || 'Unbekannter
   Fehler'}`);
    }
    } catch (error) {
    // Falls ein Netzwerkfehler auftritt, wird eine Fehlermeldung
   ausgegeben
    console.error('Ein Fehler ist beim Logout aufgetreten:', error);
    alert('Ein Netzwerkfehler ist aufgetreten. Bitte versuche es später
   erneut.');
    }
   }
   // Sobald die Webseite vollständig geladen ist, wird der folgende Code
   ausgeführt
   document.addEventListener('DOMContentLoaded', () => {
    updateClock(); // Uhrzeit sofort aktualisieren
    setInterval(updateClock, 1000); // Uhrzeit jede Sekunde aktualisieren
    fetchUsername(); // Benutzername abrufen und anzeigen
    // Holt den Logout-Button aus dem HTML
    const logoutButton = document.getElementById('logout');
    // Falls der Logout-Button existiert, wird ein Klick-Event hinzugefügt
    if (logoutButton) {
    logoutButton.addEventListener('click', async (event) => {
    event.preventDefault(); // Verhindert das Standardverhalten des
   Buttons (z. B. das Absenden eines Formulars)
    try {
    await logout(); // Führt die Logout-Funktion aus
    } catch (error) {
    console.error('Ein Fehler ist aufgetreten:', error);
    }
    });
    } else {
    console.error('Logout-Button wurde im DOM nicht gefunden.');
    }
   });