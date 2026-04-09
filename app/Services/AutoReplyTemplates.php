<?php
declare(strict_types=1);

namespace CountUsKurds\Services;

class AutoReplyTemplates
{
    private static array $templates = [
        'en' => [
            'subject' => 'Thank you for your application – Count Us Kurds',
            'greeting' => 'Dear :name,',
            'line1' => 'Thank you for your interest in joining the Count Us Kurds Foundation Team.',
            'line2' => 'We have received your application and will carefully review your proposed contribution.',
            'line3' => 'Our coordination team will respond within 5–7 working days.',
            'closing' => 'Best regards,',
            'team' => 'The Count Us Kurds Team'
        ],
        'sv' => [
            'subject' => 'Tack för din ansökan – Count Us Kurds',
            'greeting' => 'Kära :name,',
            'line1' => 'Tack för ditt intresse att gå med i Count Us Kurds grundteam.',
            'line2' => 'Vi har tagit emot din ansökan och kommer noggrant granska ditt föreslagna bidrag.',
            'line3' => 'Vårt samordningsteam återkommer inom 5–7 arbetsdagar.',
            'closing' => 'Med vänliga hälsningar,',
            'team' => 'Count Us Kurds-teamet'
        ],
        'ku' => [
            'subject' => 'Spas ji bo serlêdana te – Count Us Kurds',
            'greeting' => ':name hêja,',
            'line1' => 'Spas ji bo balkêşiya te ku tu dixwazî beşdarî tîma bingehîn a Count Us Kurds bibî.',
            'line2' => 'Me serlêdana te wergirt û em ê beşdariya te ya pêşniyarkirî bi baldarî lêkolîn bikin.',
            'line3' => 'Tîma rêvebiriya me dê di nav 5-7 rojên karî de bi te re têkilî dayne.',
            'closing' => 'Bi rêzên xwe,',
            'team' => 'Tîma Count Us Kurds'
        ],
        'ckb' => [
            'subject' => 'سوپاس بۆ داواکاریەکەت – Count Us Kurds',
            'greeting' => ':name بەڕێز,',
            'line1' => 'سوپاس بۆ ئارەزووت بۆ پەیوەستبوون بە تیمی بنگەهەی Count Us Kurds.',
            'line2' => 'ئێمە داواکارییەکەمان وەرگرت و بە وردی پێداچوونەوەی بەشداریی پێشنیارکراوت دەکەین.',
            'line3' => 'تیمی هاوکارییەکانمان لە ماوەی 5-7 ڕۆژی کاری پەیوەندیت پێوە دەگرێت.',
            'closing' => 'لەگەڵ ڕێزدا,',
            'team' => 'تیمی Count Us Kurds'
        ],
        'ar' => [
            'subject' => 'شكراً لطلبك – Count Us Kurds',
            'greeting' => 'عزيزي :name،',
            'line1' => 'شكراً لاهتمامك بالانضمام إلى فريق التأسيس في Count Us Kurds.',
            'line2' => 'لقد تلقينا طلبك وسنراجع مساهمتك المقترحة بعناية.',
            'line3' => 'سيرد فريق التنسيق خلال 5-7 أيام عمل.',
            'closing' => 'مع أطيب التحيات,',
            'team' => 'فريق Count Us Kurds'
        ],
        'fa' => [
            'subject' => 'سپاس از درخواست شما – Count Us Kurds',
            'greeting' => ':name گرامی،',
            'line1' => 'سپاس از علاقه شما به پیوستن به تیم بنیادگذار Count Us Kurds.',
            'line2' => 'ما درخواست شما را دریافت کرده‌ایم و مشارکت پیشنهادی شما را با دقت بررسی خواهیم کرد.',
            'line3' => 'تیم هماهنگی ما ظرف 5-7 روز کاری پاسخ خواهد داد.',
            'closing' => 'با احترام,',
            'team' => 'تیم Count Us Kurds'
        ],
        'fr' => [
            'subject' => 'Merci pour votre candidature – Count Us Kurds',
            'greeting' => 'Cher(e) :name,',
            'line1' => 'Merci pour votre intérêt à rejoindre l\'équipe fondatrice de Count Us Kurds.',
            'line2' => 'Nous avons bien reçu votre candidature et examinerons attentivement votre contribution proposée.',
            'line3' => 'Notre équipe de coordination vous répondra dans les 5 à 7 jours ouvrables.',
            'closing' => 'Cordialement,',
            'team' => 'L\'équipe Count Us Kurds'
        ],
        'de' => [
            'subject' => 'Danke für Ihre Bewerbung – Count Us Kurds',
            'greeting' => 'Liebe(r) :name,',
            'line1' => 'Vielen Dank für Ihr Interesse, dem Gründungsteam von Count Us Kurds beizutreten.',
            'line2' => 'Wir haben Ihre Bewerbung erhalten und werden Ihren vorgeschlagenen Beitrag sorgfältig prüfen.',
            'line3' => 'Unser Koordinationsteam wird sich innerhalb von 5-7 Werktagen bei Ihnen melden.',
            'closing' => 'Mit freundlichen Grüßen,',
            'team' => 'Das Count Us Kurds Team'
        ],
        'tr' => [
            'subject' => 'Başvurunuz için teşekkürler – Count Us Kurds',
            'greeting' => 'Sayın :name,',
            'line1' => 'Count Us Kurds kurucu ekibine katılmak için gösterdiğiniz ilgi için teşekkür ederiz.',
            'line2' => 'Başvurunuzu aldık ve önerdiğiniz katkıyı dikkatle inceleyeceğiz.',
            'line3' => 'Koordinasyon ekibimiz 5-7 iş günü içinde yanıt verecektir.',
            'closing' => 'Saygılarımızla,',
            'team' => 'Count Us Kurds Ekibi'
        ]
    ];
    
