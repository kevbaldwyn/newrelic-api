<?php namespace Tests\Shaun\Support;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use GuzzleHttp\Client;
use KevBaldwyn\NewRelicApi\ApiClient;

class ApiClientTest extends PHPUnit_Framework_TestCase {

    public function testBuildRequest()
    {
        $client = new Client();

        $api = new ApiClient('12345', '123', $client);
        $r = $api->buildRequest('http://www.example.com', 'post', ['var' => 'value']);

        $this->assertSame('http://www.example.com', $r->getUrl());
        $this->assertSame('12345', $r->getHeader('x-api-key'));
    }

    public function testDeploymentData()
    {
        $client = new Client();

        $api = new ApiClient('12345', '123', $client);
        $r = $api->sendDeployment('app-id', 'me', 'desc', 'changelog', '1.0');

        $this->assertSame(ApiClient::API_URL . 'deployments.xml', $r->getUrl());
        $this->assertSame([
            "deployment[application_id]" => "app-id",
            "deployment[description]"    => "desc",
            "deployment[changelog]"      => "changelog",
            "deployment[user]"           => "me",
            "deployment[revision]"       => "1.0"
        ], $r->getBody()->getFields());
    }

}