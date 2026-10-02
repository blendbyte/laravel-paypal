<?php

use Srmklive\PayPal\Exceptions\PayPalApiException;
use Srmklive\PayPal\Testing\MockPayPalClient;

beforeEach(function () {
    $this->mock = new MockPayPalClient();
    $this->client = $this->mock->mockProvider();
});

it('does not re-send a previous JSON body on a later request', function () {
    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->createOrder(['intent' => 'CAPTURE']);

    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->showOrderDetails('O-1');

    $request = $this->mock->lastRequest();
    expect($request->getMethod())->toBe('GET')
        ->and((string) $request->getBody())->toBe('')
        ->and($request->hasHeader('Content-Type'))->toBeFalse();
});

it('sends the token form body after a JSON request', function () {
    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->createOrder(['intent' => 'CAPTURE']);

    $this->mock->addResponse(['access_token' => 'new-token', 'token_type' => 'Bearer']);
    $this->client->getAccessToken();

    $request = $this->mock->lastRequest();
    expect($request->getHeaderLine('Content-Type'))->toBe('application/x-www-form-urlencoded')
        ->and((string) $request->getBody())->toBe('grant_type=client_credentials');
});

it('clears Basic auth credentials when the token request throws', function () {
    $this->client->withExceptions();

    $this->mock->addResponse(['error' => 'invalid_client'], 401);
    expect(fn () => $this->client->getAccessToken())->toThrow(PayPalApiException::class);

    $this->client->setAccessToken(['access_token' => 'cached-token', 'token_type' => 'Bearer']);
    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->showOrderDetails('O-1');

    $request = $this->mock->lastRequest();
    expect($request->getHeaderLine('Authorization'))->toBe('Bearer cached-token')
        ->and((string) $request->getBody())->toBe('');
});

it('does not re-send dispute evidence on a later request', function () {
    $this->mock->addResponse(['links' => []]);
    $this->client->provideDisputeEvidence('PP-D-1', [__DIR__.'/../Mocks/samples/sample.pdf']);

    expect($this->mock->lastRequest()->getHeaderLine('Content-Type'))->toStartWith('multipart/form-data; boundary=');

    $this->mock->addResponse(['dispute_id' => 'PP-D-1']);
    $this->client->showDisputeDetails('PP-D-1');

    $request = $this->mock->lastRequest();
    expect((string) $request->getBody())->toBe('')
        ->and($request->hasHeader('Content-Type'))->toBeFalse();
});

it('does not keep a per-request Content-Type for later requests', function () {
    $this->mock->addResponse(['Resources' => []]);
    $this->client->listUsers();

    expect($this->mock->lastRequest()->getHeaderLine('Content-Type'))->toBe('application/scim+json');

    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->showOrderDetails('O-1');

    expect($this->mock->lastRequest()->hasHeader('Content-Type'))->toBeFalse();
});

it('keeps persistent headers across requests', function () {
    $this->client->setPartnerAttributionId('Platform_SP');

    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->createOrder(['intent' => 'CAPTURE']);
    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->showOrderDetails('O-1');

    $request = $this->mock->lastRequest();
    expect($request->getHeaderLine('Authorization'))->toBe('Bearer mock-access-token')
        ->and($request->getHeaderLine('Accept'))->toBe('application/json')
        ->and($request->getHeaderLine('PayPal-Partner-Attribution-Id'))->toBe('Platform_SP');
});

it('sends the token request as POST after a GET request', function () {
    // Regression: getAccessToken() never set the verb and reused the previous one.
    $this->mock->addResponse(['id' => 'O-1']);
    $this->client->showOrderDetails('O-1');

    $this->mock->addResponse(['access_token' => 'new-token', 'token_type' => 'Bearer']);
    $this->client->getAccessToken();

    $request = $this->mock->lastRequest();
    expect($request->getMethod())->toBe('POST')
        ->and((string) $request->getUri())->toEndWith('/v1/oauth2/token');
});
