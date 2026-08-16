<?php

return [
    'sequence_width' => 6,
    'hawb_sequence_width' => 3,

    'tracking_prefixes' => [
        'domestic' => 'NPD',
        'ecommerce' => 'NPE',
        'international' => 'NPI',
    ],

    'international_hawb' => [
        'default_prefix' => 'INNP',
        'regions' => [
            'USNP' => ['US', 'USA', 'UNITED STATES', 'UNITED STATES OF AMERICA', 'CA', 'CAN', 'CANADA'],
            'UKNP' => ['GB', 'GBR', 'UK', 'UNITED KINGDOM', 'ENGLAND', 'SCOTLAND', 'WALES', 'NORTHERN IRELAND'],
            'AUNP' => ['AU', 'AUS', 'AUSTRALIA'],
            'EUNP' => [
                'EUROPE', 'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
                'FR', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT',
                'RO', 'SE', 'SI', 'SK', 'IS', 'LI', 'NO', 'CH',
                'AUSTRIA', 'BELGIUM', 'BULGARIA', 'CROATIA', 'CYPRUS', 'CZECHIA',
                'CZECH REPUBLIC', 'GERMANY', 'DENMARK', 'ESTONIA', 'SPAIN', 'FINLAND',
                'FRANCE', 'GREECE', 'HUNGARY', 'IRELAND', 'ITALY', 'LITHUANIA',
                'LUXEMBOURG', 'LATVIA', 'MALTA', 'NETHERLANDS', 'POLAND', 'PORTUGAL',
                'ROMANIA', 'SWEDEN', 'SLOVENIA', 'SLOVAKIA', 'ICELAND',
                'LIECHTENSTEIN', 'NORWAY', 'SWITZERLAND',
            ],
        ],
    ],
];
