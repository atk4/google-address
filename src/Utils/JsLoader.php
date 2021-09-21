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
    public static $version = '2.0.0';

    /** @var bool */
    public static $isLoaded = false;

    /** @var string The google api developer key. */
    public static $apiKey = '';

    /** @var string Google maps version. */
    public static $apiVerstion = '3.46';

    /** @var string[] Libraries to load with Google api. */
    public static $apiLibraries = ['places'];

    /** @var string */
    public static $language = 'en';

    /** @var array Google Map options */
    public static $mapOptions = [];

    public static function setGoogleApiKey(string $key): void
    {
        self::$apiKey = $key;
    }

    /**
     * Load Js file.
     * Allow bypassing default location.
     *
     * This js file add a mapService to the atk namespace. (atk.mapService)
     * and set appropriate maps api options.
     * Javascript integration can then use mapService for initialization.
     * ex: atk.mapService.getGoogleApi().then( (google) => {//initialize maps.})
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
                'language' => self::$language,
            ], self::$mapOptions)));

            self::$isLoaded = true;
        }
    }
}
