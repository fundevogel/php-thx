<?php

declare(strict_types=1);

namespace Fundevogel\Thx\Drivers;

use GuzzleHttp\Client;
use Spyc;

/**
 * Class Driver
 *
 * Base class for all package drivers
 */
abstract class Driver
{
    /**
     * Properties
     */

    /**
     * Defines timeout for API requests (in seconds)
     *
     * @var int
     */
    public int $timeout = 3;


    /**
     * Controls `User-Agent` header
     *
     * @var string
     */
    public string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0';


    /**
     * Constructor
     *
     * @param array $pkgData Path to data file
     * @param string $lockFile Lockfile contents
     * @return void
     */
    public function __construct(public array $pkgData, public string $lockFile)
    {
    }


    /**
     * Methods
     */

    /**
     * Fetches information from API endpoint
     *
     * @param string $apiURL API endpoint
     * @param int $timeout Request timeout (in seconds)
     * @param string $userAgent User-Agent header
     * @return string Response text - empty if connection failed
     * @throws \Exception
     */
    protected function fetchRemote(string $apiURL): string
    {
        # Initialize HTTP client
        $client = new Client(['timeout'  => $this->timeout]);

        # Fetch data from API
        # (1) Send GET request
        $response = $client->get($apiURL, ['headers' => ['User-Agent' => $this->userAgent]]);

        # (2) If successful ..
        if ($response->getStatusCode() === 200) {
            # .. save response
            return (string) $response->getBody();
        }

        throw new \Exception(sprintf('Downloading data from "%s" failed!', $apiURL));
    }


    /**
     * Spreads love
     *
     * Extracts data from files & retrieves additional information,
     * providing hooks for both tasks & delegating them to its subclasses
     *
     * @return array
     */
    public function spreadLove(): array
    {
        # Extract data from input files
        $data = $this->extract();

        # Enrich results using API
        return $this->extend($data);
    }


    /**
     * Extracts raw data from input files
     *
     * @return array Extracted data
     */
    abstract protected function extract(): array;


    /**
     * Retrieves additional package information
     *
     * @param array $data Extracted data
     * @return array Processed data
     */
    abstract protected function extend(array $data): array;


    /**
     * Helpers
     */

    /**
     * Parses YAML string
     *
     * @param string $stream YAML file content
     * @return array Extracted data
     */
    protected function parseYaml(string $stream): array
    {
        # Remove BOM
        $string = str_replace("\xEF\xBB\xBF", '', $stream);

        # Load YAML data
        return Spyc::YAMLLoadString($string);
    }
}
