<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Demos;

use Atk4\GoogleAddress\Form\Control\AddressLookup;
use Atk4\GoogleAddress\Utils\Build;
use Atk4\GoogleAddress\Utils\Type;
use Atk4\GoogleAddress\Utils\Value;
use Atk4\Ui\App;
use Atk4\Ui\Form;

/** @var App $app */
require_once __DIR__ . '/init-app.php';

$addressValue = Build::with(Value::of(Type::STREET_NUMBER))->concat(Value::of(Type::ROUTE))->glueWith(' ');
$address2Value = Build::with(Value::of(Type::ADMIN_LEVEL_1));
$countryValue = Build::with(Value::of(Type::COUNTRY))->concat(Value::of(Type::POSTAL_CODE))->glueWith(' / ');

$latLngValue = Build::with(Value::of(Type::LAT))->concat(Value::of(Type::LNG))->glueWith(':');

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
