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
            'attr' => [
                'layout',
                'showClosed',
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
            'we-are-open.openHours.subtitle' => 'Define the opening hours for each weekday. Days without defined opening hours are automatically considered closed. Multiple time ranges can be specified per day to represent breaks, for example.',
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
        ],
    ],
]);
