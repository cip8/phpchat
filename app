#! /usr/bin/env php

<?php

use Symfony\Component\Console\Application;

require 'api/vendor/autoload.php';

$app = new Application('PHP Chat', '1.0');

$app->add(new App\StartChat());

$app->run();
