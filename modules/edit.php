<?php

	raid_access_check($update, $data);

	debug_log('raid_edit()');
	debug_log($update);

	$id = $data['id'];

	if ($data['arg']=='type_5') {
		$keys = 
		[[
		    [
				'text' => 'Articuno', 'callback_data' => $id.':edit_poke:articuno',
			],[
				'text' => 'Lugia', 'callback_data' => $id.':edit_poke:lugia',
			],[
				'text' => 'Moltres', 'callback_data' => $id.':edit_poke:moltres',
			],[
				'text' => 'Zapdos', 'callback_data' => $id.':edit_poke:zapdos',
		]],[
		[
				'text' => 'Mewtwo', 'callback_data' => $id.':edit_poke:mewtwo',
			],[
				'text' => 'Mew', 'callback_data' => $id.':edit_poke:mew',
			],[
				'text' => 'Ho-Oh', 'callback_data' => $id.':edit_poke:hooh',
			],[
				'text' => 'Celebi', 'callback_data' => $id.':edit_poke:celebi',
		]],[
		    [
				'text' => 'Raikou', 'callback_data' => $id.':edit_poke:raikou',
			],[
				'text' => 'Entei', 'callback_data' => $id.':edit_poke:entei',
			],[
				'text' => 'Suicune', 'callback_data' => $id.':edit_poke:suicune',
		]],[[
				'text' => 'LVL5 Onbekend', 'callback_data' => $id.':edit_poke:Legendary',
		]]];
	
	} else if ($data['arg']=='type_4') {
		$keys = 
		[[[
				'text' => 'Tyranitar', 'callback_data' => $id.':edit_poke:tyranitar',
		],[
				'text' => 'Golem', 'callback_data' => $id.':edit_poke:Golem',
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
		]]];
	} else if ($data['arg']=='type_3') {
		$keys = 
		[[[
				'text' => 'Machamp', 'callback_data' => $id.':edit_poke:machamp',
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
		]]];
	} else if ($data['arg']=='type_2') {
		$keys = 
		[[[
				'text' => 'Sableye', 'callback_data' => $id.':edit_poke:sableye',
		],[
				'text' => 'Muk', 'callback_data' => $id.':edit_poke:muk',
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
				'text' => 'Magenton', 'callback_data' => $id.':edit_poke:magneton',
		]]];
	} else if ($data['arg']=='type_1') {
		$keys = [[[ 'text' => 'Not supported', 'callback_data' => 'edit:not_supported' ]]];
	} else {
		/* Edit pokemon */
		$keys = raid_edit_start_keys();
	}

	if (!$keys) $keys = [[[ 'text' => 'Not supported', 'callback_data' => 'edit:not_supported' ]]];

	if (isset($update['callback_query']['inline_message_id'])) {
		editMessageText($update['callback_query']['inline_message_id'],'Kies Raid Pokemon:',$keys);
	} else {
		editMessageText($update['callback_query']['message']['message_id'],'Kies Raid Pokemon',$keys,$update['callback_query']['message']['chat']['id'],$keys);
	}
	
	//edit_message_keyboard($update['callback_query']['message']['message_id'],$keys,$update['callback_query']['message']['chat']['id']);
	$callback_response = 'Ok';
	answerCallbackQuery($update['callback_query']['id'],$callback_response);

