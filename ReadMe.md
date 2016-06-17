# MC-REST-API

Keep in mind that this project is in beta. So there could be breaking changes.

All issues should be reported.

## ToDo

- [ ] Website Documentation with live testing (Searching for help for this)
- [X] Name->UUID Basic feature
- [X] Cache it for ten minutes
- [X] Cache with configurable amount of time (and visible in the json format)
- [X] Persistent database (visible in the JSON format)
- [X] Configurable different source IPs sending the request to Mojang
- [X] Skin API
- [X] Avater API Skin/Head
- [ ] Server Query API (with MCPE and i.e. icon only)
- [ ] Buycraft API
- [X] SRV Resolve
- [X] Has paid API
- [ ] Is up query
- [ ] Name History API
- [ ] Multiple Name resolve requests
- [ ] Wait some milliseconds to make concat some requests into a single one
- [ ] Master-Slave support?
- [ ] Random requests
- [ ] UUID -> Name resolve
- [X] Web stats
- [ ] Validate input requests
- [ ] Banner Generator (skin and head)
- [ ] Votifier API
- [ ] Blocked server query
- [ ] Ban API
- [ ] Mojang Assets, Status API
- [ ] Caching (and cache headers) for images

## Goals

* Workaround rate-limiting by global caching
* Flexible API (if you have suggestions let us know)
* Developer friendly responses -> i.e. return a cracked player response if there is a issue with our servers
* Open Source
* Open for changes and suggestions
* Simulate a Mojang API out of the box

## Setup

1. Clone this project
2. Make a .env for local configuration
3. Check the configuration settings
4. Let your webserver point to the public directory
5. Start the all the necessary servers (i.e. MySQL, memcache, redis, NGINX...)

## Development

Framework documentation: https://lumen.laravel.com/docs/5.2

## Credits

Some libraries are used or splitted into modules in order to keep the project simple. Here is the list of lib in use.

* https://github.com/games647/Minecraft-Skin-Renderer

### Recommendations

* JSON Browser Plugin
Chrome: JSON View:  https://chrome.google.com/webstore/detail/jsonview/chklaanhfefbnpoihckbnefhakgolnmc

Firefox: JSON View: https://addons.mozilla.org/en-US/firefox/addon/10869/

## Routes

GET /uuid/{playerName}

GET /skin/{uuid}

GET /domain/{domain}