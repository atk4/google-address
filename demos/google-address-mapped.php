<?php

/**
 * This sample use specific mapping of form field to google address_component field name.
 */
$key = 'YOUR_GOOGLE_MAP_API_KEY_HERE';

$f = $app->add('Form');

$ga = $f1->addField('map_search', [new atk4\GoogleAddress\AddressLookup(['apiKey' => $key])]);

// will concatenate street_number and route in address.
$ga->mapGoogleComponentToField('address', ['street_number', 'route']);

// will concatenate administrative_area_level_2 and locality in address2.
$ga->mapGoogleComponentToField('address2', ['administrative_area_level_2', 'locality']);

// will concatenate country and postal_code in country using '/' as glue.
$ga->mapGoogleComponentToField('country', ['country' => 'short_name', 'postal_code'], ' / ');


$f->addField('address');
$f->addField('address2');
$f->addField('country');
