# MC-REST-API

Keep in mind that this project is in beta. So there could be breaking changes.

All issues should be reported.

## ToDo

- [ ] Website Documentation with live testing
- [X] Name->UUID Basic feature
- [X] Cache it for ten minutes
- [X] Cache with configurable amount of time (and visible in the json format)
- [X] Persistent database (visible in the JSON format)
- [X] Configurable different source IPs sending the request to Mojang
- [X] Skin API
- [ ] Avater API Skin/Head
- [ ] Server Query API (with MCPE and i.e. icon only)
- [ ] Buycraft API
- [X] SRV Resolve
- [ ] Has paid API
- [ ] Is up query
- [ ] Name History API
- [ ] Multiple Name resolve requests
- [ ] Wait some milliseconds to make concat some requests into a single one
- [ ] Master-Slave support?
- [ ] Random requests
- [ ] UUID -> Name resolve
- [ ] Web stats
- [ ] Validate input requests
- [ ] Banner Generator (skin and head)
- [ ] Votifier API
- [ ] Blocked server query
- [ ] Ban API
- [ ] Mojang Assets, Status API

## Setup

1. Clone this project
2. Make a .env for local configuration
3. Check the configuration settings
4. Let your webserver point to the public directory
5. Start the all the necessary servers (i.e. MySQL, memcache, redis, NGINX...)

## Development

Framework documentation: https://lumen.laravel.com/docs/5.2

### Recommendations

* JSON Browser Plugin
Chrome: JSON View:  https://chrome.google.com/webstore/detail/jsonview/chklaanhfefbnpoihckbnefhakgolnmc

Firefox: JSON View: https://addons.mozilla.org/en-US/firefox/addon/10869/

## Routes

GET /uuid/{playerName}

GET /skin/{uuid}

GET /domain/{domain}