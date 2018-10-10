<?php

namespace atk4\GoogleAddress;

use atk4\ui\FormField\Line;

class AddressLookup extends Line
{
    /**
     * The google api developper key.
     * @var null
     */
    public $apiKey = null;

    /**
     * The fieldMap array.
     *
     * @var array
     */
    public $fieldMap = [];

    /**
     * The default Google address_component property value.
     * @var string
     */
    public $defaultGoogleProperty = 'long_name';

    /**
     * Sets the types of predictions to be returned as specify in Google place api.
     * https://developers.google.com/places/supported_types#table3
     *
     *
     * @var null
     */
    public $types = null;

    /**
     * Sets the default language to use <
     *
     * @var string
     */
    public $language = 'en';

    /**
     * Whether or not the place api will use bounds set by browser location.
     * @var bool
     */
    public $useBrowserLocation = false;

    /**
     * Restricts predictions to the specified country (ISO 3166-1 Alpha-2 country code, case insensitive).
     * E.g., us, br, au. An array of up to 5 country code strings.
     *
     * @var null|array
     */
    public $countryLimit = null;

    /**
     * Keep track of Google api js file loaded or not.
     * @var bool
     */
    public static $isApiLoaded = false;

    /**
     * Set Google developper api key.
     *
     * @param string $key The google api key.
     */
    public function setGoogleApiKey($key)
    {
        $this->apiKey = $key;
    }

    /**
     * Map a form field to google component property.
     *
     * @param string       $fieldName        The field name to map google address component to.
     * @param string|array $googleComponents The google component to use for the field name.
     * @param null|string  $glue             The glue to set for concatenating component together.
     *
     * Example usage:
     *  Map an input form field to a google address_compoenent field.
     *
     *  $ga->mapGoogleComponentToField('address', 'street_number');
     *
     *  this will retrieve the content of street_number from google place result and insert it in field name address in form.
     * -------------
     *  Map an input form field to more than one google address_component fields by concatenating their value.
     *
     *  $ga->mapGoogleComponentToField('address', ['street_number', 'route'], $glue);
     *
     *  When specifying an array for $googleComponents then the associated form input field will contains
     *  a concatenate value of specified google address_component. In the example above, the address field
     *  will contain street_number and route seperate by the glue value.
     *  You may specify as many google address_component to be concatenate to one value.
     * --------------
     *  Map an input form field by specifying the name property.
     *  A google address_component may have two values, a short_name or a long_name value. Ex, a country address_component can be specify using
     *  the long name, i.e United Kingdom or it's short name 'UK'
     *  You can choose to use the long or short name value by specifying it in $googleCompents.
     *
     *  $ga->mapGoogleComponentToField('country', ['country' => 'short_name', 'postal_code'], ' / ');
     *
     *  This will concatenate the google address_component country by using it' short name and the postal_code value.
     *  ex: 'UK / W1C 1JT'
     *
     */
    public function mapGoogleComponentToField($fieldName, $googleComponents, $glue = ' ')
    {
        $temp[$fieldName] = [];
        if (!is_array($googleComponents)) {
            $temp[$fieldName] = ['concat' => [['type' => $googleComponents, 'property' => $this->defaultGoogleProperty]]];
        } else {
            foreach ($googleComponents as $key => $component) {
                if ($component === 'short_name' || $component === 'long_name') {
                    $temp[$fieldName]['concat'][] = ['type' => $key, 'property' => $component];
                } else {
                    $temp[$fieldName]['concat'][] = ['type' => $component, 'property' => $this->defaultGoogleProperty];
                }
            }
        }
        if ($glue) {
            $temp[$fieldName]['glue'] = $glue;
        }

        $this->fieldMap[] = $temp;
    }

    /**
     * Set search result limit up to 5 country.
     *
     * @param string|array $limit
     */
    public function setCountryLimit($limit)
    {
        $this->countryLimit = $limit;
    }

    /**
     * Try to use browser navigation in order to set
     * place bounds from user location.
     */
    public function useBrowserLocation()
    {
        $this->useBrowserLocation = true;
    }

    /*
     * Set place result types.
     */
    public function setTypes($types)
    {
        $this->setTypes = $types;
    }

    private function getLookupOptions()
    {
        $options = [];
        if ($this->fieldMap) {
            $options['fieldMap'] = $this->fieldMap;
        }
        if ($this->countryLimit) {
            if (is_array($this->countryLimit)) {
                $options['countryLimit'] = $this->countryLimit;
            } else {
                $options['countryLimit'] = [$this->countryLimit];
            }
        }
        if ($this->types) {
            if (is_array($this->types)) {
                $options['types'] = $this->types;
            } else {
                $options['types'] = [$this->types];

            }
        }
        if ($this->useBrowserLocation) {
            $options['useBrowserLocation'] = true;
        }

        return $options;
    }

    public function renderView()
    {
        // Load google api if not loaded yet <
        self::loadGoogleAPI($this->app, $this->apiKey, $this->language);

        $this->js(true)->atkAddressLookup($this->getLookupOptions());
        parent::renderView();
    }

    public static function loadGoogleAPI($app, $api_key, $language = 'en')
    {
        if (!AddressLookup::$isApiLoaded) {

            if (!$api_key) {
                throw new \Exception('You need to supply your own Google Maps api key.');
            }

            $app->requireJs('https://cdn.rawgit.com/atk4/google-address/1.0.3/public/atk-google-address.min.js');
            $app->requireJs(
                "https://maps.googleapis.com/maps/api/js?key={$api_key}&libraries=places&language={$language}&callback=atk.mapService.initGoogleApi",
                false,
                true
            );
            AddressLookup::$isApiLoaded = true;
        }
    }
}
