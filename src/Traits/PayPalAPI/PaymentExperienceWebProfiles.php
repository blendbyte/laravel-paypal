<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

/**
 * @deprecated PayPal has deprecated the v1/payment-experience/web-profiles API.
 *             Use the `experience_context` field on Orders v2 instead, via the
 *             fluent helpers in PayPalExperienceContext (setReturnUrl(),
 *             setCancelUrl(), setBrandName(), etc.).
 *
 * @see https://developer.paypal.com/docs/api/payment-experience/v1/
 * @see \Srmklive\PayPal\Traits\PayPalExperienceContext
 */
trait PaymentExperienceWebProfiles
{
    /**
     * List Web Experience Profiles.
     *
     * @deprecated Use Orders v2 with experience_context instead.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-experience/v1/#web-profiles_get-list
     */
    public function listWebExperienceProfiles()
    {
        $this->apiEndPoint = 'v1/payment-experience/web-profiles';

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Create a Web Experience Profile.
     *
     * @deprecated Use Orders v2 with experience_context instead.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-experience/v1/#web-profile_create
     */
    public function createWebExperienceProfile(array $data)
    {
        $this->apiEndPoint = 'v1/payment-experience/web-profiles';

        $this->options['json'] = $data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Delete a Web Experience Profile.
     *
     * @deprecated Use Orders v2 with experience_context instead.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-experience/v1/#web-profile_delete
     */
    public function deleteWebExperienceProfile(string $profile_id)
    {
        $this->apiEndPoint = "v1/payment-experience/web-profiles/{$profile_id}";

        $this->verb = 'delete';

        return $this->doPayPalRequest();
    }

    /**
     * Partially update a Web Experience Profile.
     *
     * @deprecated Use Orders v2 with experience_context instead.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-experience/v1/#web-profile_partial-update
     */
    public function patchWebExperienceProfile(string $profile_id, array $data)
    {
        $this->apiEndPoint = "v1/payment-experience/web-profiles/{$profile_id}";

        $this->options['json'] = $data;

        $this->verb = 'patch';

        return $this->doPayPalRequest();
    }

    /**
     * Update a Web Experience Profile.
     *
     * @deprecated Use Orders v2 with experience_context instead.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-experience/v1/#web-profile_update
     */
    public function updateWebExperienceProfile(string $profile_id, array $data)
    {
        $this->apiEndPoint = "v1/payment-experience/web-profiles/{$profile_id}";

        $this->options['json'] = $data;

        $this->verb = 'put';

        return $this->doPayPalRequest();
    }

    /**
     * Show details for a Web Experience Profile.
     *
     * @deprecated Use Orders v2 with experience_context instead.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/payment-experience/v1/#web-profile_get
     */
    public function showWebExperienceProfileDetails(string $profile_id)
    {
        $this->apiEndPoint = "v1/payment-experience/web-profiles/{$profile_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