    public static function getTemplate(string $locale): array
    {
        return self::$templates[$locale] ?? self::$templates['en'];
    }
    
    public static function buildEmail(string $locale, string $name): string
    {
        $t = self::getTemplate($locale);
        $name = htmlspecialchars($name);
        
        $greeting = str_replace(':name', $name, $t['greeting']);
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:24px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#ed1c24,#ff684f);padding:32px;color:#fff;text-align:center;">
                            <img src="https://countuskurds.com/assets/img/count-us-kurds-logo.png" alt="Count Us Kurds" style="width:80px;height:80px;border-radius:12px;background:#fff;padding:8px;margin-bottom:16px;">
                            <h1 style="margin:0;font-size:24px;font-weight:800;">Count Us Kurds</h1>
                            <p style="margin:8px 0 0;font-size:14px;opacity:0.9;">Foundation Team</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px;font-size:16px;font-weight:600;">{$greeting}</p>
                            <p style="margin:0 0 12px;font-size:16px;line-height:1.6;">{$t['line1']}</p>
                            <p style="margin:0 0 12px;font-size:16px;line-height:1.6;">{$t['line2']}</p>
                            <p style="margin:0 0 24px;font-size:16px;line-height:1.6;">{$t['line3']}</p>
                            <p style="margin:0;font-size:14px;color:#666;">{$t['closing']}<br><strong>{$t['team']}</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#111;color:#fff;padding:20px 32px;text-align:center;">
                            <p style="margin:0;font-size:14px;">📧 info@countuskurds.com</p>
                            <p style="margin:8px 0 0;font-size:12px;opacity:0.7;">www.countuskurds.com</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
    
    public static function getAdminReplyTemplates(string $locale): array
    {
        $templates = [
            'en' => [
                'accept' => [
                    'subject' => 'Welcome to Count Us Kurds Foundation Team',
                    'body' => "We are pleased to inform you that your application has been accepted. Your expertise and commitment are valuable to our initiative.\n\nNext steps:\n- We will contact you shortly with more information\n- You will receive access to our coordination channels\n- An introductory meeting will be scheduled\n\nWelcome to the team!"
                ],
                'more_info' => [
                    'subject' => 'Additional Information Required - Count Us Kurds',
                    'body' => "We have reviewed your application and would like to receive some additional information to make a complete assessment:\n\n1. [Question 1]\n2. [Question 2]\n3. [Question 3]\n\nWe would appreciate if you could respond within one week."
                ],
                'reject' => [
                    'subject' => 'Regarding Your Application - Count Us Kurds',
                    'body' => "We sincerely appreciate your interest and commitment. After careful consideration, we have decided not to proceed with your application at this time.\n\nThis does not mean that your expertise is not valuable - we simply have many applications and need to prioritize based on our current needs.\n\nWe encourage you to:\n- Follow our work at countuskurds.com\n- Consider applying again in the future\n- Share information about the initiative in your network"
                ]
            ],
            'sv' => [
                'accept' => [
                    'subject' => 'Välkommen till Count Us Kurds Foundation Team',
                    'body' => "Vi är glada att meddela att din ansökan har accepterats. Din kompetens och engagemang är värdefulla för vårt initiativ.\n\nNästa steg:\n- Vi kommer att kontakta dig inom kort med mer information\n- Du kommer att få tillgång till våra samordningskanaler\n- Ett introduktionsmöte kommer att schemaläggas\n\nVälkommen till teamet!"
                ],
                'more_info' => [
                    'subject' => 'Ytterligare information krävs - Count Us Kurds',
                    'body' => "Vi har granskat din ansökan och skulle vilja få lite mer information för att kunna göra en fullständig bedömning:\n\n1. [Fråga 1]\n2. [Fråga 2]\n3. [Fråga 3]\n\nVi uppskattar om du kan återkomma med denna information inom en vecka."
                ],
                'reject' => [
                    'subject' => 'Angående din ansökan - Count Us Kurds',
                    'body' => "Vi uppskattar verkligen ditt intresse och engagemang. Efter noggrann övervägning har vi dock beslutat att inte gå vidare med din ansökan just nu.\n\nDetta innebär inte att din kompetens inte är värdefull - vi har helt enkelt många ansökningar och behöver prioritera baserat på våra aktuella behov.\n\nVi uppmuntrar dig att:\n- Följa vårt arbete på countuskurds.com\n- Överväga att ansöka igen i framtiden\n- Sprida information om initiativet i ditt nätverk"
                ]
            ]
        ];
        
        return $templates[$locale] ?? $templates['en'];
    }
}
