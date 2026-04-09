<?php
declare(strict_types=1);

return [
    'meta' => [
        'title' => 'Count Us Kurds – Invitation à l’équipe fondatrice',
        'description' => 'Participez à la première initiative de recensement numérique mondial pour le peuple kurde. Enregistrez votre engagement personnel ou organisationnel dans un cadre sécurisé et transparent.',
    ],
    'nav' => [
        'toggle' => 'Menu',
        'language_label' => 'Langue',
        'change_language' => 'Changer de langue',
        'vision' => 'Vision et principes',
        'timeline' => 'Objectifs et buts',
        'application' => 'Équipe fondatrice',
        'privacy' => 'Politique de confidentialité',
        'back_home' => 'Retour au site principal',
        'close_language' => 'Terminé',
    ],
    'hero' => [
        'eyebrow' => 'Initiative civique internationale',
        'title' => 'Construisons ensemble l’avenir du peuple kurde',
        'subtitle' => 'Count Us Kurds est une initiative indépendante et non partisane qui souhaite documenter et renforcer la communauté kurde mondiale grâce à un recensement numérique responsable.',
        'cta' => 'Rejoindre l’équipe fondatrice',
    ],
    'stats' => [
        'secure' => 'Gouvernance sécurisée des données',
        'community' => 'Représentation kurde mondiale',
        'gdpr' => 'Conformité totale au RGPD',
    ],
    'timeline' => [
        'heading' => 'Objectifs et buts',
        'description' => 'Trois actions claires maintiennent ce recensement kurde sûr, simple et dirigé par la communauté.',
        'phases' => [
            [
                'title' => 'Compter les Kurdes en sécurité partout',
                'description' => 'Collecter des contributions volontaires des territoires et de la diaspora sans exposer les identités.',
            ],
            [
                'title' => 'Relier les pôles locaux et la diaspora',
                'description' => 'Connecter conseils, centres culturels et organisateurs pour faire circuler les savoirs dans les deux sens.',
            ],
            [
                'title' => 'Utiliser nos données pour protéger l’avenir',
                'description' => 'Partager des faits agrégés afin de défendre droits, culture et ressources avec crédibilité.',
            ],
        ],
    ],
    'vision' => [
        'heading' => 'Vision et principes directeurs',
        'body' => 'Nous aspirons à ce que chaque Kurde, où qu’il vive et quelle que soit son histoire, soit reconnu avec dignité. La plateforme s’appuie sur des normes internationales de protection des données, une gouvernance communautaire et une transparence totale.',
        'principles' => [
            [
                'title' => 'Indépendance',
                'description' => 'Aucun lien avec des partis politiques, institutions religieuses ou gouvernements. Les décisions appartiennent à la communauté.',
            ],
            [
                'title' => 'Sécurité et intégrité',
                'description' => 'Les données sensibles sont protégées grâce à des processus audités et un chiffrement conforme, voire supérieur, aux exigences du RGPD.',
            ],
            [
                'title' => 'Unité et inclusion',
                'description' => 'Toutes les identités kurdes, les dialectes et les expériences sont accueillis avec un respect égal.',
            ],
            [
                'title' => 'Transparence',
                'description' => 'Nos objectifs, nos financements et nos choix technologiques sont communiqués ouvertement et soumis à l’examen indépendant.',
            ],
        ],
    ],
    'form' => [
        'heading' => 'Déclaration d’intérêt – Équipe fondatrice',
        'description' => 'Expliquez comment vous ou votre organisation souhaitez contribuer. Les informations sont chiffrées en transit et au repos, et seules les personnes autorisées peuvent les consulter.',
        'tabs' => [
            'individual' => 'Candidature individuelle',
            'group' => 'Organisation ou groupe',
        ],
        'fields' => [
            'name' => [
                'label' => 'Nom complet ou personne de contact',
                'placeholder' => 'Nom légal complet',
            ],
            'email' => [
                'label' => 'Adresse e-mail',
                'placeholder' => 'nom@example.com',
            ],
            'region' => [
                'label' => 'Région / diaspora',
                'options' => [
                    'prompt' => 'Sélectionnez votre région ou diaspora principale',
                    'bakur' => 'Bakur',
                    'rojava' => 'Rojava',
                    'rojhilat' => 'Rojhilat',
                    'bashur' => 'Bashur',
                    'europe' => 'Diaspora : Europe',
                    'na' => 'Diaspora : Amérique du Nord',
                    'other' => 'Autre région ou diaspora',
                ],
            ],
            'individual_contribution' => [
                'label' => 'Comment souhaitez-vous contribuer ?',
                'placeholder' => 'Exemples : cybersécurité, expertise juridique, recherche, mobilisation communautaire, gouvernance, soutien financier.',
            ],
            'org_name' => [
                'label' => 'Nom de l’organisation / du groupe',
                'placeholder' => 'Dénomination officielle ou d’usage',
            ],
            'org_contribution' => [
                'label' => 'Compétences et engagements',
                'placeholder' => 'Décrivez vos domaines d’expertise, vos réseaux, vos ressources et votre contribution proposée.',
            ],
            'org_motive' => [
                'label' => 'Motivations',
                'placeholder' => 'Présentez vos attentes concernant l’assemblée fondatrice et la coopération à long terme.',
            ],
            'gdpr' => [
                'label' => 'J’autorise Count Us Kurds à traiter mes données conformément à la politique de confidentialité.',
            ],
        ],
        'buttons' => [
            'submit' => 'Soumettre la déclaration d’intérêt',
            'policy' => 'Consulter la politique de confidentialité',
        ],
        'messages' => [
            'invalid_email' => 'Veuillez indiquer une adresse e-mail valide.',
            'consent_required' => 'L’acceptation de la politique de confidentialité est requise.',
            'required_fields' => 'Merci de remplir l’ensemble des champs obligatoires.',
            'describe_contribution' => 'Veuillez préciser la contribution que vous proposez.',
            'group_required' => 'Le nom de l’organisation, la contribution et les motivations sont indispensables.',
            'duplicate' => 'Cette adresse e-mail est déjà enregistrée. Contactez-nous pour une mise à jour.',
            'database_error' => 'Nous ne pouvons pas enregistrer vos informations pour le moment. Réessayez ultérieurement.',
            'exception' => 'Une erreur inattendue est survenue. Merci de réessayer ou de nous contacter.',
            'csrf_failed' => 'Votre session a expiré. Actualisez la page puis soumettez de nouveau le formulaire.',
            'success' => 'Merci. Notre équipe de coordination vous répondra rapidement.',
        ],
    ],
    'footer' => [
        'contact' => 'info@countuskurds.com',
        'tagline' => 'Plateforme mondiale de recensement et d’intendance des Kurdes.',
        'rights' => '© :year Count Us Kurds. Tous droits réservés avec respect.',
    ],
    'privacy' => [
        'page_title' => 'Confidentialité et propriété des données',
        'page_description' => 'Count Us Kurds sécurise chaque soumission et affirme la propriété communautaire complète des données qui construisent le recensement kurde mondial.',
        'language_label' => 'Version linguistique',
        'updated' => 'Mise à jour',
    ],
    'email' => [
        'subject' => 'Merci pour votre candidature – Count Us Kurds',
        'greeting' => 'Cher(e) :name,',
        'line1' => 'Merci pour votre intérêt à rejoindre l\'équipe fondatrice de Count Us Kurds.',
        'line2' => 'Nous avons bien reçu votre candidature et examinerons attentivement votre contribution proposée.',
        'auto_reply' => 'Ceci est une confirmation automatique',
        'response_time' => 'Notre équipe de coordination vous répondra dans les 5 à 7 jours ouvrables.',
        'closing' => 'Cordialement,\nL\'équipe Count Us Kurds',
    ],
];
