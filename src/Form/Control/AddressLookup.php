<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Form\Control;

use Atk4\GoogleAddress\Utils\Build;
use Atk4\GoogleAddress\Utils\JsLoader;
use Atk4\Ui\Form\Control;
use Atk4\Ui\Form\Control\Line;

/**
 * Will set an input control as an Autocomplete maps search input.
 * Using Google maps api.
 */
class AddressLookup extends Line
{
    /** @var array An array of Google address_components to fill specific controls with. */
    public $controlMap = [];

    /** @var array types of predictions to be returned as specify in Google place api. */
    public $types = [];

    /** @var bool Whether the place api will use bounds set by the browser location. */
    public $useBrowserLocation = false;

    /** @var array Limit search result to specific countries. */
    public $countryLimit = [];

    /**
     * Set a form control to google address component property value when a result is selected.
     *
     * $ga->onCompleteSet($addressInput, Components::with(Property::of(Property::STREET_NUMBER)));
     *
     *    Will fill $addressInput control with 'street_number' value return by Google Place AutoComplete.
     */
    public function onCompleteSet(Control $formControl, Build $builder): self
    {
        $this->controlMap[] = ['name' => $formControl->short_name, 'value' => $builder];

        return $this;
    }

    /**
     * Restricts predictions to the specified country (ISO 3166-1 Alpha-2 country code, case insensitive).
     * E.g., us, br, au. An array of up to 5 country code strings.
     */
    public function setCountryLimit(array $limit): self
    {
        $this->countryLimit = $limit;

        return $this;
    }

    /**
     * Try to use browser navigation in order to set
     * place bounds from user location.
     */
    public function useBrowserLocation(): self
    {
        $this->useBrowserLocation = true;

        return $this;
    }

    /*
     * Set place result types.
     * https://developers.google.com/places/supported_types#table3
     */
    public function setTypes(array $types): void
    {
        $this->types = $types;
    }

    private function getLookupOptions(): array
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
        JsLoader::load($this->getApp());

        $this->js(true)->atkAddressLookup($this->getLookupOptions());

        parent::renderView();
    }
}
