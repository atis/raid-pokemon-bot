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

Set `BOT_ACCESS` to the name (@Bot_Access_Groupname) or id (-100123456789) of one or multiple by comma separated groups, supergroups or channels. All administrators (not members!) will gain access to the bot.

When no group, supergroup or channel is specified, the bot will allow everyone to use it (public access).

Example for multiple access groups: `define('BOT_ACCESS', '@Bot_Access_Groupname,@Another_Bot_Access_Group,@Superadmins_Bot_Groups');`

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

To get a new API key, navigate to https://console.developers.google.com/apis/credentials and create a new API project, e.g. raid-telegram-bot

Once the project is created select "API key" from the "Create credentials" dropdown menu - a new API key is created.

After the key is created, you need to activate it for both: Geocoding and Timezone API

Therefore go to "Dashboard" on the left navigation pane and afterwards hit "Enable APIs and services" on top of the page.

Search for Geocoding and Timezone API and enable them. Alternatively use these links to get to Geocoding and Timezone API services:

https://console.developers.google.com/apis/library/timezone-backend.googleapis.com

https://console.developers.google.com/apis/library/geocoding-backend.googleapis.com

Finally check the dashboard again and make sure Google Maps Geocoding API and Google Maps Time Zone API are listed as enabled services. 

## Cleanup

The bot features an automatic cleanup of telegram raid poll messages as well as cleanup of the database (attendance and raids tables).

To activate cleanup you need to change the config and create a cronjob to trigger the cleanup process as follows:

Set the `CLEANUP` in the config to `true` and define a cleanup secret/passphrase under `CLEANUP_SECRET`.

Activate the cleanup of telegram messages and/or the database by setting `CLEANUP_TELEGRAM` / `CLEANUP_DATABASE` to true.

Specify the amount of minutes which need to pass by after raid has ended before the bot executes the cleanup. Times are in minutes in `CLEANUP_TIME_TG` for telegram cleanup and `CLEANUP_TIME_DB` for database cleanup.

The value for the minutes of the database cleanup `CLEANUP_TIME_DB` must be greater than then one for telegram cleanup `CLEANUP_TIME_TG`. Otherwise cleanup will do nothing and exit due to misconfiguration!

Finally set up a cronjob to trigger the cleanup. You can also trigger telegram / database cleanup per cronjob: For no cleanup use 0, for cleanup use 1 and to use your config file use 2 or leave "telegram" and "database" out of the request data array.

A few examples - make sure to replace the URL with yours:

#### Cronjob using cleanup values from config.php: Just the secret without telegram/database OR telegram = 2 and database = 2

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

OR

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase","telegram":"2","database":"2"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

#### Cronjob to clean up telegram messages only: telegram = 1 and database = 0 

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase","telegram":"1","database":"0"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

#### Cronjob to clean up telegram messages and database: telegram = 1 and database = 1

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase","telegram":"1","database":"1"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

#### Cronjob to clean up database and maybe telegram messages (when specified in config): telegram = 2 and database = 1

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase","telegram":"2","database":"1"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

# Access permissions

With BOT_ADMINS and BOT_ACCESS being used to restrict access, there are several access roles / types. When you do not configure BOT_ACCESS, everyone will have access to your bot (public access).  

With your MAINTAINER_ID and as a member of BOT_ADMINS you have the permissions to do anything. **For performance improvements, it's recommended to add the MAINTAINER and all members of BOT_ADMINS as moderator via /mods command!** 

As a member of BOT_ACCESS you can create raid polls, update your own raid polls' pokemon and change the gym team of your last raid poll. BOT_ACCESS members who are moderators can change the gym name and update raid polls' pokemon of others.

Telegram Users can only vote on raid polls, but have no access to other bot functions.


| Access        | Database  | Raid poll |                                      |                  | Pokemon                       |                               | Gym             |                  | Moderators       |                 |                    | Help             |
|---------------|-----------|-----------|--------------------------------------|------------------|-------------------------------|-------------------------------|-----------------|------------------|------------------|-----------------|--------------------|------------------|
|               |           | **Vote**  | **Create** `/start`, `/raid`, `/new` | **List** `/list` | **All raid polls** `/pokemon` | **Own raid polls** `/pokemon` | **Name** `/gym` | **Team** `/team` | **List** `/mods` | **Add** `/mods` | **Delete** `/mods` | **Show** `/help` |
| MAINTAINER_ID |           | Yes       | Yes                                  | Yes              | Yes                           | Yes                           | Yes             | Yes              | Yes              | Yes             | Yes                | Yes              |
| BOT_ADMINS    |           | Yes       | Yes                                  | Yes              | Yes                           | Yes                           | Yes             | Yes              | Yes              | Yes             | Yes                | Yes              |
| BOT_ACCESS    | Moderator | Yes       | Yes                                  | Yes              | Yes                           | Yes                           | Yes             | Yes              |                  |                 |                    | Yes              |
| BOT_ACCESS    | User      | Yes       | Yes                                  | Yes              |                               | Yes                           |                 | Yes              |                  |                 |                    | Yes              |
| Telegram      | User      | Yes       |                                      |                  |                               |                               |                 |                  |                  |                 |                    |                  |

# Usage

## Bot commands
#### Command: No command - just send your location to the bot

The bot will guide you through the creation of the raid poll by asking you for the raid level, the pokemon raid boss, the time until the raids starts and the time left for the raid. Afterwards you can set the gym name and gym team by using the /gym and /team commands.


#### Command: /start

The bot will guide you through the creation of the raid poll by asking you for the gym, raid level, the pokemon raid boss, the time until the raid starts and the time left for the raid. Afterwards you can set the gym team by using the /team command.


#### Command: /help

The bot will answer you "This is a private bot" so you can verify the bot is working and accepting input.


#### Command: /mods

The bot allows you to set some users as moderators. You can list, add and delete moderators from the bot. Note that when you have restricted the access to your bot via BOT_ADMINS and BOT_ACCESS, you need to add the users as administrators of a chat or their Telegram IDs to either BOT_ADMINS or BOT_ACCESS. Otherwise they won't have access to the bot, even though you have added them as moderators! 


#### Command: /raid

Create a new raid by gomap-notifier or other input. The raid command expects 8 parameters and an optional 9th parameter as input seperated by comma.

Additionally the raid command checks for existing raids, so sending the same command multiple times to the bot will result in an update of the pokemon raid boss and gym team and won't create duplicate raids.

Parameters: Pokemon raid boss, latitude, longitude, raid duration in minutes, gym team, gym name, district or street, district or street, raid pre-hatch egg countdown in minutes (optional)

Example input: `/raid Entei,52.514545,13.350095,60,Mystic,Siegessäule,Großer Stern,10557 Berlin,30`


#### Command: /pokemon

Update pokemon of an existing raid poll. With this command you can change the pokemon raid boss from e.g. "Level 5 Egg" to "Lugia" once the egg has hatched.

You can only change the pokemon raid boss of raid polls you created yourself. You cannot modify the pokemon of raid polls from other bot users.


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

* New gyms: Adding gyms to database without creating a raid via /raid
* Preferred pokemon raid boss: When multiple level 5 raids are available, e.g. Lugia and Zapdos, add buttons to tell that you're coming a) only if Lugia, b) only if Zapdos, c) independently of the pokemon
* Delete raids: Allow BOT_ADMINS to delete raids
* Delete incomplete raids automatically: When a bot user starts to create a raid via /start, but does not finish the raid creation, incomplete raid data is stored in the raids table. A method to automatically delete them without interfering with raids just being created would be nice.
