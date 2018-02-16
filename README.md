PHP Wrapper For UptimeRobot.com
==============

This is a basic PHP wrapper for https://uptimerobot.com/api

## Prerequisites
* Configure the $config apiKey
* Must be running PHP >= 5.4
* Format will be JSON & there will be no JSONCallback

## Composer
Add this to your composer.json

```JSON
{
    "require": {
        "ckdarby/php-uptimerobot": "@stable"
    }
}
```

## Example

```PHP
<?php
//Requires composer install to work
require_once(__DIR__.'/vendor/autoload.php');

use UptimeRobot\API;

//Set configuration settings
$config = [
    'apiKey' => 'APIKEY',
    'url' => 'http://api.uptimerobot.com'
];

try {

    //Initalizes API with config options
    $api = new API($config);

    //Define parameters for our getMethod request
    $args = [
        'showTimezone' => 1
    ];

    //Makes request to the getMonitor Method
    $results = $api->request('/getMonitors', $args);

    //Output json_decoded contents
    var_dump($results);

} catch (Exception $e) {
    echo $e->getMessage();
    //Output various debug information
    var_dump($api->debug);
}

```
