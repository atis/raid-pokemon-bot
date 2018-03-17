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
        getTranslation('mewtwo')
    ),
    '5' => array(
//        getTranslation('articuno'),
        getTranslation('lugia'),
//        getTranslation('moltres'),
//        getTranslation('zapdos'),
//        getTranslation('mew'),
        getTranslation('mewtwo'),
//        getTranslation('hooh'),
//        getTranslation('celebi'),
//        getTranslation('raikou'),
//        getTranslation('entei'),
//        getTranslation('suicune'),
//        getTranslation('groudon'),
//        getTranslation('rayquaza'),
//        getTranslation('kyogre'),
//        getTranslation('latios'),
//        getTranslation('latias'),
//        getTranslation('deoxys'),
//        getTranslation('jirachi'),
//        getTranslation('regirock'),
//        getTranslation('regice'),
//        getTranslation('registeel'),
        getTranslation('egg_5')
    ),
    '4' => array(
        getTranslation('tyranitar'),
        getTranslation('absol'),
        getTranslation('aggron'),
        getTranslation('golem'),
        getTranslation('egg_4')
    ),
    '3' => array(
        getTranslation('machamp'),
        getTranslation('jynx'),
        getTranslation('jolteon'),
        getTranslation('gengar'),
        getTranslation('egg_3')
    ),
    '2' => array(
        getTranslation('egg_2')
    ),
    '1' => array(
        getTranslation('egg_1')
    )
);

