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
- [X] Server Ping API (i.e. icon only)
- [X] SRV Resolve
- [X] Has paid API
- [X] Web stats
- [X] Name History API
- [ ] Server Query API
- [ ] Support for MCPE servers
- [ ] Server version mapping
- [ ] Plugin API
- [ ] Buycraft API
- [ ] Is up query
- [ ] Multiple Name resolve requests
- [ ] Wait some milliseconds to make concat some requests into a single one
- [ ] Master-Slave support?
- [ ] Random requests
- [ ] UUID -> Name resolve
- [ ] Validate input requests
- [ ] Banner Generator (skin and head)
- [ ] Votifier API
- [ ] Blocked server query
- [ ] Ban API
- [ ] Legacy (Mojang like routes and responses)
- [ ] Mojang Assets, Status API
- [ ] Caching (and cache headers) for images
- [ ] Configurable HTTP-Proxies
- [ ] Unit test the request handling

## Goals

* Workaround rate-limiting by global caching and using http-proxies
* Flexible API (if you have suggestions let us know)
* Developer friendly, clear responses -> i.e. **NOT** returning a cracked player response if there is a issue with our servers
* Open Source
* Open for changes and suggestions
* Simulate a Mojang API out of the box

## Setup

1. Clone this project
2. Run ```composer install``` or as a developer ```composer install --dev```
3. Make a .env for local configuration
4. Check the configuration settings
5. Let your webserver point to the public directory
6. Start the all the necessary servers (i.e. MySQL, memcache, redis, NGINX...)
7. Setup the database structure: ```php artisan migrate```

## Development

Framework documentation: https://lumen.laravel.com/docs/5.2

### Update model documentation

php artisan ide-helper:models
yes

### Update auto complete documentation for IDE

php artisan ide-helper:generate

### Change the database schema (add or change table structure)

php artisan make:migration

Edit the file what should happen if the migration run (Up) and how it can be reverted (Down)

### Credits

Some libraries are used or splitted into modules in order to keep the project simple. Here is the list of lib in use.

* https://github.com/games647/Minecraft-Skin-Renderer (MIT)
* https://github.com/xPaw/PHP-Minecraft-Query/ (MIT)
* https://github.com/Spirit55555/PHP-Minecraft (GPLv3)

### Recommendations

* JSON Browser Plugin
    Chrome: JSON View:  https://chrome.google.com/webstore/detail/jsonview/chklaanhfefbnpoihckbnefhakgolnmc
    Firefox: JSON View: https://addons.mozilla.org/en-US/firefox/addon/10869/

## Routes

* get('/uuid/{name}')
* get('/hasPaid/{username}')

* get('/name-history/{uuid}/all')
* get('/name-history/{uuid}')
* get('/name-history/{uuid}/{time}')

* get('/properties/{uuid}')
* get('/skin/{uuid}')
* get('/avatar/{uuid}')

*  get('/domain/{domain}')

* get('/ping/{domain}')
* get('/ping/{domain}/players')
* get('/ping/{domain}/icon')
* get('/ping/{domain}/motd')
* get('/ping/{domain}/player-count')

* get('/stats')