# sakura-api

---

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)  

[![Build Status](https://travis-ci.org/k-kinzal/sakura-api.svg?branch=master)](https://travis-ci.org/k-kinzal/sakura-api)

sakura api stocks json payloads. or the reponse of the stocked json carried out.

## Get started

Click a deploy button. 

## Ukagaka

[うさださくら - http://usada.sakura.vg/]( http://usada.sakura.vg/)(jp)

### Windows

Install [SSP](http://ssp.shillest.net/).

### Mac

Install [Wine Botter](http://winebottler.kronenberg.org/) and [SSP](http://ssp.shillest.net/).

[WineBotterでSSPを起動する with さくら - http://qiita.com/kinzal/items/2103f423c43d9ea5feb9](http://qiita.com/kinzal/items/2103f423c43d9ea5feb9)(jp)

## Webhook

    http://[:hostname]/[:tag-name]

URL is specified. (e.g. ```http://sakura-api.herokuapp.com/github.com/k-kinzal/sakura-api```)
```[:hostname]``` specifies URL which carried out deploy to Heroku.
```[:tag-name]``` should specify an identifier. the character which can be used is set to ```.+```.

## Client

Install [fluentd](http://www.fluentd.org/) and [fluent-plugin-sstp](https://github.com/bash0C7/fluent-plugin-sstp).

````
<source>
  type exec
  command curl -sS http://sakura-api.herokuapp.com/github.com/k-kinzal/sakura-api
  format json
  tag sakura.github.sakura-api
  run_interval 10s
</source>

<match sakura.github.sakura-api>
  type sstp
  sstp_server     127.0.0.1
  sstp_port       9801
  request_method  NOTIFY
  request_version SSTP/1.1
  sender          sakura-api
  script_template \0<%= record['pusher']['name'] %>が<%= record['repository']['full_name'] %>の<%= record['ref'] %>にpushしたよ\w9\w9\u\s[11]これはテストがコケるな\w9\h\s[4]\n\nええー\e
</match>
````