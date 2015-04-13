<?php namespace KevBaldwyn\NewRelicApi;

use DateTime;
use GuzzleHttp\Message\RequestInterface;
use InvalidArgumentException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;

class ApiClient {

    private $apiKey;
    private $accountID;
    private $client;

    const RPM_URL = 'https://rpm.newrelic.com';
    const API_URL = 'https://api.newrelic.com';

    public function __construct($apiKey, $accountID, ClientInterface $client = null)
    {
        if(is_null($client)) {
            $client = new Client();
        }

        $this->apiKey    = $apiKey;
        $this->accountID = (int) $accountID;
        $this->client    = $client;
    }

    public static function date(DateTime $date)
    {
        return $date->format('Y-m-d') . 'T' . $date->format('H:i:s') . 'Z';
    }

    public function getApplications()
    {
        return $this->buildRequest(self::RPM_URL.'/accounts/'.$this->accountID.'/applications.xml');
    }

    public function getSummary($appID)
    {
        return $this->buildRequest(self::RPM_URL.'/accounts/'.$this->accountID.'/applications/'.$appID.'/threshold_values.xml');
    }

    public function listMetrics($appID)
    {
        return $this->buildRequest(self::API_URL.'/api/v1/applications/'.$appID.'/metrics.xml');
    }

    public function getData($appID, DateTime $begin, DateTime $end, $metrics, $field, $summary = false)
    {
        $data = array(
            'metrics' => $metrics,
            'begin' => static::date($begin),
            'end' => static::date($end),
        );
        if( $field ) {
            $data['field'] = $field;
        }
        if( $summary ) {
            $data['summary'] = 1;
        }
        return $this->buildRequest(self::API_URL.'/api/v1/accounts/'.$this->accountID.'/applications/'.$appID.'/data.xml', 'get', $data);
    }

    public function sendDeployment($appID, $user, $description, $changelog, $revision)
    {
        $data = array(
            'deployment[application_id]' => $appID,
            'deployment[description]' => $description,
            'deployment[changelog]' => $changelog,
            'deployment[user]' => $user,
            'deployment[revision]' => $revision,
        );
        return $this->buildRequest(self::API_URL . '/deployments.xml', 'post', $data);
    }

    public function buildRequest($url, $method = 'get', array $data = array())
    {
        $method = strtolower($method);
        if ($method != 'get' && $method != 'post') {
            throw new InvalidArgumentException('New Relic api method must be either "get" or "post"');
        }

        if ($method == 'post') {
            $req = $this->client->createRequest('POST', $url, [
                'body' => $data
            ]);
        }else{
            if( count($data) ) {
                $url .= '?' . http_build_query($data);
            }
            $req = $this->client->createRequest('GET', $url);
        }

        // set authentication header
        $req->setHeader('x-api-key', $this->apiKey);

        return $req;
    }

    public function call(RequestInterface $req)
    {
        $response = $this->client->send($req);

        return $response;
    }
}