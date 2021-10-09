<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Utils;

use Atk4\Ui\App;
use Atk4\Ui\Exception;
use Atk4\Ui\JsChain;

/**
 * Load javascript files.
 */
class JsLoader
{
    /** @var string Javascript file location. */
    public static $cdn = 'https://cdn.jsdelivr.net/gh/atk4/google-address';

    /** @var string Javascript file version. */
    public static $version = '2.2.1';

    /** @var bool */
    private static $isLoaded = false;

    /** @var string The google api developer key. */
    protected static $apiKey = '';

    /** @var string Google maps version. */
    protected static $apiVerstion = 'quarterly';

    /** @var string[] Libraries to load with Google api. */
    protected static $apiLibraries = ['places'];

    /** @var array Google Map options as per https://googlemaps.github.io/js-api-loader/interfaces/LoaderOptions.html */
    protected static $mapOptions = [];

    public static function setGoogleApiKey(string $key): void
    {
        self::$apiKey = $key;
    }

    public static function setMapOptions(array $options): void
    {
        self::$mapOptions = $options;
    }

    /**
     * Load Js file.
     * Allow bypassing default location.
     *
     * This js file add a mapService to the atk namespace. (atk.mapService)
     * and set appropriate maps api options.
     * Javascript integration can then use mapService for initialization.
     * ex: atk.mapService.loadGoogleApi().then((google) => {//initialize maps.})
     */
    public static function load(App $app, string $locationUrl = null): void
    {
        if (!self::$isLoaded) {
            if (!$locationUrl) {
                $cdn = self::$cdn;
                $version = self::$version;
                $locationUrl = "{$cdn}@{$version}/public/atk-google-maps.min.js";
            }

            $app->requireJs($locationUrl);

            if (!self::$apiKey) {
                throw new Exception('Google map Api Key not set.');
            }

            $app->layout->js(true, (new JsChain('atk.mapService'))->setMapLoader(array_merge([
                'apiKey' => self::$apiKey,
                'version' => self::$apiVerstion,
                'libraries' => self::$apiLibraries,
            ], self::$mapOptions)));

            self::$isLoaded = true;
        }
    }
}
