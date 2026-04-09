[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
Add-Type -AssemblyName System.Web

function Translate {
    param(
        [string]$Text,
        [string]$Target
    )

    if ([string]::IsNullOrWhiteSpace($Text)) {
        return $Text
    }

    if ($Target -eq 'en') {
        return $Text
    }

    $encoded = [System.Web.HttpUtility]::UrlEncode($Text)
    $uri = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=$Target&dt=t&q=$encoded"
    $response = $null
    $attempt = 0

    do {
        try {
            $response = Invoke-WebRequest -Uri $uri -UseBasicParsing -TimeoutSec 30
            break
        } catch {
            $attempt++
            if ($attempt -ge 3) {
                throw
            }
            Start-Sleep -Milliseconds 400
        }
    } while ($true)

    $json = $response.Content | ConvertFrom-Json
    $segments = foreach ($segment in $json[0]) { $segment[0] }
    return ($segments -join '')
}

$base = @{
    title = 'Privacy & Data Ownership Policy'
    summary = 'Count Us Kurds collects sensitive civic data to build the first verified global Kurdish census. This policy explains how we store, use, and assert ownership over every submission while keeping it protected.'
    contact = 'Contact us at info@countuskurds.com if you have questions about privacy or data ownership.'
    updated_label = 'Updated'
    sections = @(
        @{ key='mission'; heading='Mission and scope'; body=@(
            'This policy governs everything submitted through CountUsKurds.com, live collection events, and the inbox info@countuskurds.com.',
            'All records serve our mission to document, secure, and politically strengthen Kurds worldwide.'
        ) },
        @{ key='collection'; heading='Information we collect'; intro='Depending on the form you complete we may collect:'; bullets=@(
            'Full name, professional or organisational contact details, and preferred language.',
            'Region, diaspora location, and demographic signals that help us verify representation.',
            'Individual contribution statements, organisational capacity notes, and uploaded references.',
            'Technical metadata (IP, timestamp, device) required for security reviews.'
        ) },
        @{ key='use'; heading='How we use and retain data'; body=@(
            'Authorised coordination staff review submissions to evaluate applicants, plan outreach, design governance, and maintain secure audit trails.',
            'We store encrypted records in EU data centres and retain them until the foundation assembly is complete and for at least ten (10) years afterward unless law requires a longer period.'
        ) },
        @{ key='ownership'; heading='Ownership and permitted use'; body=@(
            'By sending information to Count Us Kurds you grant us irrevocable, perpetual, worldwide, royalty-free rights to use, adapt, analyse, publish, translate, or transfer all submitted content.',
            'We may combine submissions with other trusted datasets, generate statistics, or share excerpts with auditors, partners, or legal advisors while upholding confidentiality promises.'
        ) },
        @{ key='rights'; heading='Your choices and contact'; body=@(
            'You may request a copy, correction, or limited processing of your data by emailing info@countuskurds.com from the address you used.',
            'We will acknowledge requests quickly, but we may retain information necessary for security investigations, legal obligations, or to preserve the integrity of the Kurdish census.'
        ) }
    )
}

$languages = @(
    @{ code='en'; google='en'; dir='ltr'; label='English' },
    @{ code='sv'; google='sv'; dir='ltr'; label='Svenska' },
    @{ code='ku'; google='ku'; dir='ltr'; label='Kurdî (Kurmanji)' },
    @{ code='ckb'; google='ckb'; dir='rtl'; label='کوردی (سۆرانی)' },
    @{ code='ar'; google='ar'; dir='rtl'; label='العربية' },
    @{ code='fa'; google='fa'; dir='rtl'; label='فارسی' },
    @{ code='fr'; google='fr'; dir='ltr'; label='Français' },
    @{ code='de'; google='de'; dir='ltr'; label='Deutsch' },
    @{ code='tr'; google='tr'; dir='ltr'; label='Türkçe' }
)

$results = @{}

foreach ($lang in $languages) {
    $code = $lang.code
    $target = $lang.google
    Write-Host "Translating $code"
    $entry = @{
        language = $lang.label
        dir = $lang.dir
        title = Translate $base.title $target
        summary = Translate $base.summary $target
        contact = Translate $base.contact $target
        updated_label = Translate $base.updated_label $target
        sections = @()
    }

    foreach ($section in $base.sections) {
        $translatedSection = @{
            heading = Translate $section.heading $target
        }

        if ($section.ContainsKey('intro')) {
            $translatedSection.intro = Translate $section.intro $target
        }

        if ($section.ContainsKey('body')) {
            $translatedSection.body = @()
            foreach ($paragraph in $section.body) {
                $translatedSection.body += Translate $paragraph $target
            }
        }

        if ($section.ContainsKey('bullets')) {
            $translatedSection.bullets = @()
            foreach ($bullet in $section.bullets) {
                $translatedSection.bullets += Translate $bullet $target
            }
        }

        $entry.sections += $translatedSection
    }

    $results[$code] = $entry
}

$destination = Join-Path $PSScriptRoot '..\resources\privacy\policy.json'
$results | ConvertTo-Json -Depth 6 | Set-Content -Encoding utf8 -Path $destination
