# newrelic-api
Simple php wrapper for the New Relic api, based on https://gist.github.com/HarryR/3177007

##Installation
With Composer

    "require": {
        ...
        "kevbaldwyn/newrelic-api": "dev-master"
        ...
    }

Composer Update:

    $ composer update kevbaldwyn/newrelic-api

##Usage
Instantiate the `ApiClient` with your credentials:

    use KevBaldwyn\NewRelicApi\ApiClient;

    $api = new ApiClient('api-key', 'account-id');


Create a request object and call it:

    // in this case send a deployment
    $req = $api->sendDeployment('app-id', 'User Name', 'Description', 'Change log', 'version');
    $api->call($req);

The call method returns an instance of `GuzzleHttp\Message\Response` so that can be interrogated to get the response data ie:

    $res = $api->call($req);

    // check response code
    if($res->getStatusCode() == 200) {
        $xml = $res->xml();
    }

##Available wrapper methods
- getApplications()
- getSummary()
- listMetrics()
- getData()
- sendDeployment()

Other calls can be made by manually building a request using `buildRequest` and then calling it ie:

    $req = $api->buildRequest();
    $api->call($req);