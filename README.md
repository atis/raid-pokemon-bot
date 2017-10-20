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

# TODO:

	arrived - set and show time

	create raid - check permissions (in group)
	create raid moderators - allow create any raid
	
	geofencing - auto publish to chat
	timezones
	create api / command
	
