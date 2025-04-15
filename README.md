
# 🗂️ Self-Hosted Clipboard – Volle Kontrolle & maximale Sicherheit

Dieses Projekt basiert auf einer externen Anleitung und wurde eigenständig umgesetzt. Ziel ist es, ein **selbstgehostetes Clipboard-System** bereitzustellen, das maximale Sicherheit und volle Datenkontrolle ermöglicht – komplett ohne externe Dienste oder Drittanbieter-Abhängigkeiten.

## 🚀 Projektüberblick

In diesem Projekt entwickeln wir ein Clipboard-Tool, das **ausschließlich auf unserer eigenen Infrastruktur** läuft. Es soll sicher, flexibel und effizient sein – ideal für den täglichen Einsatz in sicherheitsbewussten Umgebungen.

## 🧩 Architektur & Komponenten

### 🧭 Container-Verwaltung mit Portainer
Zur einfachen und transparenten Verwaltung unserer Container setzen wir auf **[Portainer](https://www.portainer.io/)**.

### 🔧 Wichtige Backend-Services
Die Anwendung basiert auf einer soliden Infrastruktur:
- **PostgreSQL** – relationale Datenbank zur Clip-Speicherung  
- **Apache** – Webserver zur Bereitstellung der Anwendung  
- **Redis** – schnelles Session-Management via Token für sichere Authentifizierung

### 🔐 Sicherheit & Verschlüsselung
- Login-basierter Zugriffsschutz
- **HTTPS** via **Let’s Encrypt** zur sicheren Kommunikation

## 🛠️ Technologie-Stack

| Bereich      | Technologie        |
|-------------|--------------------|
| Frontend    | HTML, CSS, JavaScript |
| Backend     | PHP                |
| Datenbank   | PostgreSQL         |
| Session/Caching | Redis          |
| Deployment  | Eigene CI/CD-Pipeline |
| Versionskontrolle | GitHub       |

## 🔄 CI/CD & Versionskontrolle

- Integriert in eine **selbstentwickelte CI/CD-Pipeline**
- Deployment-Prozesse sind automatisiert
- Code wird über **GitHub** verwaltet für nachvollziehbare und strukturierte Entwicklung

## ❓ Warum dieses Projekt?

Dieses Projekt ist nicht nur ein technisches Experiment, sondern eine praktische Anwendung mit realem Nutzen. Es ermöglicht uns:
- Unsere Skills in **Entwicklung, Administration und Security** zu vertiefen
- Eine **sichere und flexible Lösung** für den Alltag zu schaffen
- Die **volle Kontrolle über Daten und Infrastruktur** zu behalten

---
