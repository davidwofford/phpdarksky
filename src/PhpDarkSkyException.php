<?php
/**
 * Author: David Wofford
 * Class: PhpDarkSkyException
 * Description: An exception wrapper for the library based on the existing Exception class
 * Dark Sky: https://darksky.net/dev
 */

namespace DavidWofford\PhpDarkSky;

class PhpDarkSkyException extends \Exception
{
    /**
     * This class does not do anything special
     * It exists so that people can catch errors specifically from this api wrapper
     */
}
