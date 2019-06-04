<?php
/**
 * Author: David Wofford
 * Class: PhpDarkSky
 * Description: A simple wrapper for calling the dark sky api and returning the data
 * Dark Sky: https://darksky.net/dev
 */

namespace DavidWofford\PhpDarkSky;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\GuzzleException;

final class PhpDarkSky implements PhpDarkSkyConstants
{
    /**
     * @var string The api key for calling the api
     */
    private $apiKey;

    /**
     * @var float The latitude for the location to query
     */
    private $latitude;

    /**
     * @var float The longitude for the location to query
     */
    private $longitude;

    /**
     * @var array The parameters to use when querying
     */
    private $parameters;

    /**
     * PhpDarkSky constructor.
     *
     * @param string $apiKey the api key for dark sky
     * @param float $latitude the latitude for the location
     * @param float $longitude the longitude for the location
     * @param array $parameters the parameters to pass to the api
     *
     * @throws PhpDarkSkyException
     */
    public function __construct(string $apiKey, float $latitude, float $longitude, array $parameters = [])
    {
        // If the API_KEY has not been set we cannot make calls to the api
        if (trim($apiKey) === '') {
            throw new PhpDarkSkyException('api key is required', 1000);
        }

        $this->apiKey    = $apiKey;
        $this->latitude  = $latitude;
        $this->longitude = $longitude;

        $parameters = $this->filterParameters($parameters);
        $this->parameters = $parameters;

        if (!defined('PHP_DARK_SKY_BYPASS_SSL')) {
            define('PHP_DARK_SKY_BYPASS_SSL', false);
        }
    }

    /**
     * Fetches and returns all forecast information for the chosen location
     *
     * @return array the forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getForecast() : array
    {
        return $this->callApi();
    }

    /**
     * Fetches and returns the current forecast for the chosen location
     *
     * @return array the current forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getCurrentForecast() : array
    {
        $data = $this->getForecast();
        return $data[self::CURRENT_KEY] ?? [];
    }

    /**
     * Fetches and returns the minutely forecast for the chosen location
     *
     * @return array the minutely forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getMinutelyForecast() : array
    {
        $data = $this->getForecast();
        return $data[self::MINUTELY_KEY] ?? [];
    }

    /**
     * Fetches and returns the hourly forecast for the chosen location
     *
     * @return array the hourly forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getHourlyForecast() : array
    {
        $data = $this->getForecast();
        return $data[self::HOURLY_KEY] ?? [];
    }

    /**
     * Fetches and returns the daily forecast for the chosen location
     *
     * @return array the daily forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getDailyForecast() : array
    {
        $data = $this->getForecast();
        return $data[self::DAILY_KEY] ?? [];
    }

    /**
     * Fetches and return the alerts for the current location
     *
     * @return array the forecast alerts data
     *
     * @throws PhpDarkSkyException
     */
    public function getForecastAlerts() : array
    {
        $data = $this->getForecast();
        return $data[self::ALERTS_KEY] ?? [];
    }

    /**
     * Fetches and returns the flags for the current location
     *
     * @return array the forecast flags data
     *
     * @throws PhpDarkSkyException
     */
    public function getForecastFlags() : array
    {
        $data = $this->getForecast();
        return $data[self::FLAGS_KEY] ?? [];
    }

    /**
     * Fetches and returns time machine data
     *
     * @param string $time
     *
     * @return array
     *
     * @throws PhpDarkSkyException
     */
    public function getTimeMachine(string $time) : array
    {
        if (trim($time) === '') {
            throw new PhpDarkSkyException('time is required for time machine requests', 1002);
        }
        return $this->callApi($time);
    }

    /**
     * Fetches and returns the current forecast for the chosen location
     *
     * @param string $time
     *
     * @return array the current forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getCurrentTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);
        return $data[self::CURRENT_KEY] ?? [];
    }

    /**
     * Fetches and returns the minutely forecast for the chosen location
     *
     * @param string $time
     *
     * @return array the minutely forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getMinutelyTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);
        return $data[self::MINUTELY_KEY] ?? [];
    }

    /**
     * Fetches and returns the hourly time machine for the chosen location
     *
     * @param string $time
     *
     * @return array the hourly forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getHourlyTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);
        return $data[self::HOURLY_KEY] ?? [];
    }

    /**
     * Fetches and returns the daily time machine for the chosen location
     *
     * @param string $time
     *
     * @return array the daily forecast data
     *
     * @throws PhpDarkSkyException
     */
    public function getDailyTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);
        return $data[self::DAILY_KEY] ?? [];
    }

    /**
     * Handles making calls to the api
     *
     * @param string|null $time the time
     *
     * @return array the array of data
     *
     * @throws PhpDarkSkyException
     */
    private function callApi(?string $time = null) : array
    {
        $time = trim($time) === '' ? '' : ',' . $time;
        $queryString = $this->getQueryString();
        $parameters = $queryString === '' ? '' : '?' . $queryString;

        $client = new Client(
            [
                'base_uri'  => self::API_URL,
                'timeout'   => self::TIMEOUT,
                // This will bypass the ssl cert check, do not turn this on in production
                'verify'    => !PHP_DARK_SKY_BYPASS_SSL
            ]
        );

        $uri = $this->apiKey  . '/' . $this->latitude . ',' . $this->longitude . $time;
        $request = new Request('GET', $uri, [], $parameters);
        try {
            $response = $client->send($request);

            $returnData = json_decode($response->getBody()->getContents(), true);

            if (isset($returnData['error'])) {
                throw new PhpDarkSkyException($returnData['error'], $returnData['code']);
            }

            return $returnData;
        } catch (GuzzleException $e) {
            throw new PhpDarkSkyException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Filters out any unusable parameters and returns what's left
     *
     * @param $parameters
     *
     * @return array
     */
    private function filterParameters($parameters) : array
    {
        $filteredParameters = [];
        $usable = [
            'exclude',
            'extend',
            'lang',
            'units'
        ];

        foreach ($parameters as $key => $value) {
            if (!is_array($value) && in_array($key, $usable, true)) {
                $filteredParameters[$key] = $value;
            }
        }

        return $filteredParameters;
    }

    /**
     * Gets the query string based on the list of parameters
     *
     * @return string
     */
    private function getQueryString() : string
    {
        return http_build_query($this->parameters);
    }
}
