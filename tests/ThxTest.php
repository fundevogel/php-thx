<?php

namespace Fundevogel\Thx\Tests;

use Fundevogel\Thx\Thx;

use PHPUnit\Framework\TestCase;

class ThxTest extends TestCase
{
    /**
     * Properties
     */

    /**
     * Fixtures
     *
     * @var array
     */
    private static $files;


    /**
     * Setup
     */

    public static function setUpBeforeClass(): void
    {
        # Setup
        self::$files = [
            'composer' => [
                'data' => __DIR__ . '/resources/composer/composer.json',
                'lock' => __DIR__ . '/resources/composer/composer.lock',
            ],
            'npm' => [
                'data' => __DIR__ . '/resources/npm/package.json',
                'lock' => __DIR__ . '/resources/npm/package-lock.json',
            ],
            'yarn-v1' => [
                'data' => __DIR__ . '/resources/yarn-v1/package.json',
                'lock' => __DIR__ . '/resources/yarn-v1/yarn.lock',
            ],
            'yarn-v2' => [
                'data' => __DIR__ . '/resources/yarn-v2/package.json',
                'lock' => __DIR__ . '/resources/yarn-v2/yarn.lock',
            ],
        ];
    }


    /**
     * Tests
     */

    public function testException(): void
    {
        # Assert exception
        $this->expectException(Exception::class);

        # Run function
        $obj = new Thx('some-data.json', 'some-lock.json');
    }


    public function testData(): void
    {
        # Setup
        # (1) Providers
        $array = [
            // 'composer',
            'npm',
            'yarn-v1',
            'yarn-v2',
        ];

        foreach ($array as $key) {
            # (2) Object
            $obj = new Thx(self::$files[$key]['data'], self::$files[$key]['lock']);

            # (3) Data file
            $expected = sprintf(__DIR__ . '/fixtures/%s/data.json', $key);

            # Run function
            $result = $obj->data();

            // $json_encoded = json_encode($result);
            // file_put_contents($files['file'], $json_encoded);

            # Assert result
            $this->assertEquals($result, json_decode(file_get_contents($expected), true));
        }
    }


    public function testPkgs(): void
    {
        # Setup
        # (1) Providers
        $array = [
            'composer',
            'npm',
            'yarn-v1',
            'yarn-v2',
        ];

        foreach ($array as $key) {
            # (2) Object
            $obj = new Thx(self::$files[$key]['data'], self::$files[$key]['lock']);

            # (3) Data file
            $expected = sprintf(__DIR__ . '/fixtures/%s/pkgs.json', $key);

            # Run function
            $result = $obj->giveBack()->pkgs();

            # Assert result
            $this->assertEquals($result, json_decode(file_get_contents($expected), true));
        }
    }


    public function testPackages(): void
    {
        # Setup
        # (1) Providers
        $array = [
            'composer',
            'npm',
            'yarn-v1',
            'yarn-v2',
        ];

        foreach ($array as $key) {
            # (2) Object
            $obj = new Thx(self::$files[$key]['data'], self::$files[$key]['lock']);

            # (3) Data file
            $expected = sprintf(__DIR__ . '/fixtures/%s/packages.json', $key);

            # Run function
            $result = $obj->giveBack()->packages();

            # Assert result
            $this->assertEquals($result, json_decode(file_get_contents($expected), true));
        }
    }


    public function testLicenses(): void
    {
        # Setup
        # (1) Providers
        $array = [
            'composer',
            'npm',
            'yarn-v1',
            'yarn-v2',
        ];

        foreach ($array as $key) {
            # (2) Object
            $obj = new Thx(self::$files[$key]['data'], self::$files[$key]['lock']);

            # (3) Data file
            $expected = sprintf(__DIR__ . '/fixtures/%s/licenses.json', $key);

            # Run function
            $result = $obj->giveBack()->licenses();

            # Assert result
            $this->assertEquals($result, json_decode(file_get_contents($expected), true));
        }
    }


    public function testByLicense(): void
    {
        # Setup
        # (1) Providers
        $array = [
            'composer',
            'npm',
            'yarn-v1',
            'yarn-v2',
        ];

        foreach ($array as $key) {
            # (2) Object
            $obj = new Thx(self::$files[$key]['data'], self::$files[$key]['lock']);

            # (3) Data file
            $expected = sprintf(__DIR__ . '/fixtures/%s/byLicense.json', $key);

            # Run function
            $result = $obj->giveBack()->byLicense();

            # Assert result
            $this->assertEquals($result, json_decode(file_get_contents($expected), true));
        }
    }
}
