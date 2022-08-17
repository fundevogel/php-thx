<?php

namespace Fundevogel\Thx\Tests;

use Fundevogel\Thx\ThankYou;

class ThankYouTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Properties
     */

    /**
     * Fixtures
     *
     * @var array
     */
    private static array $files;


    /**
     * Setup
     */

    public static function setUpBeforeClass(): void
    {
        # Setup
        self::$files = [
            'composer' => [
                'data' => __DIR__ . '/fixtures/composer/composer.json',
                'lock' => __DIR__ . '/fixtures/composer/composer.lock',
            ],
            'npm-v1' => [
                'data' => __DIR__ . '/fixtures/npm-v1/package.json',
                'lock' => __DIR__ . '/fixtures/npm-v1/package-lock.json',
            ],
            'npm-v2' => [
                'data' => __DIR__ . '/fixtures/npm-v2/package.json',
                'lock' => __DIR__ . '/fixtures/npm-v2/package-lock.json',
            ],
            'yarn-v1' => [
                'data' => __DIR__ . '/fixtures/yarn-v1/package.json',
                'lock' => __DIR__ . '/fixtures/yarn-v1/yarn.lock',
            ],
            'yarn-v2' => [
                'data' => __DIR__ . '/fixtures/yarn-v2/package.json',
                'lock' => __DIR__ . '/fixtures/yarn-v2/yarn.lock',
            ],
        ];
    }


    /**
     * Tests
     */

    public function testComposer(): void
    {
        # Run function
        $data = ThankYou::veryMuch(self::$files['composer']['data'], self::$files['composer']['lock']);

        # Assert result
        $this->assertEquals($data, [
            [
                'name' => 'php-mastodon',
                'version' => '0.6.0',
                'maintainer' => 'fundevogel',
                'license' => 'GPL-3.0',
                'description' => 'A small PHP library for interacting with Mastodon\'s REST API.',
                'url' => 'https://github.com/fundevogel/php-mastodon',
            ],
            [
                'name' => 'php-pcbis',
                'version' => '4.0.0',
                'maintainer' => 'fundevogel',
                'license' => 'GPL-3.0',
                'description' => 'Simple PHP wrapper for Zeitfracht\'s webservice API, written in PHP',
                'url' => 'https://github.com/fundevogel/php-pcbis',
            ],
            [
                'name' => 'tiny-phpeanuts',
                'version' => '1.0.2',
                'maintainer' => 'fundevogel',
                'license' => 'MIT',
                'description' => 'A tiny PHP library for creating SVG donut (and pie) charts.',
                'url' => 'https://github.com/fundevogel/tiny-phpeanuts',
            ],
            [
                'name' => 'php-gesetze',
                'version' => '0.7.1',
                'maintainer' => 's1syphos',
                'license' => 'GPL-3.0',
                'description' => 'Linking german legal norms, dependency-free & GDPR-friendly',
                'url' => 'https://github.com/S1SYPHOS/php-gesetze',
            ],
            [
                'name' => 'php-thx',
                'version' => '1.2.1',
                'maintainer' => 's1syphos',
                'license' => 'MIT',
                'description' => 'Acknowledge the people behind your frontend dependencies - and give thanks!',
                'url' => 'https://github.com/fundevogel/php-thx',
            ],
        ]);
    }


    public function testNpmV1(): void
    {
        # Run function
        $data = ThankYou::veryMuch(self::$files['npm-v1']['data'], self::$files['npm-v1']['lock']);

        # Assert result
        $this->assertEquals($data, [
            [
                'name' => 'animejs',
                'version' => '3.2.1',
                'maintainer' => 'juliangarnier',
                'license' => 'MIT',
                'description' => 'JavaScript animation engine',
                'url' => 'https://github.com/juliangarnier/anime'
            ],
            [
                'name' => 'bigpicture',
                'version' => '2.6.2',
                'maintainer' => 'henrygd',
                'license' => 'MIT',
                'description' => 'Lightweight image and video viewer, supports youtube / vimeo',
                'url' => 'https://github.com/henrygd/bigpicture'
            ],
            [
                'name' => 'lazysizes',
                'version' => '5.3.2',
                'maintainer' => 'aFarkas',
                'license' => 'MIT',
                'description' => 'High performance (jankfree) lazy loader for images (including responsive images), iframes and scripts (widgets).',
                'url' => 'https://github.com/aFarkas/lazysizes'
            ],
            [
                'name' => 'tippy.js',
                'version' => '6.3.7',
                'maintainer' => 'atomiks',
                'license' => 'MIT',
                'description' => 'The complete tooltip, popover, dropdown, and menu solution for the web',
                'url' => 'https://github.com/atomiks/tippyjs'
            ],
        ]);
    }


    public function testNpmV2(): void
    {
        # Run function
        $data = ThankYou::veryMuch(self::$files['npm-v2']['data'], self::$files['npm-v2']['lock']);

        # Assert result
        $this->assertEquals($data, [
            [
                'name' => 'animejs',
                'version' => '3.2.1',
                'maintainer' => 'juliangarnier',
                'license' => 'MIT',
                'description' => 'JavaScript animation engine',
                'url' => 'https://github.com/juliangarnier/anime'
            ],
            [
                'name' => 'bigpicture',
                'version' => '2.5.3',
                'maintainer' => 'henrygd',
                'license' => 'MIT',
                'description' => 'Lightweight image and video viewer, supports youtube / vimeo',
                'url' => 'https://github.com/henrygd/bigpicture'
            ],
            [
                'name' => 'lazysizes',
                'version' => '5.3.2',
                'maintainer' => 'aFarkas',
                'license' => 'MIT',
                'description' => 'High performance (jankfree) lazy loader for images (including responsive images), iframes and scripts (widgets).',
                'url' => 'https://github.com/aFarkas/lazysizes'
            ],
            [
                'name' => 'tippy.js',
                'version' => '6.3.7',
                'maintainer' => 'atomiks',
                'license' => 'MIT',
                'description' => 'The complete tooltip, popover, dropdown, and menu solution for the web',
                'url' => 'https://github.com/atomiks/tippyjs'
            ],
        ]);
    }


    public function testYarnV1(): void
    {
        # Run function
        $data = ThankYou::veryMuch(self::$files['yarn-v1']['data'], self::$files['yarn-v1']['lock']);

        # Assert result
        $this->assertEquals($data, [
            [
                'name' => 'animejs',
                'version' => '3.2.1',
                'maintainer' => 'juliangarnier',
                'license' => 'MIT',
                'description' => 'JavaScript animation engine',
                'url' => 'https://github.com/juliangarnier/anime'
            ],
            [
                'name' => 'bigpicture',
                'version' => '2.5.3',
                'maintainer' => 'henrygd',
                'license' => 'MIT',
                'description' => 'Lightweight image and video viewer, supports youtube / vimeo',
                'url' => 'https://github.com/henrygd/bigpicture'
            ],
            [
                'name' => 'lazysizes',
                'version' => '5.3.2',
                'maintainer' => 'aFarkas',
                'license' => 'MIT',
                'description' => 'High performance (jankfree) lazy loader for images (including responsive images), iframes and scripts (widgets).',
                'url' => 'https://github.com/aFarkas/lazysizes'
            ],
            [
                'name' => 'tippy.js',
                'version' => '6.3.7',
                'maintainer' => 'atomiks',
                'license' => 'MIT',
                'description' => 'The complete tooltip, popover, dropdown, and menu solution for the web',
                'url' => 'https://github.com/atomiks/tippyjs'
            ],
        ]);
    }


    public function testYarnV2(): void
    {
        # Run function
        $data = ThankYou::veryMuch(self::$files['yarn-v2']['data'], self::$files['yarn-v2']['lock']);

        # Assert result
        $this->assertEquals($data, [
            [
                'name' => 'animejs',
                'version' => '3.2.1',
                'maintainer' => 'juliangarnier',
                'license' => 'MIT',
                'description' => 'JavaScript animation engine',
                'url' => 'https://github.com/juliangarnier/anime'
            ],
            [
                'name' => 'bigpicture',
                'version' => '2.5.3',
                'maintainer' => 'henrygd',
                'license' => 'MIT',
                'description' => 'Lightweight image and video viewer, supports youtube / vimeo',
                'url' => 'https://github.com/henrygd/bigpicture'
            ],
            [
                'name' => 'lazysizes',
                'version' => '5.3.2',
                'maintainer' => 'aFarkas',
                'license' => 'MIT',
                'description' => 'High performance (jankfree) lazy loader for images (including responsive images), iframes and scripts (widgets).',
                'url' => 'https://github.com/aFarkas/lazysizes'
            ],
            [
                'name' => 'tippy.js',
                'version' => '6.3.7',
                'maintainer' => 'atomiks',
                'license' => 'MIT',
                'description' => 'The complete tooltip, popover, dropdown, and menu solution for the web',
                'url' => 'https://github.com/atomiks/tippyjs'
            ],
        ]);
    }
}
