<?php

require_once('vendor/autoload.php');


if (isset($argv[1])) {
    if ($argv[1] === 'composer') {
        $obj = S1SYPHOS\Thx::giveBack('tests/composer/composer.json', 'tests/composer/composer.lock');
    }

    if ($argv[1] === 'yarn1') {
        $obj = S1SYPHOS\Thx::giveBack('tests/yarn-v1/package.json', 'tests/yarn-v1/yarn.lock');
    }

    if ($argv[1] === 'yarn2') {
        $obj = S1SYPHOS\Thx::giveBack('tests/yarn-v2/package.json', 'tests/yarn-v2/yarn.lock');
    }

    if ($argv[1] === 'npm') {
        $obj = S1SYPHOS\Thx::giveBack('tests/npm/package.json', 'tests/npm/package-lock.json');
    }
}


if (!isset($obj)) {
    echo sprintf('Invalid driver, exiting ..%s', "\n");
    exit(1);
}


if (isset($argv[2])) {
    # Extend proper geeting
    echo sprintf('Loading tests for "%s" ..%s', $argv[1], "\n");

    if ($argv[2] === 'data') {
        var_dump($obj->spreadLove()->data());
    }

    if ($argv[2] === 'pkgs') {
        var_dump($obj->spreadLove()->pkgs());
    }

    if ($argv[2] === 'packages') {
        var_dump($obj->spreadLove()->packages());
    }

} else {
    echo sprintf('Invalid data source for "%s", exiting ..%s', $argv[1], "\n");
}
