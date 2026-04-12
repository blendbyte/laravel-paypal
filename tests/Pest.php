<?php

use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;
use PHPUnit\Framework\TestCase;

pest()->extend(TestCase::class)
    ->use(MockClientClasses::class, MockResponsePayloads::class)
    ->in('Unit', 'Feature');
