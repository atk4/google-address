<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Demos;

use Atk4\GoogleAddress\Model\Address;
use Atk4\GoogleAddress\Utils\JsLoader;
use Atk4\Ui\App;
use Atk4\Ui\Crud;

/*
 * Demonstrate use with Crud.
 * JsLoader::load() is need for AddressLookup to work in ModalExecutor.
 */

/** @var App $app */
require_once __DIR__ . '/init-app.php';

// Load map api.
JsLoader::load($app);

Crud::addTo($app)->setModel(new Address($app->db, ['table' => 'address']));
