<?php
declare(strict_types=1);

return [
    'meta' => [
        'title' => 'Count Us Kurds – We Exist. We Count. We Rise.',
        'description' => 'Participate in building the first independent digital census of the Kurdish people. Together, we make our nation visible to the world.',
    ],
    'nav' => [
        'toggle' => 'Menu',
        'language_label' => 'Language',
        'change_language' => 'Change language',
        'vision' => 'Our Mission',
        'timeline' => 'Our Goals',
        'application' => 'Register Interest',
        'privacy' => 'Privacy Policy',
        'back_home' => 'Return to homepage',
        'close_language' => 'Close',
    ],
    'hero' => [
        'eyebrow' => 'One nation. One voice. One future.',
        'title' => 'We are 50 million',
        'subtitle' => 'The Kurdish people constitute one of the world\'s largest nations without a state of their own. Through this initiative, we create the first independent census – a historic step towards recognition and visibility.',
        'cta' => 'Register your interest',
        'banner_alt' => 'The Kurdish people gathered under the symbol of Kurdistan',
    ],
    'stats' => [
        'secure' => 'Security & Independence',
        'community' => 'Global Community',
        'gdpr' => 'Cultural Heritage Protection',
    ],
    'manifesto' => [
        'heading' => 'Why this census is necessary',
        'paragraphs' => [
            'The Kurdish people are the largest ethnic group in the world without a state of their own. Over 50 million Kurds live in Kurdistan and the diaspora – in Bakur, Bashur, Rojava, Rojhilat, as well as in Europe, North America, and across the globe. Despite this, we remain invisible in official statistical records.',
            'The states that control Kurdish territories register us as Turks, Iranians, Iraqis, or Syrians – never as Kurds. Our language, our culture, and our identity have been systematically excluded from official records and censuses.',
            'Count Us Kurds is an independent, citizen-driven initiative to document our true population, preserve our identity, and demonstrate to the world what we have always known: We exist. We are many. We are united.',
        ],
    ],
    'timeline' => [
        'heading' => 'Three steps towards historic change',
        'description' => 'Every significant change begins with a first step. Here is our strategy to achieve what no state has yet made possible.',
        'phases' => [
            [
                'title' => 'Document',
                'description' => 'Establish the first secure and voluntary census of Kurds worldwide – from the mountains of Kurdistan to cities such as Stockholm, Berlin, and London.',
            ],
            [
                'title' => 'Unite',
                'description' => 'Create networks between Kurdish communities, organizations, and families across national borders. In unity lies our strength.',
            ],
            [
                'title' => 'Influence',
                'description' => 'Utilize documented data to demand recognition, protect our rights, and ensure future generations inherit a world that acknowledges our existence.',
            ],
        ],
    ],
    'vision' => [
        'heading' => 'Our core principles',
        'body' => 'This initiative is more than a census. It is an act of resistance, love, and determination. We build this platform on the values that have sustained our people through millennia.',
        'principles' => [
            [
                'title' => 'Complete independence',
                'description' => 'No political party, government, or foreign power controls this initiative. It belongs to the Kurdish people – all of us, regardless of dialect, religion, or political conviction.',
            ],
            [
                'title' => 'Highest level of security',
                'description' => 'We protect every participant. All data is encrypted and identities are kept secure. We will never allow information to be used against our own people.',
            ],
            [
                'title' => 'Inclusive unity',
                'description' => 'Whether you speak Kurmanji, Sorani, Zazaki, or Gorani – whether you are from Amed, Hewlêr, Qamişlo, or Chicago – you are part of this community.',
            ],
        ],
    ],
    'cta_section' => [
        'heading' => 'The time is now',
        'body' => 'Our ancestors dreamed of a day when the Kurdish people would be recognized. Our parents fought for this goal. Now it is our turn to take the next step.',
    ],
    'form' => [
        'heading' => 'Expression of Interest',
        'description' => 'We are building this together. Please indicate how you or your organization can contribute to this historic initiative.',
        'tabs' => [
            'individual' => 'Individual',
            'group' => 'Organization',
        ],
        'fields' => [
            'name' => [
                'label' => 'Full name',
                'placeholder' => 'First and last name',
            ],
            'email' => [
                'label' => 'Email address',
                'placeholder' => 'name@example.com',
            ],
            'region' => [
                'label' => 'Geographic connection',
                'options' => [
                    'prompt' => 'Select region',
                    'bakur' => 'Bakur (Northern Kurdistan)',
                    'rojava' => 'Rojava (Western Kurdistan)',
                    'rojhilat' => 'Rojhilat (Eastern Kurdistan)',
                    'bashur' => 'Bashur (Southern Kurdistan)',
                    'europe' => 'Diaspora: Europe',
                    'na' => 'Diaspora: North America',
                    'other' => 'Other location',
                ],
            ],
            'individual_contribution' => [
                'label' => 'How can you contribute?',
                'placeholder' => 'Describe your skills and experience: technical expertise, legal knowledge, research background, organizational experience, translation, funding, or communications.',
            ],
            'org_name' => [
                'label' => 'Organization name',
                'placeholder' => 'Official name of organization or association',
            ],
            'org_contribution' => [
                'label' => 'What can your organization offer?',
                'placeholder' => 'Describe your networks, resources, and preferred form of collaboration.',
            ],
            'org_motive' => [
                'label' => 'Motivation for participation',
                'placeholder' => 'Explain why your organization wishes to participate in this initiative.',
            ],
            'gdpr' => [
                'label' => 'I consent to Count Us Kurds processing my personal data in accordance with the applicable privacy policy.',
            ],
        ],
        'buttons' => [
            'submit' => 'Submit expression of interest',
            'policy' => 'Read privacy policy',
        ],
        'messages' => [
            'invalid_email' => 'Please enter a valid email address.',
            'consent_required' => 'You must accept the privacy policy to continue.',
            'required_fields' => 'Please complete all required fields.',
            'describe_contribution' => 'Please describe how you wish to contribute.',
            'group_required' => 'Organization name, contribution, and motivation are required.',
            'duplicate' => 'This email address is already registered. Please contact us to update your information.',
            'database_error' => 'An error occurred while saving your information. Please try again.',
            'exception' => 'An unexpected error occurred. Please try again or contact us.',
            'csrf_failed' => 'Your session has expired. Please refresh the page and try again.',
            'success' => 'Thank you for your expression of interest. Our team will contact you shortly.',
        ],
    ],
    'footer' => [
        'contact' => 'info@countuskurds.com',
        'tagline' => 'The first independent Kurdish census.',
        'rights' => '© :year Count Us Kurds. An initiative by and for the Kurdish people.',
    ],
    'privacy' => [
        'page_title' => 'Privacy & Data Management',
        'page_description' => 'Your data belongs to you. We protect it with the same determination with which we protect our identity.',
        'language_label' => 'Language',
        'updated' => 'Last updated',
    ],
    'email' => [
        'subject' => 'Confirmation - Count Us Kurds',
        'greeting' => 'Dear :name,',
        'line1' => 'Thank you for registering your interest with Count Us Kurds.',
        'line2' => 'We have received your application and will review how you can contribute to this historic initiative.',
        'auto_reply' => 'This is an automatic confirmation',
        'response_time' => 'Our coordination team will contact you within 5-7 business days.',
        'closing' => 'Kind regards,\nCount Us Kurds',
    ],
];
