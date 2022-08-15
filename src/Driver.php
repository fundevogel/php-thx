<?php

declare(strict_types=1);

namespace Fundevogel\Thx;

use Fundevogel\Thx\Packaging\Packages;
use Fundevogel\Thx\Traits\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Shieldon\SimpleCache\Cache;

abstract class Driver
{
    /**
     * Traits
     */

    use Helpers;


    /**
     * Properties
     */

    /**
     * Raw data as extracted from lockfile
     *
     * @var array
     */
    public array $data;


    /**
     * Constructor
     *
     * @param array $pkgData Content of datafile as array
     * @param string $lockFile Content of lockfile as string
     * @param string $cacheDriver Cache driver
     * @param array $cacheSettings Cache settings
     * @return void
     */
    public function __construct(array $pkgData, string $lockFile)
    {
        # Extract raw data
        $this->data = $this->extract($pkgData, $lockFile);
    }


    /**
     * Shared methods
     */

    /**
     * Spreads love
     *
     * @param \Shieldon\SimpleCache\Cache $cache Cache object
     * @param array $config Configuration options
     * @return \Fundevogel\Thx\Packaging\Packages Processed data
     */
    public function spreadLove(Cache $cache, array $config): Packages
    {
        # Process raw data
        return $this->process($cache, $config);
    }


    /**
     * Fetches information from API endpoint
     *
     * @param string $apiURL API endpoint
     * @param int $timeout Request timeout (in seconds)
     * @param string $userAgent User-Agent header
     * @return string Response text - empty if connection failed
     */
    protected function fetchRemote(string $apiURL, int $timeout = 3, string $userAgent = ''): string
    {
        # Initialize HTTP client
        $client = new Client(['timeout'  => $timeout]);

        try {
            # Fetch data from API
            # (1) Send GET request
            $response = $client->get($apiURL, ['headers' => ['User-Agent' => $userAgent]]);

            # (2) If successful ..
            if ($response->getStatusCode() === 200) {
                # .. save response
                return (string) $response->getBody();
            }

            # .. otherwise, return empty text
        } catch (TransferException $e) {
        }

        return '';
    }


    /**
     * Required methods
     */

    /**
     * Extracts raw data from input files
     *
     * @param array $pkgData Path to data file
     * @param string $lockFile Lockfile contents
     * @return array Extracted data
     */
    abstract protected function extract(array $pkgData, string $lockFile): array;


    /**
     * Processes raw data
     *
     * @param \Shieldon\SimpleCache\Cache $cache Cache object
     * @param array $config Configuration options
     * @return \Fundevogel\Thx\Packaging\Packages Processed data
     */
    abstract protected function process(Cache $cache, array $config): Packages;
}
