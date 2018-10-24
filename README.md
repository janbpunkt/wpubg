# WPUBG
## What is this thing?
WPUBG is a plugin for WordPress which contacts the PUBG-API to get some basic statistics about a player and present those as a widget.

# IMPORTANT
This plugin contacts https://api.pubg.com with every visitor to get the needed data.
Keep this in mind regarding GDPR/DSGVO.

[[PUBG API Website](https://developer.pubg.com)] - 
[[PUBG API Privacy Policy](https://developer.pubg.com/privacy_policy)] - 
[[PUBG API TOS](https://developer.pubg.com/tos?locale=en)]

## What do I need to use it?
* a working Wordpress installation
* a working [PUBG API key](https://developer.pubg.com)
* your in-game nickname

## Installation
1. upload zip to your plugin-folder or install it from the WordPress Library
2. activate the plugin
3. go to the widgets area and place the widget where you want it to show up
4. fill out all fields
5. save
6. test

## Known issues / to do
1. no error catching yet
2. PUBG-API only allows 10 requests per minute as a default (you can request higher amounts on https://developer.pubg.com)
3. no caching yet

## Changelog
# 0.41
* bugfixes

# 0.4
* first version of error catching

# 0.3
* added regions (you must select your region immediately after updating the plugin)

# 0.2
* added screenshots to readme.txt
* The field "gamemode" now shows the last saved value
 
# 0.1
* first deploy of the plugin
