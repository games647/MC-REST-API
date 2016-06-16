# MC-REST-API

Keep in mind that this project is in beta. All issues should be reported.

## ToDo

- [X] Name->UUID Basic feature
- [X] Cache it for ten minutes
- [X] Cache with configurable amount of time (and visible in the json format)
- [X] Persistent database (visible in the JSON format)
- [X] Configurable different source IPs sending the request to Mojang
- [ ] Skin API
- [ ] Name History API
- [ ] Wait some milliseconds to make concat some requests into a single one
- [ ] Master-Slave support?
- [ ] Random requests
- [ ] UUID -> Name resolve
- [ ] Web stats

## Routes

GET /uuid/{playerName}