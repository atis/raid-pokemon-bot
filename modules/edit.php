<?php

	raid_access_check($update, $data);

	debug_log('raid_edit()');
	debug_log($update);

	$id = $data['id'];

	if ($data['arg']=='type_5') {
		$keys = 
		[[[
				'text' => 'Articuno', 'callback_data' => $id.':edit_poke:articuno',
			],[
				'text' => 'Lugia', 'callback_data' => $id.':edit_poke:lugia',
			],[
				'text' => 'Moltres', 'callback_data' => $id.':edit_poke:moltres',
			],[
				'text' => 'Zapdos', 'callback_data' => $id.':edit_poke:zapdos',
		]],[[
				'text' => 'Mewtwo', 'callback_data' => $id.':edit_poke:mewtwo',
			],[
				'text' => 'Ho-Oh', 'callback_data' => $id.':edit_poke:hooh',
			],[
				'text' => 'Giratina', 'callback_data' => $id.':edit_poke:giratina',
			],[
				'text' => 'Cresselia', 'callback_data' => $id.':edit_poke:cresselia',
		]],[[
				'text' => 'Raikou', 'callback_data' => $id.':edit_poke:raikou',
			],[
				'text' => 'Entei', 'callback_data' => $id.':edit_poke:entei',
			],[
				'text' => 'Suicune', 'callback_data' => $id.':edit_poke:suicune',
			],[
				'text' => 'Groudon', 'callback_data' => $id.':edit_poke:groudon',
		]],[[
				'text' => 'Kyogre', 'callback_data' => $id.':edit_poke:kyogre',
			],[
				'text' => 'Rayquaza', 'callback_data' => $id.':edit_poke:rayquaza',
			],[
				'text' => 'Latias', 'callback_data' => $id.':edit_poke:latias',
			],[
				'text' => 'Latios', 'callback_data' => $id.':edit_poke:latios',
		]],[[
				'text' => 'Regice', 'callback_data' => $id.':edit_poke:regice',
			],[
				'text' => 'Registeel', 'callback_data' => $id.':edit_poke:registeel',
			],[
				'text' => 'Regirock', 'callback_data' => $id.':edit_poke:regirock',
			],[
				'text' => 'Heatran', 'callback_data' => $id.':edit_poke:heatran',
		]],[[
				'text' => 'Azelf', 'callback_data' => $id.':edit_poke:azelf',
			],[
				'text' => 'Mesprit', 'callback_data' => $id.':edit_poke:mesprit',
			],[
				'text' => 'Uxie', 'callback_data' => $id.':edit_poke:uxie',
			],[
				'text' => 'Darkrai', 'callback_data' => $id.':edit_poke:darkrai',
		]],[[
				'text' => 'Palkia', 'callback_data' => $id.':edit_poke:palkia',
			],[
				'text' => 'Dialga', 'callback_data' => $id.':edit_poke:dialga',
			],[
				'text' => 'Cobalion', 'callback_data' => $id.':edit_poke:cobalion',
			],[
				'text' => 'Terrakion', 'callback_data' => $id.':edit_poke:terrakion',
		]],[[
				'text' => 'Virizion', 'callback_data' => $id.':edit_poke:virizion',
			],[
				'text' => 'Tornadus', 'callback_data' => $id.':edit_poke:tornadus',
			],[
				'text' => 'Thundurus', 'callback_data' => $id.':edit_poke:thundurus',
			],[
				'text' => 'Landorus', 'callback_data' => $id.':edit_poke:landorus',
		]],[[
				'text' => 'Reshiram', 'callback_data' => $id.':edit_poke:reshiram',
			],[
				'text' => 'Zekrom', 'callback_data' => $id.':edit_poke:zekrom',
			],[
				'text' => 'Kyurem', 'callback_data' => $id.':edit_poke:kyurem',
			],[
				'text' => 'Deoxys', 'callback_data' => $id.':edit_poke:deoxys',
		]],[[
				'text' => 'Genesect', 'callback_data' => $id.':edit_poke:genesect',
			],[
				'text' => 'Legendary (unknown)', 'callback_data' => $id.':edit_poke:legendary',
		]]];
	
	} else if ($data['arg']=='type_4') {
		$keys = 
		[[[
				'text' => 'Tyranitar', 'callback_data' => $id.':edit_poke:tyranitar',
			],[
				'text' => 'Golem', 'callback_data' => $id.':edit_poke:golem',
			],[
				'text' => 'Absol', 'callback_data' => $id.':edit_poke:absol',
			],[
				'text' => 'Togetic', 'callback_data' => $id.':edit_poke:togetic',
		]],[[
				'text' => 'Dragonite', 'callback_data' => $id.':edit_poke:dragonite',
			],[
				'text' => 'Snorlax', 'callback_data' => $id.':edit_poke:snorlax',
			],[
				'text' => 'Lapras', 'callback_data' => $id.':edit_poke:lapras',
			],[
				'text' => 'NidoKing', 'callback_data' => $id.':edit_poke:nidoking',
		]],[[
				'text' => 'Venusaur', 'callback_data' => $id.':edit_poke:venusaur',
			],[
				'text' => 'Charizard', 'callback_data' => $id.':edit_poke:charizard',
			],[
				'text' => 'Blastoise', 'callback_data' => $id.':edit_poke:blastoise',
			],[
				'text' => 'Excadrill', 'callback_data' => $id.':edit_poke:excadrill',
		]],[[
				'text' => 'Weezing Galar', 'callback_data' => $id.':edit_poke:weezing-galar',
			],[
				'text' => 'Marowak Alola', 'callback_data' => $id.':edit_poke:marowak-alola',
			],[
				'text' => 'Exegutor Alola', 'callback_data' => $id.':edit_poke:exeggutor-alola',
		]]];
	} else if ($data['arg']=='type_3') {
		$keys = 
		[[[
				'text' => 'Machamp', 'callback_data' => $id.':edit_poke:machamp',
			],[
				'text' => 'Aerodactyl', 'callback_data' => $id.':edit_poke:aerodactyl',
			],[
				'text' => 'Raichu Alola', 'callback_data' => $id.':edit_poke:raichu-alola',
		]],[[
				'text' => 'Gengar', 'callback_data' => $id.':edit_poke:gengar',
			],[
				'text' => 'Alakazam', 'callback_data' => $id.':edit_poke:alakazam',
			],[
				'text' => 'Porygon', 'callback_data' => $id.':edit_poke:porygon',
			],[
				'text' => 'Sharpedo', 'callback_data' => $id.':edit_poke:sharpedo',
		]],[[
				'text' => 'Ninetales', 'callback_data' => $id.':edit_poke:ninetales',
			],[
				'text' => 'Scyther', 'callback_data' => $id.':edit_poke:scyther',
			],[
				'text' => 'Granbull', 'callback_data' => $id.':edit_poke:granbull',
		]],[[
				'text' => 'Shuckle', 'callback_data' => $id.':edit_poke:shuckle',
			],[
				'text' => 'Solrock', 'callback_data' => $id.':edit_poke:solrock',
			],[
				'text' => 'Lunatone', 'callback_data' => $id.':edit_poke:lunatone',
			],[
				'text' => 'Feroseed', 'callback_data' => $id.':edit_poke:feroseed',
		]],[[
				'text' => 'Donphan', 'callback_data' => $id.':edit_poke:donphan',
			],[
				'text' => 'Flareon', 'callback_data' => $id.':edit_poke:flareon',
			],[
				'text' => 'Jolteon', 'callback_data' => $id.':edit_poke:jolteon',
			],[
				'text' => 'Skarmony', 'callback_data' => $id.':edit_poke:skarmony',
		]]];
	} else if ($data['arg']=='type_2') {
		$keys = 
		[[[
				'text' => 'Sableye', 'callback_data' => $id.':edit_poke:sableye',
    		],[
				'text' => 'Lileep', 'callback_data' => $id.':edit_poke:lileep',
    		],[
				'text' => 'Mawile', 'callback_data' => $id.':edit_poke:mawile',
    		],[
				'text' => 'Sneasel', 'callback_data' => $id.':edit_poke:sneasel',
		]],[[
				'text' => 'Kabuto', 'callback_data' => $id.':edit_poke:kabuto',
			],[
				'text' => 'Anorith', 'callback_data' => $id.':edit_poke:anorith',
			],[
				'text' => 'Misdreavous', 'callback_data' => $id.':edit_poke:misdreavous',
		]],[[
				'text' => 'Magmar', 'callback_data' => $id.':edit_poke:magmar',
			],[
				'text' => 'Unown-U', 'callback_data' => $id.':edit_poke:unown-u',
			],[
				'text' => 'Unown-L', 'callback_data' => $id.':edit_poke:unown-l',
		]],[[
				'text' => 'Unown-T', 'callback_data' => $id.':edit_poke:unown-t',
			],[
				'text' => 'Unown-R', 'callback_data' => $id.':edit_poke:unown-r',
			],[
				'text' => 'Unown-A', 'callback_data' => $id.':edit_poke:unown-a',
		]]];
	} else if ($data['arg']=='type_1') {
		$keys = 
		[[[
				'text' => 'Makuhita', 'callback_data' => $id.':edit_poke:makuhita',
			],[
				'text' => 'Meditite', 'callback_data' => $id.':edit_poke:meditite',
			],[
				'text' => 'Wailmer', 'callback_data' => $id.':edit_poke:wailmer',
		]],[[
				'text' => 'Omanyte', 'callback_data' => $id.':edit_poke:omanyte',
			],[
				'text' => 'Magikarp', 'callback_data' => $id.':edit_poke:magikarp',
			],[
				'text' => 'Snorunt', 'callback_data' => $id.':edit_poke:snorunt',
		]],[[
				'text' => 'Duskull', 'callback_data' => $id.':edit_poke:duskull',
			],[
				'text' => 'Shuppet', 'callback_data' => $id.':edit_poke:shuppet',
			],[
				'text' => 'Magnemite', 'callback_data' => $id.':edit_poke:magnemite',
		]],[[
				'text' => 'Shinx', 'callback_data' => $id.':edit_poke:shinx',
			],[
				'text' => 'Gibble', 'callback_data' => $id.':edit_poke:gibble',
			],[
				'text' => 'Timburr', 'callback_data' => $id.':edit_poke:timburr',
		]]];
	} else {
		/* Edit pokemon */
		$keys = raid_edit_start_keys();
	}

	if (!$keys) $keys = [[[ 'text' => 'Not supported', 'callback_data' => 'edit:not_supported' ]]];

	if (isset($update['callback_query']['inline_message_id'])) {
		editMessageText($update['callback_query']['inline_message_id'],'Choose Raid Boss:',$keys);
	} else {
		editMessageText($update['callback_query']['message']['message_id'],'Choose Raid Boss',$keys,$update['callback_query']['message']['chat']['id'],$keys);
	}
	
	//edit_message_keyboard($update['callback_query']['message']['message_id'],$keys,$update['callback_query']['message']['chat']['id']);
	$callback_response = 'Ok';
	answerCallbackQuery($update['callback_query']['id'],$callback_response);

