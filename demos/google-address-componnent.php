<?php

declare(strict_types=1);

use Atk4\GoogleAddress\Form\Control\AddressLookup;
use Atk4\GoogleAddress\Utils\JsLoader;
use Atk4\GoogleAddress\Utils\Type;
use Atk4\Ui\Form;

// @var \Atk4\Ui\App $app

// Set Google developer key.
JsLoader::setGoogleApiKey('');

$form = Form::addTo($app);
$form->addControl('map_search', [AddressLookup::class]);

$f_add = $form->addGroup('Street/City');
$f_add->addControl(Type::STREET_NUMBER, ['width' => 'four']);
$f_add->addControl(Type::ROUTE, ['width' => 'twelve']);

$f_add2 = $form->addGroup('City / Locality');
$f_add2->addControl(Type::LOCALITY, ['width' => 'five']);
$f_add2->addControl(Type::SUB_LOCALITY_1, ['width' => 'five']);
$f_add2->addControl(Type::POSTAL_TOWN, ['width' => 'six']);

$f_county = $form->addGroup('County / Administrative Area');
$f_county->addControl(Type::ADMIN_LEVEL_2, ['width' => 'eight']);
$f_county->addControl(Type::ADMIN_LEVEL_1, ['width' => 'eight']);

$f_country = $form->addGroup('Country');
$f_country->addControl(Type::COUNTRY, ['width' => 'eight']);
$f_country->addControl(Type::POSTAL_CODE, ['width' => 'eight']);

$f_lat_lng = $form->addGroup('Lat/Lng');
$f_lat_lng->addControl(Type::LAT, ['width' => 'eight']);
$f_lat_lng->addControl(Type::LNG, ['width' => 'eight']);
