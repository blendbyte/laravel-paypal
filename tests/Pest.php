<?php

use Srmklive\PayPal\Tests\MockClientClasses;
use Srmklive\PayPal\Tests\MockResponsePayloads;
use PHPUnit\Framework\TestCase;

pest()->extend(TestCase::class)
    ->use(MockClientClasses::class, MockResponsePayloads::class)
    ->in('Unit', 'Feature');
