<?php

use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockResponsePayloads;
use PHPUnit\Framework\TestCase;

uses(
    TestCase::class,
    MockClientClasses::class,
    MockResponsePayloads::class,
)->in('Unit', 'Feature');
