<?php

	define('CR', "\n");
	define('CR2', "\n");
	
//	define('TEAM_B', "&#x1f499;");
//	define('TEAM_R', "&#x2764;");
//	define('TEAM_Y', "&#x1f49B;");
//	define('TEAM_B', "\ud83d\udc99");
//	define('TEAM_R', "\u2764\ufe0f");
//	define('TEAM_Y', "\ud83d\udc9b");
//	define('TEAM_CANCEL', "\ud83d\udc94");
//	define('TEAM_DONE', "\ud83d\udc98");

	define('TEAM_B', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f499)));
	define('TEAM_R', iconv('UCS-4LE', 'UTF-8', pack('V', 0x2764)));
	define('TEAM_Y', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f49B)));
	define('TEAM_CANCEL', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f494)));
	define('TEAM_DONE', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f498)));
	define('TEAM_UNKNOWN', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1f680)));

	define('EMOJI_REFRESH', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F504)));
//	define('EMOJI_EGG', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F95A))); // not showing on TG web
	define('EMOJI_EGG', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F423)));
	define('EMOJI_EGG2', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F373)));
	define('EMOJI_FEET', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F463)));
	define('EMOJI_FACE', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F466)));
	define('EMOJI_PAPERCLIP', iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F4CE)));

	define('RAID_TIME', 45); /* Minutes */
	define('HATCH_TIME', 60); /* Minutes */
	define('DISALLOW_END', 20); /* Minutes */
	define('SUBMIT_INTERVALS', 5); /* Minutes */

$teams = array(
	'mystic' => TEAM_B,
	'valor' => TEAM_R,
	'instinct' => TEAM_Y,
	'unknown' => TEAM_UNKNOWN,
	'cancel' => TEAM_CANCEL,
);

$pokemon = array(
	'5' => array(
		'Articuno',
		'Lugia',
		'Moltres',
		'Zapdos',
	),
	'4' => array(
		'Tyranitar',
		'Snorlax',
		'Lapras',
		'Rhydon',
		'Charizard',
		'Venusaur',
		'Blastoise',
	),
	'3' => array(
		'Machamp',
		'Vaporeon',
		'Flareon',
		'Jolteon',
		'Alakazam',
		'Arcanine',
		'Gengar',
	),
);


