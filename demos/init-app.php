<?php

declare(strict_types=1);

namespace Atk4\GoogleAddress\Demos;

use Atk4\GoogleAddress\Utils\JsLoader;
use Atk4\Ui\App;
use Atk4\Ui\Layout\Centered;

require_once __DIR__ . '/init-autoloader.php';

$app = new App();
$app->initLayout([Centered::class]);

// Set Google developer key.
JsLoader::setGoogleApiKey('[YOUR-API-KEY]');
