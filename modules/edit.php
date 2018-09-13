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
				'text' => 'Mew', 'callback_data' => $id.':edit_poke:mew',
			],[
				'text' => 'Ho-Oh', 'callback_data' => $id.':edit_poke:hooh',
			],[
				'text' => 'Celebi', 'callback_data' => $id.':edit_poke:celebi',
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
		]],[[
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
		]],[[
				'text' => 'Snorlax', 'callback_data' => $id.':edit_poke:snorlax',
			],[
				'text' => 'Lapras', 'callback_data' => $id.':edit_poke:lapras',
			],[
				'text' => 'NidoKing', 'callback_data' => $id.':edit_poke:nidoking',
		]],[[
				'text' => 'Victrebell', 'callback_data' => $id.':edit_poke:victrebell',
			],[
				'text' => 'Poliwrath', 'callback_data' => $id.':edit_poke:poliwrath',
			],[
				'text' => 'NidoQueen', 'callback_data' => $id.':edit_poke:nidoqueen',
		]],[[
				'text' => 'Aggron', 'callback_data' => $id.':edit_poke:aggron',
			],[
				'text' => 'Houndoom', 'callback_data' => $id.':edit_poke:houndoom',
			],[
				'text' => 'Marowak Alola', 'callback_data' => $id.':edit_poke:marowak-alola',
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
		]],[[
				'text' => 'Ninetales', 'callback_data' => $id.':edit_poke:ninetales',
			],[
				'text' => 'Scyther', 'callback_data' => $id.':edit_poke:scyther',
			],[
				'text' => 'Omastar', 'callback_data' => $id.':edit_poke:Omastar',
		]],[[
				'text' => 'Shuckle', 'callback_data' => $id.':edit_poke:shuckle',
			],[
				'text' => 'Solrock', 'callback_data' => $id.':edit_poke:solrock',
			],[
				'text' => 'Lunatone', 'callback_data' => $id.':edit_poke:lunatone',
		]],[[
				'text' => 'Donphan', 'callback_data' => $id.':edit_poke:donphan',
			],[
				'text' => 'Flareon', 'callback_data' => $id.':edit_poke:flareon',
		]]];
	} else if ($data['arg']=='type_2') {
		$keys = 
		[[[
				'text' => 'Sableye', 'callback_data' => $id.':edit_poke:sableye',
/* Removed
		],[
				'text' => 'Muk', 'callback_data' => $id.':edit_poke:muk',
*/
    		],[
				'text' => 'Lileep', 'callback_data' => $id.':edit_poke:lileep',
    		],[
				'text' => 'Mawile', 'callback_data' => $id.':edit_poke:mawile',
		]],[[
				'text' => 'Marowak', 'callback_data' => $id.':edit_poke:marowak',
			],[
				'text' => 'Cloyster', 'callback_data' => $id.':edit_poke:cloyster',
			],[
				'text' => 'Tentacruel', 'callback_data' => $id.':edit_poke:tentacruel',
		]],[[
				'text' => 'Sandslash', 'callback_data' => $id.':edit_poke:sandslash',
			],[
				'text' => 'Weezing', 'callback_data' => $id.':edit_poke:weezing',
			],[
				'text' => 'Magneton', 'callback_data' => $id.':edit_poke:magneton',
		]],[[
				'text' => 'Kabuto', 'callback_data' => $id.':edit_poke:kabuto',
			],[
				'text' => 'Omanyte', 'callback_data' => $id.':edit_poke:omanyte',
			],[
				'text' => 'Anorith', 'callback_data' => $id.':edit_poke:anorith',
		]],[[
				'text' => 'Exegutor Alola', 'callback_data' => $id.':edit_poke:exeggutor-alola',
			],[
				'text' => 'Kirlia', 'callback_data' => $id.':edit_poke:kirlia',
			],[
				'text' => 'Magmar', 'callback_data' => $id.':edit_poke:magmar',
		]]];
	} else if ($data['arg']=='type_1') {
		$keys = 
		[[[
				'text' => 'Charmander', 'callback_data' => $id.':edit_poke:charmander',
			],[
				'text' => 'Makuhita', 'callback_data' => $id.':edit_poke:makuhita',
			],[
				'text' => 'Meditite', 'callback_data' => $id.':edit_poke:meditite',
		]],[[
				'text' => 'Omanyte', 'callback_data' => $id.':edit_poke:omanyte',
			],[
				'text' => 'Wailmer', 'callback_data' => $id.':edit_poke:wailmer',
			],[
				'text' => 'Magikarp', 'callback_data' => $id.':edit_poke:magikarp',
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

