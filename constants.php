<?php
// Carriage return.
define('CR',  "\n");
define('CR2', "\n");

// Icons.
define('TEAM_B',        iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f499)));
define('TEAM_R',        iconv('UCS-4LE', 'UTF-8', pack('V', 0x2764)));
define('TEAM_Y',        iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f49B)));
define('TEAM_CANCEL',   iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f494)));
define('TEAM_DONE',     iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f4aa)));
define('TEAM_UNKNOWN',  iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f680)));
define('EMOJI_REFRESH', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f504)));
define('EMOJI_GROUP',   iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f465)));
define('EMOJI_WARN',    iconv('UCS-4LE', 'UTF-8', pack('V', 0x26A0)));

// Teams.
$teams = array(
    'mystic'    => TEAM_B,
    'valor'     => TEAM_R,
    'instinct'  => TEAM_Y,
    'unknown'   => TEAM_UNKNOWN,
    'cancel'    => TEAM_CANCEL
);

// Raid boss pokemon.
$pokemon = array(
    'X' => array(
	'Mewtu',
    ),
    '5' => array(
        'Arktos',
        'Lugia',
        'Lavados',
        'Zapdos',
        'Mewtu',
        'Mew',
        'Ho-Oh',
        'Celebi',
        'Raikou',
        'Entei',
        'Suicune',
	'Level 5 Ei',
    ),
    '4' => array(
        'Despotar',
        'Relaxo',
        'Lapras',
        'Quappo',
        'Bisasflor',
        'Glurak',
        'Turtok',
        'Sarzenia',
        'Nidoqueen',
        'Nidoking',
        'Rizeros',
        'Geowaz',
        'Absol',
	'Level 4 Ei',
    ),
    '3' => array(
        'Aquana',
        'Flamara',
        'Blitza',
        'Arkani',
        'Machomei',
        'Simsala',
        'Gengar',
        'Sichlor',
        'Porygon',
        'Amoroso',
        'Vulnona',
	'Level 3 Ei',
    )
);


