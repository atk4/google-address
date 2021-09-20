<?php

declare(strict_types=1);

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../../init-app.php';

use Atk4\GoogleAddress\Form\Control\AddressLookup;
use Atk4\GoogleAddress\Utils\Property;
use Atk4\GoogleAddress\Utils\Type;
use Atk4\GoogleAddress\Utils\Components;
use Atk4\GoogleAddress\Utils\JsLoader;
use Atk4\Ui\Form;

// Set Google developer key.
JsLoader::setGoogleApiKey('');

$addressValue = Components::with(Property::of(Type::STREET_NUMBER))
                          ->concat(Property::of(Type::ROUTE))
                          ->glueWith(' ');
$address2Value = Components::with(Property::of(Type::ADMIN_LEVEL_1));
$countryValue = Components::with(Property::of(Type::COUNTRY))
                          ->concat(Property::of(Type::POSTAL_CODE))
                          ->glueWith(' / ');

$latLngValue = Components::with(Property::of(Type::LAT))->concat(Property::of(Type::LNG))->glueWith(':');


$form = Form::addTo($app);

/** @var AddressLookup $ga */
$ga = $form->addControl('map_search', [AddressLookup::class]);

$address = $form->addControl('address');
$address2 = $form->addControl('address2');
$country = $form->addControl('country');
$latLng = $form->addControl('lat_lng');

$ga->onCompleteSet($address, $addressValue);
$ga->onCompleteSet($address2, $address2Value);
$ga->onCompleteSet($country, $countryValue);
$ga->onCompleteSet($latLng, $latLngValue);
