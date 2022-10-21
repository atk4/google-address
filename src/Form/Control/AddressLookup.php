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
    /** @var list<array{name:string,value:Build}> An array of Google address_components to fill specific controls with. */
    public array $controlMap = [];

    /** @var string[] of predictions to be returned as specify in Google place api. */
    public array $types = [];

    /** the place api will use bounds set by the browser location. */
    public bool $useBrowserLocation = false;

    /** @var string[] Limit search result to specific countries. */
    public array $countryLimit = [];

    /** @var string[] Any of the plugin settings. */
    public array $settings = [];

    /**
     * Set a form control to google address component property value when a result is selected.
     *
     * $ga->onCompleteSet($addressInput, Components::with(Property::of(Property::STREET_NUMBER)));
     *
     *    Will fill $addressInput control with 'street_number' value return by Google Place AutoComplete.
     */
    public function onCompleteSet(Control $formControl, Build $builder): self
    {
        $this->controlMap[] = ['name' => $formControl->shortName, 'value' => $builder];

        return $this;
    }

    /**
     * Restricts predictions to the specified country (ISO 3166-1 Alpha-2 country code, case insensitive).
     * E.g., us, br, au. An array of up to 5 country code strings.
     *
     * @param string[] $limit
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

    /**
     * Set place result types.
     * https://developers.google.com/places/supported_types#table3
     *
     * @param string[] $types
     */
    public function setTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }

    /**
     * @return array{formSelector: string}
     */
    private function getLookupSettings(): array
    {
        $settings = [];
        $settings['formSelector'] = '#' . $this->form->name;

        foreach ($this->controlMap as $k => $comp) {
            $settings['fieldMap'][] = ['name' => $comp['name'], 'value' => $comp['value']->getBuiltValue()];
        }

        if (!empty($this->countryLimit)) {
            $settings['countryLimit'] = $this->countryLimit;
        }

        if (!empty($this->types)) {
            $settings['types'] = $this->types;
        }

        if ($this->useBrowserLocation) {
            $settings['useBrowserLocation'] = true;
        }

        return array_merge($this->settings, $settings);
    }

    public function renderView(): void
    {
        JsLoader::load($this->getApp());

        $this->js(true)->AddressLookup($this->getLookupSettings());

        parent::renderView();
    }
}
