<?php
declare(strict_types=1);

return [
    'meta' => [
        'title' => 'Count Us Kurds – Einladung zum Gründungsteam',
        'description' => 'Gestalten Sie die erste weltweite digitale Volkszählung der Kurdinnen und Kurden mit. Melden Sie Ihr persönliches oder institutionelles Engagement innerhalb eines sicheren und transparenten Rahmens an.',
    ],
    'nav' => [
        'toggle' => 'Menü',
        'language_label' => 'Sprache',
        'change_language' => 'Sprache wechseln',
        'vision' => 'Vision und Leitprinzipien',
        'timeline' => 'Zweck und Ziele',
        'application' => 'Gründungsteam',
        'privacy' => 'Datenschutzrichtlinie',
        'back_home' => 'Zurück zur Hauptseite',
        'close_language' => 'Fertig',
    ],
    'hero' => [
        'eyebrow' => 'Internationales zivilgesellschaftliches Vorhaben',
        'title' => 'Gemeinsam die Zukunft des kurdischen Volkes sichern',
        'subtitle' => 'Count Us Kurds ist eine unabhängige, überparteiliche Initiative, die die weltweite kurdische Gemeinschaft durch eine verantwortungsvolle digitale Erfassung sichtbar und handlungsfähig macht.',
        'cta' => 'Dem Gründungsteam beitreten',
    ],
    'stats' => [
        'secure' => 'Sichere Daten-Governance',
        'community' => 'Globale kurdische Repräsentation',
        'gdpr' => 'Umfassende DSGVO-Konformität',
    ],
    'timeline' => [
        'heading' => 'Zweck und Ziele',
        'description' => 'Drei klare Schritte halten diese kurdische Volkszählung sicher, einfach und gemeinschaftsgeführt.',
        'phases' => [
            [
                'title' => 'Kurdinnen und Kurden überall sicher erfassen',
                'description' => 'Freiwillige Meldungen aus Heimatregionen und Diaspora sammeln, ohne Identitäten offenzulegen.',
            ],
            [
                'title' => 'Lokale Knoten und Diaspora verbinden',
                'description' => 'Räte, Kulturzentren und Organisierende vernetzen, damit Wissen in beide Richtungen fließt.',
            ],
            [
                'title' => 'Eigene Daten zum Schutz der Zukunft nutzen',
                'description' => 'Verdichtete Fakten teilen, um Rechte, Kultur und Ressourcen mit Glaubwürdigkeit zu verteidigen.',
            ],
        ],
    ],
    'vision' => [
        'heading' => 'Vision und Leitlinien',
        'body' => 'Wir setzen uns dafür ein, dass jede Kurdin und jeder Kurde – unabhängig von Aufenthaltsort oder Biografie – sichtbar und respektvoll erfasst wird. Die Plattform orientiert sich an internationalen Datenschutzstandards, gemeinschaftlicher Verantwortung und transparenter Verwaltung.',
        'principles' => [
            [
                'title' => 'Unabhängigkeit',
                'description' => 'Keine Bindung an politische Parteien, religiöse Institutionen oder staatliche Stellen. Die Gemeinschaft trifft die Entscheidungen.',
            ],
            [
                'title' => 'Sicherheit und Integrität',
                'description' => 'Sensible Daten werden durch geprüfte Prozesse sowie Verschlüsselung geschützt und erfüllen die Vorgaben der DSGVO.',
            ],
            [
                'title' => 'Einigkeit und Inklusion',
                'description' => 'Alle kurdischen Identitäten, Dialekte und Erfahrungen werden gleichberechtigt einbezogen.',
            ],
            [
                'title' => 'Transparenz',
                'description' => 'Ziele, Finanzierung und technische Entscheidungen werden offen kommuniziert und unabhängiger Prüfung zugänglich gemacht.',
            ],
        ],
    ],
    'form' => [
        'heading' => 'Interessenbekundung – Gründungsteam',
        'description' => 'Beschreiben Sie, wie Sie oder Ihre Organisation mitwirken möchten. Die Informationen werden während der Übertragung und Speicherung verschlüsselt und ausschließlich berechtigten Koordinatorinnen und Koordinatoren zugänglich gemacht.',
        'tabs' => [
            'individual' => 'Einzelbewerbung',
            'group' => 'Organisation oder Gruppe',
        ],
        'fields' => [
            'name' => [
                'label' => 'Vollständiger Name oder Hauptkontakt',
                'placeholder' => 'Vollständiger amtlicher Name',
            ],
            'email' => [
                'label' => 'E-Mail-Adresse',
                'placeholder' => 'name@example.com',
            ],
            'region' => [
                'label' => 'Region / Diaspora',
                'options' => [
                    'prompt' => 'Primäre Region oder Diaspora auswählen',
                    'bakur' => 'Bakur',
                    'rojava' => 'Rojava',
                    'rojhilat' => 'Rojhilat',
                    'bashur' => 'Bashur',
                    'europe' => 'Diaspora: Europa',
                    'na' => 'Diaspora: Nordamerika',
                    'other' => 'Weitere Diaspora / anderer Standort',
                ],
            ],
            'individual_contribution' => [
                'label' => 'Wie möchten Sie beitragen?',
                'placeholder' => 'Beispiele: Cybersicherheit, juristische Beratung, Forschung, Community-Outreach, Governance, finanzielle Unterstützung.',
            ],
            'org_name' => [
                'label' => 'Name der Organisation / Gruppe',
                'placeholder' => 'Registrierter oder gebräuchlicher Name',
            ],
            'org_contribution' => [
                'label' => 'Kompetenzen und Zusagen',
                'placeholder' => 'Beschreiben Sie Ihre Schwerpunkte, Netzwerke, Ressourcen und den vorgeschlagenen Beitrag.',
            ],
            'org_motive' => [
                'label' => 'Motivation zur Teilnahme',
                'placeholder' => 'Erläutern Sie Ihre Erwartungen an die Gründungsversammlung und die langfristige Zusammenarbeit.',
            ],
            'gdpr' => [
                'label' => 'Ich stimme zu, dass Count Us Kurds meine Daten entsprechend der Datenschutzrichtlinie verarbeitet.',
            ],
        ],
        'buttons' => [
            'submit' => 'Interessenbekundung absenden',
            'policy' => 'Datenschutzrichtlinie ansehen',
        ],
        'messages' => [
            'invalid_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'consent_required' => 'Die Zustimmung zur Datenschutzrichtlinie ist erforderlich.',
            'required_fields' => 'Bitte füllen Sie alle Pflichtfelder aus.',
            'describe_contribution' => 'Beschreiben Sie bitte den geplanten Beitrag.',
            'group_required' => 'Name, Beitrag und Motivation der Organisation sind erforderlich.',
            'duplicate' => 'Diese E-Mail-Adresse ist bereits registriert. Kontaktieren Sie uns für Aktualisierungen.',
            'database_error' => 'Die Daten konnten derzeit nicht gespeichert werden. Bitte versuchen Sie es erneut.',
            'exception' => 'Es ist ein unerwarteter Fehler aufgetreten. Bitte versuchen Sie es erneut oder kontaktieren Sie uns.',
            'csrf_failed' => 'Ihre Sitzung ist abgelaufen. Bitte aktualisieren Sie die Seite und senden Sie das Formular erneut.',
            'success' => 'Vielen Dank. Unser Koordinationsteam meldet sich zeitnah bei Ihnen.',
        ],
    ],
    'footer' => [
        'contact' => 'info@countuskurds.com',
        'tagline' => 'Globale Plattform für kurdische Volkszählung und Verwaltung.',
        'rights' => '© :year Count Us Kurds. Alle Rechte respektvoll vorbehalten.',
    ],
    'privacy' => [
        'page_title' => 'Datenschutz und Dateneigentum',
        'page_description' => 'Count Us Kurds sichert jede Einreichung und beansprucht das volle Eigentum an den Daten, die für den globalen kurdischen Zensus verwendet werden.',
        'language_label' => 'Sprachversion',
        'updated' => 'Aktualisiert',
    ],
];
