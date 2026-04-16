<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait Identity
{
    /**
     * Get user profile information.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v1/#userinfo_get
     */
    public function showProfileInfo()
    {
        $this->apiEndPoint = 'v1/identity/openidconnect/userinfo?schema=openid';

        $this->setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * List Users.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v2/#users_list
     */
    public function listUsers(string $field = 'userName')
    {
        $this->apiEndPoint = 'v2/scim/Users?filter='.rawurlencode($field);

        $this->setRequestHeader('Content-Type', 'application/scim+json');

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Show details for a user by ID.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v2/#users_get
     */
    public function showUserDetails(string $user_id)
    {
        $this->apiEndPoint = "v2/scim/Users/{$user_id}";

        $this->setRequestHeader('Content-Type', 'application/scim+json');

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Delete a user by ID.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v2/#users_get
     */
    public function deleteUser(string $user_id)
    {
        $this->apiEndPoint = "v2/scim/Users/{$user_id}";

        $this->setRequestHeader('Content-Type', 'application/scim+json');

        $this->verb = 'delete';

        return $this->doPayPalRequest(false);
    }

    /**
     * Create a merchant application.
     *
     *
     *
     * @param list<string> $redirect_uris
     * @param array<string, mixed> $contacts
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v1/#applications_post
     */
    public function createMerchantApplication(string $client_name, array $redirect_uris, array $contacts, string $payer_id, string $migrated_app, string $application_type = 'web', string $logo_url = '')
    {
        $this->apiEndPoint = 'v1/identity/applications';

        $this->options['json'] = array_filter([
            'application_type' => $application_type,
            'redirect_uris' => $redirect_uris,
            'client_name' => $client_name,
            'contacts' => $contacts,
            'payer_id' => $payer_id,
            'migrated_app' => $migrated_app,
            'logo_uri' => $logo_url,
        ]);

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Set account properties / features for a merchant account.
     *
     *
     *
     * @param list<string> $features
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v1/#account-settings_post
     */
    public function setAccountProperties(array $features, string $account_property = 'BRAINTREE_MERCHANT')
    {
        $this->apiEndPoint = 'v1/identity/account-settings';

        $this->options['json'] = [
            'account_property' => $account_property,
            'features' => $features,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Deactivate account properties / features for a merchant account.
     *
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v1/#account-settings_deactivate
     */
    public function disableAccountProperties(string $account_property = 'BRAINTREE_MERCHANT')
    {
        $this->apiEndPoint = 'v1/identity/account-settings/deactivate';

        $this->options['json'] = [
            'account_property' => $account_property,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Get a client token.
     *
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/multiparty/checkout/advanced/integrate/#link-sampleclienttokenrequest
     */
    public function getClientToken()
    {
        $this->apiEndPoint = 'v1/identity/generate-token';

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * Generate a client token for use with PayPal Fastlane or Advanced Card Payments.
     *
     * Alias for getClientToken(). Pass the returned client_token to the
     * PayPal JS SDK to initialise Fastlane on the client side.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/checkout/fastlane/
     */
    public function generateClientToken()
    {
        return $this->getClientToken();
    }
}
