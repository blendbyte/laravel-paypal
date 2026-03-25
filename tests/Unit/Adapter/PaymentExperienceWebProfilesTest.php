<?php

namespace Blendbyte\PayPal\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Blendbyte\PayPal\Tests\MockClientClasses;
use Blendbyte\PayPal\Tests\MockRequestPayloads;
use Blendbyte\PayPal\Tests\MockResponsePayloads;

class PaymentExperienceWebProfilesTest extends TestCase
{
    use MockClientClasses;
    use MockRequestPayloads;
    use MockResponsePayloads;

    #[Test]
    public function it_can_list_web_experience_profiles(): void
    {
        $expectedResponse = $this->mockListWebProfilesResponse();

        $expectedMethod = 'listWebExperienceProfiles';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}());
    }

    #[Test]
    public function it_can_create_web_experience_profile(): void
    {
        $expectedResponse = $this->mockWebProfileResponse();

        $expectedParams = $this->mockCreateWebProfileParams();

        $expectedMethod = 'createWebExperienceProfile';
        $additionalMethod = 'setRequestHeader';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true, $additionalMethod);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();
        $mockClient->{$additionalMethod}('PayPal-Request-Id', 'some-request-id');

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_delete_web_experience_profile(): void
    {
        $expectedResponse = '';

        $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

        $expectedMethod = 'deleteWebExperienceProfile';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }

    #[Test]
    public function it_can_partially_update_web_experience_profile(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->partiallyUpdateWebProfileParams();

        $expectedMethod = 'patchWebExperienceProfile';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams));
    }

    #[Test]
    public function it_can_fully_update_web_experience_profile(): void
    {
        $expectedResponse = '';

        $expectedParams = $this->updateWebProfileParams();

        $expectedMethod = 'updateWebExperienceProfile';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}('XP-A88A-LYLW-8Y3X-E5ER', $expectedParams));
    }

    #[Test]
    public function it_can_get_web_experience_profile_details(): void
    {
        $expectedResponse = $this->mockWebProfileResponse();

        $expectedParams = 'XP-A88A-LYLW-8Y3X-E5ER';

        $expectedMethod = 'showWebExperienceProfileDetails';

        $mockClient = $this->mock_client($expectedResponse, $expectedMethod, true);

        $mockClient->setApiCredentials($this->getMockCredentials());
        $mockClient->getAccessToken();

        $this->assertEquals($expectedResponse, $mockClient->{$expectedMethod}($expectedParams));
    }
}
