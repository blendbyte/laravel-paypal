<?php

use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

it('requests a Fastlane client token from the OAuth endpoint', function () {
    $this->mock->addResponse(['access_token' => 'browser-safe-token', 'expires_in' => 900]);

    $response = $this->client->generateFastlaneClientToken(['example.com', 'shop.example.com']);

    $request = $this->mock->lastRequest();
    parse_str((string) $request->getBody(), $form);

    expect($response)->toBe(['access_token' => 'browser-safe-token', 'expires_in' => 900])
        ->and($request->getMethod())->toBe('POST')
        ->and((string) $request->getUri())->toEndWith('/v1/oauth2/token')
        ->and($request->getHeaderLine('Authorization'))->toBe('Basic '.base64_encode('mock-client-id:mock-client-secret'))
        ->and($request->getHeaderLine('Content-Type'))->toBe('application/x-www-form-urlencoded')
        ->and($form)->toBe([
            'grant_type' => 'client_credentials',
            'response_type' => 'client_token',
            'domains' => ['example.com,shop.example.com'],
        ]);
});

it('keeps the server access token after generating a Fastlane client token', function () {
    $this->mock->addResponse(['access_token' => 'browser-safe-token', 'expires_in' => 900]);
    $this->mock->addResponse(['id' => 'O-1']);

    $this->client->generateFastlaneClientToken(['example.com']);
    $this->client->showOrderDetails('O-1');

    expect($this->mock->lastRequest()->getHeaderLine('Authorization'))->toBe('Bearer mock-access-token');
});

it('builds the list users query', function (array $args, string $expectedSuffix) {
    $this->mock->addResponse(['Resources' => []]);

    $this->client->listUsers(...$args);

    expect((string) $this->mock->lastRequest()->getUri())->toEndWith($expectedSuffix);
})->with([
    'no filter' => [[], '/v2/scim/Users'],
    'bare attribute (invalid, ignored)' => [['userName'], '/v2/scim/Users'],
    'filter expression' => [['userName eq "jdoe"'], '/v2/scim/Users?filter=userName%20eq%20%22jdoe%22'],
    'paging' => [['', 11, 10], '/v2/scim/Users?startIndex=11&count=10'],
]);
