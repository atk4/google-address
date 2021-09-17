<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress;


use Atk4\Ui\Exception;
use Atk4\Ui\Form\Control;
use Atk4\Ui\Form\Control\Line;

class AddressLookup extends Line
{
    public $defaultGoogleProperty = 'long_name';
    //-------------

    /** @var array An array of Google address_components to fill specific controls with. */
    public $controlMap = [];

    /** @var string The google api developper key. */
    public $apiKey = '';

    /** @var array types of predictions to be returned as specify in Google place api. */
    public $types = [];

    /** @var string */
    public $language = 'en';

    /** @var bool Whether the place api will use bounds set by the browser location. */
    public $useBrowserLocation = false;

    /** @var array Limit search result to specific countries. */
    public $countryLimit = [];

    /** @var bool Keep track of Google api js file loaded or not. */
    public static $isApiLoaded = false;

    /**
     * Set Google developper api key.
     */
    public function setGoogleApiKey(string $key)
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
     *  A Google address_component may have two values, a short_name or a long_name value. Ex, a country address_component can be specified using
     *  the long name, i.e United Kingdom or it's short name 'UK'
     *  You can choose to use the long or short name value by specifying it in $googleComponents.
     *
     *  $ga->mapGoogleComponentToField('country', ['country' => 'short_name', 'postal_code'], ' / ');
     *
     *  This will concatenate the google address_component country by using it' short name and the postal_code value.
     *  ex: 'UK / W1C 1JT'
     *
     */
    public function onAutoCompleteSetWith(Control $formControl, Components $component): self
    {
        if ($formControl->form->name !== $this->form->name) {
            throw new Exception('Control must be within the same form.');
        }
        $this->controlMap[] = ['name' => $formControl->short_name, 'value' => $component];

        return $this;
    }

    /**
     * Restricts predictions to the specified country (ISO 3166-1 Alpha-2 country code, case insensitive).
     * E.g., us, br, au. An array of up to 5 country code strings.
     *
     */
    public function setCountryLimit(array $limit)
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
     * https://developers.google.com/places/supported_types#table3
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    private function getLookupOptions()
    {
        $options = [];
        $options['formSelector'] = '#' . $this->form->name;
        if ($this->controlMap) {
            foreach ($this->controlMap as $k => $comp) {
                $options['fieldMap'][] = ['name' => $comp['name'], 'value' => $comp['value']->getBuiltValue()];
            }
        }
        if ($this->countryLimit) {
            if (is_array($this->countryLimit)) {
                $options['countryLimit'] = $this->countryLimit;
            } else {
                $options['countryLimit'] = [$this->countryLimit];
            }
        }
        if ($this->types) {
            $options['types'] = [$this->types];
        }

        if ($this->useBrowserLocation) {
            $options['useBrowserLocation'] = true;
        }

        return $options;
    }

    public function renderView(): void
    {
        // Load google api if not loaded yet <
        self::loadGoogleAPI($this->getApp(), $this->apiKey, $this->language);

        $this->js(true)->atkAddressLookup($this->getLookupOptions());
        parent::renderView();
    }

    public static function loadGoogleAPI($app, $api_key, $language = 'en')
    {
        if (!AddressLookup::$isApiLoaded) {

            if (!$api_key) {
                throw new \Exception('You need to supply your own Google Maps api key.');
            }

            $app->requireJs('../../../vendor/atk4/google-address/public/atk-google-maps-api.js');
            $app->requireJs(
                "https://maps.googleapis.com/maps/api/js?key={$api_key}&libraries=places&language={$language}&callback=atk.mapService.initGoogleApi",
                false,
                true
            );
            AddressLookup::$isApiLoaded = true;
        }
    }
}
