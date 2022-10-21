<?php
declare(strict_types=1);

namespace Atk4\GoogleAddress\Demos;

/**
 * Demonstrate use with Crud.
 * JsLoader::load() is need for AddressLookup to work in ModalExecutor.
 */

/** @var \Atk4\Ui\App $app */

use Atk4\GoogleAddress\Model\Address;
use Atk4\GoogleAddress\Utils\JsLoader;
use Atk4\Ui\Crud;

// Set Google developer key.
JsLoader::setGoogleApiKey('');
// Load map api.
JsLoader::load($app);

Crud::addTo($app)->setModel(new Address($app->db, ['table' => 'address']));
