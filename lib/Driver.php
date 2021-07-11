<?php

namespace S1SYPHOS;


use S1SYPHOS\Traits\Helpers;


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
    public $data = null;


    /**
     * Operating mode identifier
     *
     * @var string
     */
    public $mode;


    /**
     * Constructor
     *
     * @param string $pkgData Content of datafile as array
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
     * @return \S1SYPHOS\Packaging\Packages Processed data
     */
    public function spreadLove(\Shieldon\SimpleCache\Cache $cache, array $config): \S1SYPHOS\Packaging\Packages
    {
        # Process raw data
        return $this->process($cache, $config);
    }


    protected function fetchRemote(string $apiURL, int $timeout = 3, string $userAgent = ''): string
    {
        # Initialize HTTP client
        $client = new \GuzzleHttp\Client(['timeout'  => $timeout]);

        try {
            $response = $client->get($apiURL, ['headers' => ['User-Agent' => $userAgent]]);
        } catch (\GuzzleHttp\Exception\TransferException $e) {
            return '';
        }

        if ($response->getStatusCode() === 200) {
            return $response->getBody();
        }

        # (3) .. otherwise, transmission *may* have worked
        return '';
    }


    /**
     * Required methods
     */

    /**
     * Extracts raw data from input files
     *
     * @param string $dataFile Path to data file
     * @param string $lockFile Lockfile contents
     * @return array Extracted data
     */
    abstract protected function extract(array $pkgData, string $lockFile): array;


    /**
     * Processes raw data
     *
     * @param \Shieldon\SimpleCache\Cache $cache Cache object
     * @param array $config Configuration options
     * @return \S1SYPHOS\Packaging\Packages Processed data
     */
    abstract protected function process(\Shieldon\SimpleCache\Cache $cache, array $config): \S1SYPHOS\Packaging\Packages;
}
