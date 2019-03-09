# PHP Dark Sky

A simple wrapper for connecting to and pulling information from the [Dark Sky](https://darksky.net) api

## Features

- An easy to use wrapper for connecting to the dark sky api to get weather data
- Methods to get filtered down data instead of 1 massive blob

## Notes

- All data is returned as associative arrays
- Errors from the Dark Sky api are returned as exceptions

## Requirements

- PHP >= 7.2
- [A Dark Sky api key](https://darksky.net/dev/register)

## Installation

### Composer

To install through composer add the following line to your `composer.json` file:
```
    "require": {
        "davidwofford/phpdarksky": "1.1.*"
    }
```
or run this command
```
    composer require davidwofford/phpdarksky
```

### Copy

If you do not wish to use composer, copy the PhpDarkSky directory to your library / vendor folder and add:

```php
    include "[vendor / library directory]/phpdarksky/src/PhpDarkSky.php";
```

## Usage

### Get the forecast

To get all of the forecast data for a location
```php
    $darkSky = new PhpDarkSky('[API KEY]', '[LATITUDE]', '[LONGITUDE]');
    
    try {
        $foreacast = $darkSky->getForecast();
    } catch (\Exception $e) {
        // Handle the exception
    }
```

### Get the current forecast only
This will return data that would be in the `currently` array from `getForecast`

```php
    $darkSky = new PhpDarkSky('[API KEY]', '[LATITUDE]', '[LONGITUDE]');
    
    try {
        $foreacast = $darkSky->getCurrentForecast();
    } catch (\Exception $e) {
        // Handle the exception
    }
```

There is a similar function for all of the other arrays that appear in `getForecast` as well.

### Get the time machine data

To get the time machine data for a location

```php
    $darkSky = new PhpDarkSky('[API KEY]', '[LATITUDE]', '[LONGITUDE]');
    
    try {
        $foreacast = $darkSky->getTimeMachine('[UNIX TIMESTAMP]');
    } catch (\Exception $e) {
        // Handle the exception
    }
```

As with the forecast items above there is a method to get filtered down arrays for each of the array items that appear from this call as well.

### Configuration
If you are having issues with your ssl cert being denied locally you can add this define in your project to bypass the ssl cert check.

`define('PHP_DARK_SKY_BYPASS_SSL', true);`

**DO NOT TURN THIS ON IN PRODUCTION**

## Resources

- [Dark Sky api documentation](https://darksky.net/dev/docs)
