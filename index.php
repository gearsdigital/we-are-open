<?php

use GearsDigital\WeAreOpen\Facade\WeAreOpenFacade;
use GearsDigital\WeAreOpen\Schedule\ScheduleTableTagService;
use GearsDigital\WeAreOpen\Support\Api;
use GearsDigital\WeAreOpen\Support\SiteYamlKeys;
use GearsDigital\WeAreOpen\Support\SiteYamlReader;
use Kirby\Cms\App as Kirby;

$autoload = __DIR__.'/vendor/autoload.php';

if (is_file($autoload)) {
    require_once $autoload;
}

Kirby::plugin('gearsdigital/we-are-open', [
    'siteMethods' => [
        'weAreOpen' => function () {
            return new WeAreOpenFacade();
        },
    ],

    'areas' => [
        'we-are-open' => function () {
            return [
                'label' => 'Öffnungszeiten',
                'icon' => 'clock',
                'menu' => true,
                'link' => 'we-are-open',
                'views' => [
                    [
                        'pattern' => 'we-are-open',
                        'action' => function () {
                            $defaultStartTime = option('gearsdigital.we-are-open.defaultStartTime', '08:00:00');
                            $defaultEndTime = option('gearsdigital.we-are-open.defaultEndTime', '17:00:00');

                            return [
                                'component' => 'k-we-are-open-view',
                                'title' => 'Öffnungszeiten',
                                'props' => [
                                    'openHours' => Api::normalizeOpenHours(SiteYamlReader::get(SiteYamlKeys::OPENHOURS)),
                                    'defaultStartTime' => $defaultStartTime,
                                    'defaultEndTime' => $defaultEndTime,
                                ],
                            ];
                        },
                    ],
                ],
            ];
        },
    ],

    'api' => [
        'routes' => [
            [
                'pattern' => 'we-are-open/save',
                'method' => 'POST',
                'action' => function () {
                    // Check if PRO version is available
                    $proApiClass = 'GearsDigital\\WeAreOpenPro\\Support\\Api';
                    if (class_exists($proApiClass) && method_exists($proApiClass, 'save')) {
                        return $proApiClass::save();
                    }
                    return Api::save();
                },
            ],
            [
                'pattern' => 'we-are-open/load',
                'method' => 'GET',
                'action' => function () {
                    // Check if PRO version is available
                    $proApiClass = 'GearsDigital\\WeAreOpenPro\\Support\\Api';
                    if (class_exists($proApiClass) && method_exists($proApiClass, 'load')) {
                        return $proApiClass::load();
                    }
                    return Api::load();
                },
            ],
        ],
    ],

    'snippets' => [
        'we-are-open/business-hours-table' => __DIR__.'/snippets/we-are-open/business-hours-table.php',
    ],

    'tags' => [
        'scheduleTable' => [
            // KirbyTag matches attribute names case-insensitively but only
            // promotes them to $tag->{name} properties when the registered
            // name here is lowercase — keep these lowercase even though the
            // tag itself reads fine as `(scheduleTable: showClosed: ...)`.
            'attr' => [
                'layout',
                'showclosed',
                'showweekends',
                'timeformat',
            ],
            'html' => static function ($tag): string {
                return ScheduleTableTagService::render($tag);
            },
        ],
    ],

    'translations' => [
        'de' => [
            'we-are-open.title' => 'Öffnungszeiten',

            'we-are-open.openHours.title' => 'Reguläre Öffnungszeiten',
            'we-are-open.openHours.subtitle' => 'Legen Sie Öffnungszeiten für einzelne Wochentage fest. Pro Tag kann <b>ein Zeitraum</b> definiert werden. Tage ohne Eintrag gelten automatisch als geschlossen.',
            'we-are-open.openHours.closed' => 'Geschlossen',
            'we-are-open.openHours.addSlot' => 'Zeitraum hinzufügen',
            'we-are-open.openHours.removeSlot' => 'Zeitraum entfernen',
            'we-are-open.openHours.overlap' => 'Überlappung',
            'we-are-open.openHours.overlapError' => 'Zeiträume überschneiden sich',
            'we-are-open.openHours.invalidTimeError' => 'Startzeit muss vor Endzeit liegen',

            'we-are-open.weekdays.mon' => 'Montag',
            'we-are-open.weekdays.tue' => 'Dienstag',
            'we-are-open.weekdays.wed' => 'Mittwoch',
            'we-are-open.weekdays.thu' => 'Donnerstag',
            'we-are-open.weekdays.fri' => 'Freitag',
            'we-are-open.weekdays.sat' => 'Samstag',
            'we-are-open.weekdays.sun' => 'Sonntag',

            'we-are-open.messages.saved' => 'Gespeichert',
            'we-are-open.messages.errorSaving' => 'Fehler beim Speichern',
            'we-are-open.messages.overlapError' => 'Bitte beheben Sie die Überlappungen vor dem Speichern.',
            'we-are-open.messages.validationError' => 'Bitte beheben Sie alle Fehler (Überlappungen und ungültige Zeiten) vor dem Speichern.',

            'we-are-open.proTeaserTitle' => "We're open PRO",
            'we-are-open.proTeaserText' => '»We Are Open <strong>PRO</strong>« – unbegrenzt viele Zeiträume, flexible Sonderöffnungszeiten und automatische berücksichtigung von Feiertagen in den Öffnungszeiten. <a href="">Mehr erfahren…</a>',
        ],

        'en' => [
            'we-are-open.title' => 'Opening hours',

            'we-are-open.openHours.title' => 'Opening hours',
            'we-are-open.openHours.subtitle' => 'Define the opening hours for each weekday. <b>One time slot</b> can be defined per day. Days without defined opening hours are automatically considered closed.',
            'we-are-open.openHours.closed' => 'Closed',
            'we-are-open.openHours.addSlot' => 'Add time slot',
            'we-are-open.openHours.removeSlot' => 'Remove time slot',
            'we-are-open.openHours.overlap' => 'Overlap',
            'we-are-open.openHours.overlapError' => 'Time slots overlap',
            'we-are-open.openHours.invalidTimeError' => 'Start time must be before end time',

            'we-are-open.weekdays.mon' => 'Monday',
            'we-are-open.weekdays.tue' => 'Tuesday',
            'we-are-open.weekdays.wed' => 'Wednesday',
            'we-are-open.weekdays.thu' => 'Thursday',
            'we-are-open.weekdays.fri' => 'Friday',
            'we-are-open.weekdays.sat' => 'Saturday',
            'we-are-open.weekdays.sun' => 'Sunday',

            'we-are-open.messages.saved' => 'Saved',
            'we-are-open.messages.errorSaving' => 'Error while saving',
            'we-are-open.messages.overlapError' => 'Please resolve overlaps before saving.',
            'we-are-open.messages.validationError' => 'Please resolve all errors (overlaps and invalid times) before saving.',

            'we-are-open.proTeaserTitle' => "We're open PRO",
            'we-are-open.proTeaserText' => '»We Are Open <strong>PRO</strong>« — unlimited time slots per day, flexible exception days, and automatic public holiday support. <a href="">Learn more…</a>',
        ],

        'fr' => [
            'we-are-open.title' => 'Horaires d\'ouverture',

            'we-are-open.openHours.title' => 'Horaires d\'ouverture réguliers',
            'we-are-open.openHours.subtitle' => 'Définissez les horaires d\'ouverture pour chaque jour de la semaine. <b>Un créneau horaire</b> peut être défini par jour. Les jours sans horaire sont automatiquement considérés comme fermés.',
            'we-are-open.openHours.closed' => 'Fermé',
            'we-are-open.openHours.addSlot' => 'Ajouter un créneau',
            'we-are-open.openHours.removeSlot' => 'Supprimer le créneau',
            'we-are-open.openHours.overlap' => 'Chevauchement',
            'we-are-open.openHours.overlapError' => 'Les créneaux horaires se chevauchent',
            'we-are-open.openHours.invalidTimeError' => 'L\'heure de début doit précéder l\'heure de fin',

            'we-are-open.weekdays.mon' => 'Lundi',
            'we-are-open.weekdays.tue' => 'Mardi',
            'we-are-open.weekdays.wed' => 'Mercredi',
            'we-are-open.weekdays.thu' => 'Jeudi',
            'we-are-open.weekdays.fri' => 'Vendredi',
            'we-are-open.weekdays.sat' => 'Samedi',
            'we-are-open.weekdays.sun' => 'Dimanche',

            'we-are-open.messages.saved' => 'Enregistré',
            'we-are-open.messages.errorSaving' => 'Erreur lors de l\'enregistrement',
            'we-are-open.messages.overlapError' => 'Veuillez résoudre les chevauchements avant d\'enregistrer.',
            'we-are-open.messages.validationError' => 'Veuillez corriger toutes les erreurs (chevauchements et horaires invalides) avant d\'enregistrer.',

            'we-are-open.proTeaserTitle' => "We're open PRO",
            'we-are-open.proTeaserText' => '»We Are Open <strong>PRO</strong>« – créneaux horaires illimités, horaires exceptionnels flexibles et prise en compte automatique des jours fériés. <a href="">En savoir plus…</a>',
        ],

        'pl' => [
            'we-are-open.title' => 'Godziny otwarcia',

            'we-are-open.openHours.title' => 'Standardowe godziny otwarcia',
            'we-are-open.openHours.subtitle' => 'Ustaw godziny otwarcia dla poszczególnych dni tygodnia. Dla każdego dnia można zdefiniować <b>jeden przedział czasowy</b>. Dni bez wpisu są automatycznie traktowane jako zamknięte.',
            'we-are-open.openHours.closed' => 'Zamknięte',
            'we-are-open.openHours.addSlot' => 'Dodaj przedział czasowy',
            'we-are-open.openHours.removeSlot' => 'Usuń przedział czasowy',
            'we-are-open.openHours.overlap' => 'Nakładanie się',
            'we-are-open.openHours.overlapError' => 'Przedziały czasowe nakładają się na siebie',
            'we-are-open.openHours.invalidTimeError' => 'Godzina rozpoczęcia musi być wcześniejsza niż godzina zakończenia',

            'we-are-open.weekdays.mon' => 'Poniedziałek',
            'we-are-open.weekdays.tue' => 'Wtorek',
            'we-are-open.weekdays.wed' => 'Środa',
            'we-are-open.weekdays.thu' => 'Czwartek',
            'we-are-open.weekdays.fri' => 'Piątek',
            'we-are-open.weekdays.sat' => 'Sobota',
            'we-are-open.weekdays.sun' => 'Niedziela',

            'we-are-open.messages.saved' => 'Zapisano',
            'we-are-open.messages.errorSaving' => 'Błąd podczas zapisywania',
            'we-are-open.messages.overlapError' => 'Przed zapisaniem usuń nakładające się przedziały czasowe.',
            'we-are-open.messages.validationError' => 'Przed zapisaniem popraw wszystkie błędy (nakładające się przedziały i nieprawidłowe godziny).',

            'we-are-open.proTeaserTitle' => "We're open PRO",
            'we-are-open.proTeaserText' => '»We Are Open <strong>PRO</strong>« – nieograniczona liczba przedziałów czasowych, elastyczne dni wyjątkowe oraz automatyczne uwzględnianie dni świątecznych. <a href="">Dowiedz się więcej…</a>',
        ],

        'cs' => [
            'we-are-open.title' => 'Otevírací doba',

            'we-are-open.openHours.title' => 'Pravidelná otevírací doba',
            'we-are-open.openHours.subtitle' => 'Nastavte otevírací dobu pro jednotlivé dny v týdnu. Pro každý den lze definovat <b>jeden časový úsek</b>. Dny bez zadání jsou automaticky považovány za zavřené.',
            'we-are-open.openHours.closed' => 'Zavřeno',
            'we-are-open.openHours.addSlot' => 'Přidat časový úsek',
            'we-are-open.openHours.removeSlot' => 'Odebrat časový úsek',
            'we-are-open.openHours.overlap' => 'Překryv',
            'we-are-open.openHours.overlapError' => 'Časové úseky se překrývají',
            'we-are-open.openHours.invalidTimeError' => 'Čas zahájení musí být před časem ukončení',

            'we-are-open.weekdays.mon' => 'Pondělí',
            'we-are-open.weekdays.tue' => 'Úterý',
            'we-are-open.weekdays.wed' => 'Středa',
            'we-are-open.weekdays.thu' => 'Čtvrtek',
            'we-are-open.weekdays.fri' => 'Pátek',
            'we-are-open.weekdays.sat' => 'Sobota',
            'we-are-open.weekdays.sun' => 'Neděle',

            'we-are-open.messages.saved' => 'Uloženo',
            'we-are-open.messages.errorSaving' => 'Chyba při ukládání',
            'we-are-open.messages.overlapError' => 'Před uložením prosím vyřešte překryvy.',
            'we-are-open.messages.validationError' => 'Před uložením prosím opravte všechny chyby (překryvy a neplatné časy).',

            'we-are-open.proTeaserTitle' => "We're open PRO",
            'we-are-open.proTeaserText' => '»We Are Open <strong>PRO</strong>« – neomezený počet časových úseků, flexibilní výjimečné otevírací doby a automatické zohlednění státních svátků. <a href="">Zjistit více…</a>',
        ],

        'nl' => [
            'we-are-open.title' => 'Openingstijden',

            'we-are-open.openHours.title' => 'Reguliere openingstijden',
            'we-are-open.openHours.subtitle' => 'Stel de openingstijden voor elke weekdag in. Per dag kan <b>één tijdsblok</b> worden gedefinieerd. Dagen zonder invoer worden automatisch als gesloten beschouwd.',
            'we-are-open.openHours.closed' => 'Gesloten',
            'we-are-open.openHours.addSlot' => 'Tijdsblok toevoegen',
            'we-are-open.openHours.removeSlot' => 'Tijdsblok verwijderen',
            'we-are-open.openHours.overlap' => 'Overlapping',
            'we-are-open.openHours.overlapError' => 'Tijdsblokken overlappen elkaar',
            'we-are-open.openHours.invalidTimeError' => 'Begintijd moet vóór eindtijd liggen',

            'we-are-open.weekdays.mon' => 'Maandag',
            'we-are-open.weekdays.tue' => 'Dinsdag',
            'we-are-open.weekdays.wed' => 'Woensdag',
            'we-are-open.weekdays.thu' => 'Donderdag',
            'we-are-open.weekdays.fri' => 'Vrijdag',
            'we-are-open.weekdays.sat' => 'Zaterdag',
            'we-are-open.weekdays.sun' => 'Zondag',

            'we-are-open.messages.saved' => 'Opgeslagen',
            'we-are-open.messages.errorSaving' => 'Fout bij het opslaan',
            'we-are-open.messages.overlapError' => 'Los de overlappingen op voordat u opslaat.',
            'we-are-open.messages.validationError' => 'Los alle fouten op (overlappingen en ongeldige tijden) voordat u opslaat.',

            'we-are-open.proTeaserTitle' => "We're open PRO",
            'we-are-open.proTeaserText' => '»We Are Open <strong>PRO</strong>« – onbeperkt aantal tijdsblokken, flexibele uitzonderingsdagen en automatische verwerking van feestdagen. <a href="">Meer informatie…</a>',
        ],
    ],
]);
