<?php

define('CR', "\n");
define('CR2', "\n");

define('TEAM_B', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f499)));
define('TEAM_R', iconv('UCS-4LE', 'UTF-8', pack('V', 0x2764)));
define('TEAM_Y', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f49B)));
define('TEAM_CANCEL', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f494)));
define('TEAM_DONE', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f4aa)));
define('TEAM_UNKNOWN', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f680)));

define('EMOJI_REFRESH', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F504)));

$teams = array(
    'mystic' => TEAM_B,
    'valor' => TEAM_R,
    'instinct' => TEAM_Y,
    'unknown' => TEAM_UNKNOWN,
    'cancel' => TEAM_CANCEL
);

$pokemon = array(
    '5' => array(
        'Arktos',
        'Lugia',
        'Lavados',
        'Zapdos',
    ),
    '4' => array(
        'Despotar',
        'Relaxo',
        'Lapras',
        'Rizeros',
        'Glurak',
        'Bisasflor',
        'Turtok',
    ),
    '3' => array(
        'Machamp',
        'Vaporeon',
        'Flareon',
        'Jolteon',
        'Alakazam',
        'Arcanine',
        'Gengar',
    )
);


