<?php

require_once('vendor/autoload.php');


$drivers = [
    'composer',
    'yarn1',
    'yarn2',
    'npm',
];

if (in_array($argv[1], $drivers) === false) {
    throw new Exception('Invalid driver, exiting ..');
}


switch ($argv[1]) {
    case 'composer':
        $obj = new S1SYPHOS\Thx('tests/composer/composer.json', 'tests/composer/composer.lock');

    case 'yarn1':
        $obj = new S1SYPHOS\Thx('tests/yarn-v1/package.json', 'tests/yarn-v1/yarn.lock');

    case 'yarn2':
        $obj = new S1SYPHOS\Thx('tests/yarn-v2/package.json', 'tests/yarn-v2/yarn.lock');

    case 'npm':
        $obj = new S1SYPHOS\Thx('tests/npm/package.json', 'tests/npm/package-lock.json');
}


var_dump($obj->data());


# Extend proper geeting
echo sprintf('Loading tests for %s .. done.%s', $argv[1], "\n");
