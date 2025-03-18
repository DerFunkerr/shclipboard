// Funktion zum Einloggen eines Benutzers
async function login(username, password) {
    try {
    // Senden einer POST-Anfrage an den Server zur Authentifizierung
    const response = await fetch('http://192.168.0.121/api/auth.php', {
    method: 'POST',
    headers: {
    'Content-Type': 'application/json',
    },
    body: JSON.stringify({
    username: username, // Benutzername aus dem Eingabefeld
    password: password, // Passwort aus dem Eingabefeld
    action: 'login' // Gibt an, dass es sich um eine Login-Anfrage handelt
    }),
    });
    // Die Antwort vom Server wird als JSON interpretiert
    const data = await response.json();
    // Falls die Antwort erfolgreich ist (HTTP-Status 200-299)
    if (response.ok) {
    showInfo('Login erfolgreich! Weiterleitung...', 'success');
    setTimeout(() => {
    window.location.href = '/'; // Nach erfolgreichem Login auf die Startseite weiterleiten
    }, 2000);
    } else {
    // Falls der Benutzername oder das Passwort falsch ist
    showInfo('Ungültiger Benutzername oder Passwort!', 'error');
    console.error('Fehler beim Login:', data.error || 'Unbekannter Fehler');
    }
    } catch (error) {
    // Falls ein Netzwerkfehler auftritt
    console.error('Ein Fehler ist beim Login aufgetreten:', error);
    alert('Ein Netzwerkfehler ist aufgetreten. Bitte versuche es später erneut.');
    }
   }
   // Sobald die Webseite vollständig geladen ist, wird dieser Code ausgeführt
   document.addEventListener('DOMContentLoaded', () => {
    // Holt das Login-Formular aus dem HTML
    const loginForm = document.getElementById('login-form');
    // Fügt dem Formular ein "submit"-Ereignis hinzu
    loginForm.addEventListener('submit', async (event) => {
    event.preventDefault(); // Verhindert das Standardverhalten des Formulars (Seiten-Neuladen)
    // Holt den Benutzername und das Passwort aus den Eingabefeldern
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    // Überprüfung, ob Benutzername und Passwort eingegeben wurden
    if (!username || !password) {
    showInfo('Bitte geben Sie Benutzername und Passwort ein!',
   'warning');
    return;
    }
    try {
    // Führt die Login-Funktion aus
    await login(username, password);
    } catch (error) {
    // Falls ein unerwarteter Fehler auftritt
    console.error('Ein Fehler ist aufgetreten:', error);
    alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
    }
    });   
});
   // Funktion zur Anzeige von Meldungen auf der Webseite
   function showInfo(infoText, level) {
    // Holt das Info-Box-Element
    const infoBox = document.getElementById('infobox');
    infoBox.innerText = infoText; // Setzt den Text der Meldung
    infoBox.classList.remove('hidden');
    infoBox.classList.add('visible');
    // Ändert die Farbe der Meldung je nach Art der Nachricht
    switch(level) {
    case 'success':
    infoBox.style.color = 'lightgreen'; // Erfolgreiche Meldung
    break;
    case 'warning':
    infoBox.style.color = 'orange'; // Warnung
    break;
    case 'error':
    infoBox.style.color = 'red'; // Fehler
    break;
    default:
    infoBox.style.color = 'lightgreen';
    }
    // Blendet die Meldung nach 5 Sekunden aus
    setTimeout(() => {
    infoBox.classList.remove('visible');
    infoBox.classList.add('hidden');
    }, 5000);
}