<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Psr\Http\Message\StreamInterface;

trait Identity
{
    /**
     * Get user profile information.
     *
     * Requires the user's access token from Log in with PayPal (authorization
     * code flow), set via setAccessToken(); the app token from getAccessToken()
     * is not sufficient.
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
     * @param string   $field       SCIM filter expression, e.g. 'userName eq "jdoe"'. Empty sends no filter.
     * @param int|null $start_index 1-based index of the first result (1-100000).
     * @param int|null $count       Results per page (0-100).
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/identity/v2/#users_list
     */
    public function listUsers(string $field = '', ?int $start_index = null, ?int $count = null)
    {
        $query = http_build_query(array_filter([
            // The old default of a bare attribute name ("userName") was not a valid
            // SCIM filter, so the default now sends no filter at all.
            'filter' => $field !== '' ? $field : null,
            'startIndex' => $start_index,
            'count' => $count,
        ], fn ($value) => $value !== null), '', '&', PHP_QUERY_RFC3986);

        $this->apiEndPoint = 'v2/scim/Users'.($query !== '' ? "?{$query}" : '');

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
     * Get a client token for JS SDK v5 hosted card fields (Advanced Card Payments).
     *
     * For PayPal Fastlane use generateFastlaneClientToken() instead.
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
     * Generate a client token for JS SDK v5 hosted card fields (Advanced Card Payments).
     *
     * Alias for getClientToken(). For PayPal Fastlane use
     * generateFastlaneClientToken() instead.
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     */
    public function generateClientToken()
    {
        return $this->getClientToken();
    }

    /**
     * Generate a browser-safe client token for PayPal Fastlane.
     *
     * Requests a client token from the OAuth token endpoint
     * (response_type=client_token) for the given domains. The token is
     * returned in 'access_token' (with 'expires_in' for caching) and is NOT
     * stored on the provider: the server-side access token is left unchanged.
     *
     * @param list<string> $domains Domains the token is used on, e.g. ['example.com'].
     *
     * @return array<string, mixed>|StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/sdk/js/set-up/#option-b-client-token-for-fastlane-only
     */
    public function generateFastlaneClientToken(array $domains)
    {
        $this->apiEndPoint = 'v1/oauth2/token';

        $this->verb = 'post';

        $this->options['auth'] = [$this->config['client_id'], $this->config['client_secret']];
        $this->options['form_params'] = array_filter([
            'grant_type' => 'client_credentials',
            'response_type' => 'client_token',
            'domains[]' => implode(',', $domains),
        ], fn ($value) => $value !== '');

        return $this->doPayPalRequest();
    }
}
