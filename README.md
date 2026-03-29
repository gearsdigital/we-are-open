# We Are Open - Kirby Opening Hours Plugin

Kirby Panel Plugin zur Verwaltung von Öffnungszeiten mit regulären Wochenzeiten und außerordentlichen Tagen.

## Features

- ✅ Verwaltung regulärer Öffnungszeiten für jeden Wochentag
- ✅ Mehrere Zeitslots pro Tag (z.B. Mittagspause)
- ✅ Außerordentliche Öffnungszeiten für spezielle Tage
- ✅ Überlappungserkennung für Zeitslots
- ✅ Konfigurierbare Default-Zeiten
- ✅ i18n-Unterstützung (DE/EN)
- ✅ Modulare Komponentenstruktur

## Installation

1. Plugin-Ordner in `site/plugins/` kopieren
2. `npm install` im Plugin-Ordner ausführen
3. `npm run build` zum Kompilieren

## Konfiguration

Die Default-Öffnungszeiten können in der `site/config/config.php` konfiguriert werden:

```php
return [
    'gearsdigital.we-are-open.defaultStartTime' => '09:00:00',
    'gearsdigital.we-are-open.defaultEndTime'   => '18:00:00',
];
```

**Standard-Werte:**
- Start: `08:00:00`
- Ende: `17:00:00`

## Komponentenstruktur

### Hauptkomponenten

#### `WeAreOpenView.vue`
Haupt-Container-Komponente mit:
- State-Management für Öffnungszeiten
- API-Kommunikation
- i18n-Integration
- Validierung

#### `OpenHoursRow.vue`
Zeile für reguläre Öffnungszeiten:
- Wochentag-Label
- Zeitslot-Verwaltung
- Überlappungsprüfung
- Add/Remove-Funktionen

#### `ExceptionDayRow.vue`
Zeile für außerordentliche Tage:
- Datums-Auswahl
- Zeitslot-Verwaltung
- Grund-Feld (optional)
- Add/Remove-Funktionen

#### `TimeSlot.vue`
Wiederverwendbare Zeitslot-Komponente:
- Start-/Endzeit-Eingabe mit `k-time-input`
- Fehlervisualisierung
- Remove-Button

### i18n

Lokalisierungen in `src/i18n.js`:
- **Deutsch (de):** Standard
- **Englisch (en):** Verfügbar

```javascript
// Verwendung in Komponenten
this.t('openHours.title')        // "Öffnungszeiten"
this.t('weekdays.mon')            // "Montag"
this.t('messages.saved')          // "Gespeichert"
```

## Dateistruktur

```
we-are-open/
├── src/
│   ├── components/
│   │   ├── WeAreOpenView.vue      # Haupt-Container
│   │   ├── OpenHoursRow.vue       # Reguläre Öffnungszeiten
│   │   ├── ExceptionDayRow.vue    # Außerordentliche Tage
│   │   └── TimeSlot.vue           # Zeitslot-Komponente
│   ├── i18n.js                     # Lokalisierungen
│   └── index.js                    # Plugin-Registrierung
├── lib/                            # PHP-Bibliotheken
├── index.php                       # PHP-Plugin-Logik
├── package.json
└── README.md
```

## PHP-API

### Site-Methoden

#### `site()->schedule($hours, $closedDays)`

Gibt ein Objekt mit aktuellen Öffnungsinformationen zurück:

```php
$schedule = site()->schedule(
    site()->openhours()->yaml(),
    site()->closeddays()->yaml()
);

// Verfügbare Eigenschaften:
$schedule->isOpen              // boolean: Aktuell geöffnet?
$schedule->hours_this_week     // array: Öffnungszeiten dieser Woche
$schedule->isSpecialDay        // boolean: Ist heute ein besonderer Tag?
$schedule->hasSpecialHours     // boolean: Hat der Tag besondere Zeiten?
$schedule->specialDayReason    // string|null: Grund
$schedule->specialHours        // array: Besondere Öffnungszeiten
```

### KirbyTags

#### `(openNote:)`
Zeigt den aktuellen Öffnungsstatus an:
```
(openNote: variant: text)  // Nur Text
(openNote:)                // Als Badge
```

#### `(schedule:)`
Zeigt formatierte Öffnungszeiten:
```
(schedule:)
```

#### `(scheduleTable:)`
Zeigt Öffnungszeiten als Tabelle:
```
(scheduleTable:)
```

#### `(closureNote:)`
Zeigt Hinweis bei außerordentlichen Öffnungszeiten/Schließungen:
```
(closureNote:)
```

## Datenformat

### Reguläre Öffnungszeiten

```yaml
openhours:
  -
    weekday: mon
    slots:
      - start: '08:00:00'
        end: '12:00:00'
      - start: '13:00:00'
        end: '17:00:00'
```

### Außerordentliche Tage

```yaml
closeddays:
  -
    date: '2024-12-24'
    reason: 'Heiligabend'
    slots:
      - start: '08:00:00'
        end: '12:00:00'
  -
    date: '2024-12-25'
    reason: '1. Weihnachtsfeiertag'
    slots: []  # Komplett geschlossen
```

## Entwicklung

### Build-Befehle

```bash
# Development mit Watch-Mode
npm run dev

# Production Build
npm run build
```

### Wartbarkeit

Die refaktorisierte Struktur bietet:

1. **Modulare Komponenten:** Jede Komponente hat eine klar definierte Verantwortung
2. **Wiederverwendbarkeit:** `TimeSlot.vue` wird in beiden Tabellen verwendet
3. **Einfache Erweiterbarkeit:** Neue Funktionen können isoliert hinzugefügt werden
4. **Testbarkeit:** Komponenten können einzeln getestet werden
5. **i18n-ready:** Neue Sprachen können einfach in `i18n.js` hinzugefügt werden

### Code-Konventionen

- **Props:** Immer mit Type-Definitionen
- **Events:** Beschreibende Namen (z.B. `update-slot`, `remove-exception`)
- **Styles:** Scoped CSS für Komponenten-Isolation
- **Naming:** BEM-ähnliche Konvention (`k-we-are-open-*`)

## Lizenz

MIT

## Autor

gearsdigital
