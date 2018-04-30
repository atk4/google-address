<?php

/**
 * This sample use direct mapping of form field to google address_component field name.
 */
$key = 'YOUR_GOOGLE_MAP_API_KEY_HERE';

$f = $app->add('Form');

$f->addField('map_search', [new atk4\GoogleAddress\AddressLookup(['apiKey' => $key])]);
$f_add = $f->addGroup('Street/City');
$f_add->addField('street_number', ['width' => 'four']);
$f_add->addField('route', ['width' => 'twelve']);

$f_add2 = $f->addGroup('City / Locality');
$f_add2->addField('locality', ['width' => 'five']);
$f_add2->addField('sub_locality_level_1', ['width' => 'five']);
$f_add2->addField('postal_town', ['width' => 'six']);

$f_county = $f->addGroup('County / Administrative Area');
$f_county->addField('administrative_area_level_2', ['width' => 'eight']);
$f_county->addField('administrative_area_level_1', ['width' => 'eight']);

$f_country = $f->addGroup('Country');
$f_country->addField('country', ['width' => 'eight']);
$f_country->addField('postal_code', ['width' => 'eight']);

$f_lat_lng = $f->addGroup('Lat/Lng');
$f_lat_lng->addField('lat', ['width' => 'eight']);
$f_lat_lng->addField('lng', ['width' => 'eight']);
