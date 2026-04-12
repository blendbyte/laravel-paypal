<?php

use Srmklive\PayPal\Tests\MockRequestPayloads;

uses(MockRequestPayloads::class);

it('can create a product', function () {
    $expectedResponse = $this->mockCreateCatalogProductsResponse();

    $expectedParams = $this->createProductParams();

    $expectedMethod = 'createProduct';
    $additionalMethod = 'setRequestHeader';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();
    $mockClient->{$additionalMethod}('PayPal-Request-Id', 'some-request-id');

    expect($mockClient->{$expectedMethod}($expectedParams))->toBe($expectedResponse);
});

it('can list products', function () {
    $expectedResponse = $this->mockListCatalogProductsResponse();

    $expectedMethod = 'listProducts';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}())->toBe($expectedResponse);
});

it('can update a product', function () {
    $expectedResponse = '';

    $expectedParams = $this->updateProductParams();

    $expectedMethod = 'updateProduct';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('72255d4849af8ed6e0df1173', $expectedParams))->toBe($expectedResponse);
});

it('can get details for a product', function () {
    $expectedResponse = $this->mockGetCatalogProductsResponse();

    $expectedMethod = 'showProductDetails';

    $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

    $mockClient->setApiCredentials($this->getMockCredentials());
    $mockClient->getAccessToken();

    expect($mockClient->{$expectedMethod}('72255d4849af8ed6e0df1173'))->toBe($expectedResponse);
});
