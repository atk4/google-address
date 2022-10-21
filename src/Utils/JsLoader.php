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
    private static bool $isLoaded = false;

    /** The Google api developer key. */
    protected static string $apiKey = '';

    /** Default location of js file, use this if $locationUrl is not specified in JsLoader::load($app, '/new/path/to/js-file.min.js'); */
    protected static string $defaultLocationUrl = '/assets/atk-google-maps.min.js';

    /** Google maps version. */
    protected static string $apiVerstion = 'quarterly';

    /** @var string[] Libraries to load with Google api. */
    protected static array $apiLibraries = ['places'];

    /** @var array<string, int|string|string[]|mixed> Google Map options as per https://googlemaps.github.io/js-api-loader/interfaces/LoaderOptions.html */
    protected static array $mapOptions = [];

    public static function setGoogleApiKey(string $key): void
    {
        self::$apiKey = $key;
    }

    /**
     * @param array<string, int|string|string[]|mixed> $options
     */
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
            $app->requireJs($locationUrl ?? self::$defaultLocationUrl);

            if (self::$apiKey === '') {
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
