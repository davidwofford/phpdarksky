<?php
/**
 * Author: David Wofford
 * Class: PhpDarkSky
 * Description: A simple wrapper for calling the dark sky api and returning the data
 * Dark Sky: https://darksky.net/dev
 */

namespace DavidWofford\PhpDarkSky;

final class PhpDarkSky
{
    /**
     * @const string Stores the base url to call for the api
     */
    const API_URL = 'https://api.darksky.net/forecast/';

    /**
     * @const int Stores the timeout time for the api call
     */
    const TIMEOUT = 10;

    /**
     * @const string Stores the array key for current items
     */
    const CURRENT_KEY = 'currently';

    /**
     * @const string Stores the array key for minutely items
     */
    const MINUTELY_KEY = 'minutely';

    /**
     * @const string Stores the array key for hourly items
     */
    const HOURLY_KEY = 'hourly';

    /**
     * @const string Stores the array key for daily items
     */
    const DAILY_KEY = 'daily';

    /**
     * @const string Stores the array key for the alerts
     */
    const ALERTS_KEY = 'alerts';

    /**
     * @const string Stores the array key for the flags
     */
    const FLAGS_KEY = 'flags';

    /**
     * @var string The api key for calling the api
     */
    private $url;

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
     * @param string $apiKey the api key for dark sky
     * @param float $latitude the latitude for the location
     * @param float $longitude the longitude for the location
     * @param array $parameters the parameters to pass to the api
     * @throws \Exception if any of the required parameters is missing an exception is thrown
     */
    public function __construct(string $apiKey, float $latitude, float $longitude, array $parameters = [])
    {
        // If the API_KEY has not been set we cannot make calls to the api
        if ($apiKey === '' || $apiKey === null) {
            throw new \Exception('api key is required', 1000);
        }

        if ($latitude === null || $longitude === null) {
            throw new \Exception('latitude and longitude are required', 1001);
        }

        $this->setUrl(self::API_URL . $apiKey);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);

        $parameters = $this->filterParameters($parameters);
        $this->setParameters($parameters);
    }

    /**
     * Fetches and returns all forecast information for the chosen location
     * @return array the forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getForecast() : array
    {
        return $this->callApi();
    }

    /**
     * Fetches and returns the current forecast for the chosen location
     * @return array the current forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getCurrentForecast() : array
    {
        $data = $this->getForecast();

        return isset($data[self::CURRENT_KEY]) ? $data[self::CURRENT_KEY] : [];
    }

    /**
     * Fetches and returns the minutely forecast for the chosen location
     * @return array the minutely forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getMinutelyForecast() : array
    {
        $data = $this->getForecast();

        return isset($data[self::MINUTELY_KEY]) ? $data[self::MINUTELY_KEY] : [];
    }

    /**
     * Fetches and returns the hourly forecast for the chosen location
     * @return array the hourly forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getHourlyForecast() : array
    {
        $data = $this->getForecast();

        return isset($data[self::HOURLY_KEY]) ? $data[self::HOURLY_KEY] : [];
    }

    /**
     * Fetches and returns the daily forecast for the chosen location
     * @return array the daily forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getDailyForecast() : array
    {
        $data = $this->getForecast();

        return isset($data[self::DAILY_KEY]) ? $data[self::DAILY_KEY] : [];
    }

    /**
     * Fetches and return the alerts for the current location
     * @return array the forecast alerts data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getForecastAlerts() : array
    {
        $data = $this->getForecast();

        return isset($data[self::ALERTS_KEY]) ? $data[self::ALERTS_KEY] : [];
    }

    /**
     * Fetches and returns the flags for the current location
     * @return array the forecast flags data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getForecastFlags() : array
    {
        $data = $this->getForecast();

        return isset($data[self::FLAGS_KEY]) ? $data[self::FLAGS_KEY] : [];
    }

    /**
     * Fetches and returns time machine data
     * @param string $time
     * @return array
     * @throws \Exception If an error is returned from the api or time has not been passed in an exception is thrown
     */
    public function getTimeMachine(string $time) : array
    {
        if ($time === '' || $time === null) {
            throw new \Exception('time is required for time machine requests', 1002);
        }

        return $this->callApi($time);
    }

    /**
     * Fetches and returns the current forecast for the chosen location
     * @param string $time
     * @return array the current forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getCurrentTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);

        return isset($data[self::CURRENT_KEY]) ? $data[self::CURRENT_KEY] : [];
    }

    /**
     * Fetches and returns the minutely forecast for the chosen location
     * @param string $time
     * @return array the minutely forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getMinutelyTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);

        return isset($data[self::MINUTELY_KEY]) ? $data[self::MINUTELY_KEY] : [];
    }

    /**
     * Fetches and returns the hourly time machine for the chosen location
     * @param string $time
     * @return array the hourly forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getHourlyTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);

        return isset($data[self::HOURLY_KEY]) ? $data[self::HOURLY_KEY] : [];
    }

    /**
     * Fetches and returns the daily time machine for the chosen location
     * @param string $time
     * @return array the daily forecast data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    public function getDailyTimeMachine(string $time) : array
    {
        $data = $this->getTimeMachine($time);

        return isset($data[self::DAILY_KEY]) ? $data[self::DAILY_KEY] : [];
    }

    /**
     * Handles making calls to the api
     * @param string $time the time
     * @return array the array of data
     * @throws \Exception If an error is returned from the api an exception is thrown
     */
    private function callApi(string $time = '') : array
    {
        $time = $time === '' ? '' : ',' . $time;
        $queryString = $this->getQueryString();
        $parameters = $queryString === '' ? '' : '?' . $queryString;

        $url = $this->getUrl() . '/' . $this->getLatitude() . ',' . $this->getLongitude() . $time . $parameters;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::TIMEOUT);

        if (!$returnData = curl_exec($curl)) {
            throw new \Exception(curl_error($curl), curl_errno($curl));
        } else {
            $returnData = json_decode($returnData, true);

            // Make sure no error is present
            if (isset($returnData['error'])) {
                throw new \Exception($returnData['error'], $returnData['code']);
            }
        }

        curl_close($curl);

        return $returnData;
    }

    /**
     * Filters out any unsuable paramters and returns what's left
     * @param $parameters
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
            if (in_array($key, $usable) && !is_array($value)) {
                $filteredParameters[$key] = $value;
            }
        }

        return $filteredParameters;
    }

    /**
     * Gets the query string based on the list of parameters
     * @return string
     */
    private function getQueryString() : string
    {
        return http_build_query($this->getParameters());
    }

    /**
     * Gets the url
     * @return string
     */
    private function getUrl() : string
    {
        return $this->url;
    }

    /**
     * Gets the latitude
     * @return float
     */
    private function getLatitude() : float
    {
        return $this->latitude;
    }

    /**
     * Gets the longitude
     * @return float
     */
    private function getLongitude() : float
    {
        return $this->longitude;
    }

    /**
     * Gets the parameters
     * @return array
     */
    private function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Sets the url
     * @param string $url
     */
    private function setUrl(string $url) : void
    {
        $this->url = $url;
    }

    /**
     * Sets the latitude
     * @param float $latitude
     */
    private function setLatitude(float $latitude) : void
    {
        $this->latitude = $latitude;
    }

    /**
     * Sets the longitude
     * @param float $longitude
     */
    private function setLongitude(float $longitude) : void
    {
        $this->longitude = $longitude;
    }

    /**
     * Sets the parameters
     * @param array $parameters
     */
    private function setParameters(array $parameters) : void
    {
        $this->parameters = $parameters;
    }
}
