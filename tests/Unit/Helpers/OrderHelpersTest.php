<?php

use Srmklive\PayPal\Services\PayPal as PayPalClient;

// ---------------------------------------------------------------------------
// getCaptureIdFromOrder — pure array accessor, no mocking needed
// ---------------------------------------------------------------------------

it('getCaptureIdFromOrder returns capture ID from nested order response', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $order = [
        'purchase_units' => [[
            'payments' => [
                'captures' => [['id' => 'CAP-123ABC']],
            ],
        ]],
    ];

    expect($client->getCaptureIdFromOrder($order))->toBe('CAP-123ABC');
});

it('getCaptureIdFromOrder returns null when order is empty', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    expect($client->getCaptureIdFromOrder([]))->toBeNull();
});

it('getCaptureIdFromOrder returns null when captures are missing', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $order = ['purchase_units' => [[]]];

    expect($client->getCaptureIdFromOrder($order))->toBeNull();
});

it('getCaptureIdFromOrder returns null when purchase_units is empty', function () {
    $client = $this->createPartialMock(PayPalClient::class, []);

    $order = ['purchase_units' => []];

    expect($client->getCaptureIdFromOrder($order))->toBeNull();
});

// ---------------------------------------------------------------------------
// createOrderWithPaymentSource — delegates to createOrder
// ---------------------------------------------------------------------------

it('createOrderWithPaymentSource passes payment source to createOrder', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createOrder']);

    $client->setPaymentSourcePayPal(['description' => 'Test PayPal checkout']);

    $orderData   = ['intent' => 'CAPTURE', 'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]]];
    $expectedArg = array_merge($orderData, [
        'payment_source' => ['paypal' => ['description' => 'Test PayPal checkout']],
    ]);

    $client->expects($this->once())
        ->method('createOrder')
        ->with($expectedArg)
        ->willReturn(['id' => 'ORDER-123', 'status' => 'CREATED']);

    $result = $client->createOrderWithPaymentSource($orderData);

    expect($result)->toBe(['id' => 'ORDER-123', 'status' => 'CREATED']);
});

it('createOrderWithPaymentSource creates order without payment_source key when none set', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createOrder']);

    $orderData = ['intent' => 'CAPTURE', 'purchase_units' => [['amount' => ['currency_code' => 'USD', 'value' => '10.00']]]];

    $client->expects($this->once())
        ->method('createOrder')
        ->with($orderData)
        ->willReturn(['id' => 'ORDER-456', 'status' => 'CREATED']);

    $result = $client->createOrderWithPaymentSource($orderData);

    expect($result)->toBe(['id' => 'ORDER-456', 'status' => 'CREATED']);
});

it('createOrderWithPaymentSource nests experience_context inside payment source method', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['createOrder']);

    $client->setPaymentSourcePayPal(['description' => 'Test']);
    $experienceContext = (new ReflectionProperty(PayPalClient::class, 'experience_context'));
    $experienceContext->setValue($client, ['return_url' => 'https://example.com/return', 'cancel_url' => 'https://example.com/cancel']);

    $client->expects($this->once())
        ->method('createOrder')
        ->willReturnCallback(function (array $data) {
            expect($data['payment_source']['paypal'])->toHaveKey('experience_context');
            expect($data['payment_source']['paypal']['experience_context']['return_url'])->toBe('https://example.com/return');

            return ['id' => 'ORDER-789'];
        });

    $client->createOrderWithPaymentSource(['intent' => 'CAPTURE', 'purchase_units' => []]);
});

// ---------------------------------------------------------------------------
// setupOrderConfirmation — delegates to confirmOrder
// ---------------------------------------------------------------------------

it('setupOrderConfirmation calls confirmOrder with payment source and instruction', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['confirmOrder']);

    $client->setPaymentSourcePayPal(['description' => 'Test']);

    $client->expects($this->once())
        ->method('confirmOrder')
        ->with('ORDER-123', [
            'processing_instruction' => 'ORDER_COMPLETE_ON_PAYMENT_APPROVAL',
            'payment_source'         => ['paypal' => ['description' => 'Test']],
        ])
        ->willReturn(['id' => 'ORDER-123', 'status' => 'APPROVED']);

    $result = $client->setupOrderConfirmation('ORDER-123', 'ORDER_COMPLETE_ON_PAYMENT_APPROVAL');

    expect($result)->toBe(['id' => 'ORDER-123', 'status' => 'APPROVED']);
});

it('setupOrderConfirmation defaults to paypal key when no payment source set', function () {
    $client = $this->createPartialMock(PayPalClient::class, ['confirmOrder']);

    $experienceContext = (new ReflectionProperty(PayPalClient::class, 'experience_context'));
    $experienceContext->setValue($client, ['return_url' => 'https://example.com/return']);

    $client->expects($this->once())
        ->method('confirmOrder')
        ->willReturnCallback(function (string $orderId, array $body) {
            expect($body['payment_source'])->toHaveKey('paypal');
            expect($body['payment_source']['paypal']['experience_context']['return_url'])->toBe('https://example.com/return');

            return ['id' => $orderId, 'status' => 'APPROVED'];
        });

    $client->setupOrderConfirmation('ORDER-456');
});
