# Installation

## Webserver

Preferrably apache2 with php7 and https certificate ( https://www.letsencrypt.org )

## Config

Copy config.php.example to config.php and edit (values explained further)

## Log files

Create log dir, e.g. /var/log/tg-bots/ and set it writeable by webserver

Edit config.php and set `CONFIG_LOGFILE`

## Bot token

Start chat with https://t.me/BotFather and create bot token.

Bot Settings: Enable Inline mode, Allow Groups, Group Privacy off

Use https://www.miniwebtool.com/sha512-hash-generator/ and set `CONFIG_HASH` to hashed value of your token (make sure it is lowecase)

## Bot access

Set `BOT_ACCESS` to the name (@Bot_Access_Groupname) or id (-100123456789) of group, supergroup or channel. All administrators (not members!) will gain access to the bot.

When no group, supergroup or channel is specified, the bot will allow everyone to use it (public access).

## Raid times

There are several options to configure the times related to the raid polls:

Set `RAID_LOCATION` to true to send back the location as message in addition to the raid poll.

Set `RAID_SLOTS` to the amount of minutes which shall be between the voting slots.

Set `RAID_LAST_START` to the minutes for the last start option before the a raid ends.

## Proxy

In case you are running the bot behind a proxy server, set `CURL_USEPROXY` to `true`.

Add the proxy server address and port at `CURL_PROXYSERVER`.

Authentication against the proxy server by username and password is currently not supported.

## Database

Create database named for your bot ID (first part of your Telegram bot token)

Set database password to second part of your TG bot token

Only allow localhost access

Import `raid-pokemon-bot.sql` as default DB structure

## Webhooks

Set Telegram webhook via https://your-hostname/bot-dir/webhooks.html

## Google maps API

Optionally you can you use Google maps API to lookup addresses of gyms based on latitude and longitude

Therefore get a Google maps API key and set it as `GOOGLE_API_KEY` in your config.

Activate it for both Geocoding and Time Zone API

https://developers.google.com/maps/documentation/timezone/get-api-key

https://developers.google.com/maps/documentation/geocoding/start#get-a-key

# Usage

## Bot commands
#### Command: No command - just send your location to the bot

The bot will guide you through the creation of the raid poll by asking you for the raid level, the pokemon raid boss, the time until the raids starts and the time left for the raid. Afterwards you can set the gym name and gym team by using the /gym and /team commands.


#### Command: /start

The bot will guide you through the creation of the raid poll by asking you for the gym, raid level, the pokemon raid boss, the time until the raid starts and the time left for the raid. Afterwards you can set the gym team by using the /team command.


#### Command: /help

The bot will answer you "This is a private bot" so you can verify the bot is working and accepting input.


#### Command: /raid

Create a new raid by gomap-notifier or other input. The raid command expects 8 parameters and an optional 9th parameter as input seperated by comma.

Additionally the raid command checks for existing raids, so sending the same command multiple times to the bot will result in an update of the pokemon raid boss and gym team and won't create duplicate raids.

Parameters: Pokemon raid boss, latitude, longitude, raid duration in minutes, gym team, gym name, district or street, district or street, raid pre-hatch egg countdown in minutes (optional)

Example input: `/raid Entei,52.514545,13.350095,60,Mystic,Siegessäule,Großer Stern,10557 Berlin,30`


#### Command: /new

The bot expects latitude and longitude seperated by comma and will then guide you through the creation of the raid poll.

This command was implemented since the Telegram Desktop Client does not allow to share a location currently.

Example input: `/new 52.514545,13.350095`


#### Command: /list 

The bot will send you a list of the last 20 active raids.


#### Command: /team

The bot will set the team to Mystic/Valor/Instinct for the last created raid based on your input.

Example input: `/team Mystic`


#### Command: /gym

The bot will set the name of gym to your input.

Example input: `/gym Siegessäule`

# Debugging
Check your bot logfile and other related log files, e.g. apache/httpd log, php log, and so on.

# TODO:

* Cleanup logic to delete raid polls once a raid ended.

	arrived - set and show time

	create raid - check permissions (in group)
	create raid moderators - allow create any raid
	
	geofencing - auto publish to chat
	timezones
	create api / command
