# Installing

## Webserver

Preferrably apache2 with php7 and https certificate ( https://www.letsencrypt.org )

## Config

Copy config.php.example and edit (values explained further)

## Log files

Create dir /var/log/tg-bots/ and set it writeable by webserver
edit config.php and set `CONFIG_LOGFILE`

## Bot token

Start chat with https://t.me/BotFather and create bot token.
Enable Inline mode, Allow Groups, Group Privacy off

Use http://passwordsgenerator.net/sha512-hash-generator/ and set `CONFIG_HASH` to hashed value of your token

## Database

Create database named for your bot ID (first part of your TG bot token)
Set database password to second part of your TG bot token
Only allow localhost access
Import `raid-pokemon-bot.sql` as default DB structure

## Webhooks

Use https://your-hostname/bot-dir/setup.php

## Google maps API

Get maps API key, and set as `TZ_API_KEY` in your config.

