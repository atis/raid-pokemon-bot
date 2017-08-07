<?php

	$msg = '
<b>LV Pamācība, kā izveidot reidu ar raid aptauju</b>

1) pārliecinies, ka reids vēl nav bijis izveidots čatā
2) apskaties cik laiks palicis reidam
3) atver PM ar @RaidPokemonBot 
4) aizsūti lokāciju (obligāti pārbīdi uz vietu kur ir gym)
5) izvēlies kas par reida bosu, un ievadi atlikušo laiku
6) lai atvieglotu atrast pareizo gym gan spēlē, gan čatā, izmantojam funkciju /gym <code>(nosaukums/aprakats)</code> - bez iekavām
7) nospied share, un izvēlies Rīgas Raid sarakste
8) pagaidi līdz izlec papildus izvēle ar bosu ko nupat aizpildīji - uzspied uz tā

<b>EN Guide on how to create a raid poll raid bot</b>
1) make sure the raid hasn\'t been posted yet in the chat
2) check how much time is left for the raid
3) open new PM with @RaidPokemonBot
4) send your location to the bot (make sure you send the location of where the gym is located)
5) choose the type of raid boss and the time left
6) to ensure an easier way to locate the gym in game/chat, it\'s recommended to use the bot function /gym <code>(name of the gym and/or description of it)</code>
7) press share and choose Rīgas Raid sarakste
8) wait until the option with the boss name appears and select it
';
	
	sendMessage('none',$update['message']['from']['id'],$msg);
	