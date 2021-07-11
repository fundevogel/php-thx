<?php

namespace S1SYPHOS\Traits;


trait Remote
{
    /**
     * Properties
     */

    /**
     * Defines timeout for API requests (in seconds)
     *
     * @var int
     */
    protected $timeout = 3;


    /**
     * Controls `User-Agent` header
     *
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0';


    /**
     * Setters & getters
     */

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }


    public function getTimeout(): string
    {
        return $this->timeout;
    }


    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }


    public function getUserAgent(): string
    {
        return $this->userAgent;
    }


    /**
     * Methods
     */

    protected function fetchRemote(string $apiURL): string
    {
        # Initialize HTTP client
        $client = new \GuzzleHttp\Client(['timeout'  => $this->timeout]);

        try {
            $response = $client->get($apiURL, ['headers' => ['User-Agent' => $this->userAgent]]);
        } catch (\GuzzleHttp\Exception\TransferException $e) {
            return '';
        }

        if ($response->getStatusCode() === 200) {
            return $response->getBody();
        }

        # (3) .. otherwise, transmission *may* have worked
        return '';
    }
}
