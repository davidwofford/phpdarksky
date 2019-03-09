<?php
/**
 * Author: David Wofford
 * Interface: PhpDarkSkyConstants
 * Description: Holds the constants used by PhpDarkSky
 * Dark Sky: https://darksky.net/dev
 */

namespace DavidWofford\PhpDarkSky;

interface PhpDarkSkyConstants
{
    /**
     * @const string Stores the base url to call for the api
     */
    public const API_URL = 'https://api.darksky.net/forecast/';

    /**
     * @const int Stores the timeout time for the api call
     */
    public const TIMEOUT = 10;

    /**
     * @const string Stores the array key for current items
     */
    public const CURRENT_KEY = 'currently';

    /**
     * @const string Stores the array key for minutely items
     */
    public const MINUTELY_KEY = 'minutely';

    /**
     * @const string Stores the array key for hourly items
     */
    public const HOURLY_KEY = 'hourly';

    /**
     * @const string Stores the array key for daily items
     */
    public const DAILY_KEY = 'daily';

    /**
     * @const string Stores the array key for the alerts
     */
    public const ALERTS_KEY = 'alerts';

    /**
     * @const string Stores the array key for the flags
     */
    public const FLAGS_KEY = 'flags';
}