<?php

namespace TextParams\TextRu\Api;

use GuzzleHttp\ClientInterface;
use TextParams\TextRu\Api\Exception\ApiException;
use TextParams\TextRu\Api\Model\CheckResult;

/**
 * API client.
 */
class Client
{
    /**
     * Default API URI.
     */
    const API_URI = 'http://api.text.ru/';

    /**
     * User API key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * HTTP client.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * Constructor.
     *
     * @param string $apiKey User API key.
     * @param ClientInterface $client HTTP client
     */
    public function __construct($apiKey, ClientInterface $client)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    /**
     * Initiates request for text check and returns unique text identifier.
     *
     * @param string $text
     * @param string $resultCallback URI of result callback
     * @param bool $isResultPublic
     * @param bool $hasResultVisualReport
     * @param array $excludedDomains
     *
     * @return string Unique text identifier
     *
     * @throws ApiException
     */
    public function check(
        $text,
        $resultCallback = null,
        $isResultPublic = false,
        $hasResultVisualReport = false,
        array $excludedDomains = []
    )
    {
        try {
            $data = $this->prepareCheckData(
                $text,
                $resultCallback,
                $isResultPublic,
                $hasResultVisualReport,
                $excludedDomains
            );

            $result = $this->client->post(self::API_URI . 'post', ['body' => $data])->json();

            return $result;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Tries to get text check result.
     *
     * @param string $textId Text unique identifier
     *
     * @return CheckResult
     *
     * @throws ApiException
     */
    public function tryGetResult($textId)
    {
        try {
            $result = (string) $this
                ->client
                ->post(
                    self::API_URI . 'post',
                    ['body' => ['uid' => $textId, 'userkey' => $this->apiKey, 'jsonvisible' => 'detail']]
                )->getBody()
            ;

            return $this->parseResult($result, $textId);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns available symbols.
     *
     * @return int
     *
     * @throws ApiException
     */
    public function availableSymbols()
    {
        try {
            $result = $this
                ->client
                ->post(
                    self::API_URI . 'account',
                    ['body' => ['method' => 'get_packages_info', 'userkey' => $this->apiKey]]
                )
                ->json()
            ;

            return $result;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Tries to parse result and returns object model.
     *
     * @param string $requestContent
     * @param null|string $textId
     *
     * @return CheckResult
     */
    public function parseResult($requestContent, $textId = null)
    {
        $result = json_decode($requestContent, true);

        $seoResult = isset($result['seo_check']) ? json_decode($result['seo_check'], true) : [];
        $waterPercent = isset($seoResult['water_percent']) ? $seoResult['water_percent'] : 0;

        if (null === $textId) {
            $textId = $result['uid'];
        }

        return new CheckResult($textId, (float) $result['text_unique'], $waterPercent);
    }

    /**
     * Prepares data ready for request.
     *
     * @param string $text
     * @param string $resultCallback URI of result callback
     * @param bool $isResultPublic
     * @param bool $hasResultVisualReport
     * @param array $excludedDomains
     *
     * @return array Returns data ready for request
     */
    private function prepareCheckData(
        $text,
        $resultCallback = null,
        $isResultPublic = false,
        $hasResultVisualReport = true,
        array $excludedDomains = []
    ) {
        $data = [
            'text' => $text,
            'userkey' => $this->apiKey
        ];

        if (! empty($resultCallback)) {
            $data['callback'] = $resultCallback;
        }

        if ($isResultPublic) {
            $data['visible'] = 'vis_on';
        }

        if (! $hasResultVisualReport) {
            $data['copying'] =  'noadd';
        }

        if (0 !== count($excludedDomains)) {
            $data['exceptdomain'] = implode(' ', $excludedDomains);
        }

        return $data;
    }
}
