<?php

use Illuminate\Support\Facades\Route;
use Srmklive\PayPal\Http\Controllers\PayTheFlyWebhookController;
use Srmklive\PayPal\Http\Middleware\VerifyPayTheFlyWebhook;

/*
|--------------------------------------------------------------------------
| PayTheFly Webhook Route
|--------------------------------------------------------------------------
|
| This route handles incoming PayTheFly webhook notifications.
| The VerifyPayTheFlyWebhook middleware validates the HMAC signature
| before the request reaches the controller.
|
| IMPORTANT: This route is excluded from CSRF verification by default
| since webhooks are server-to-server. Add this path to your
| VerifyCsrfToken middleware's $except array if needed:
|
|     protected $except = [
|         'paythefly/*',
|     ];
|
*/

$webhookPath = config('paythefly.webhook.path', '/paythefly/webhook');
$webhookMiddleware = array_merge(
    config('paythefly.webhook.middleware', ['api']),
    [VerifyPayTheFlyWebhook::class]
);

Route::post($webhookPath, PayTheFlyWebhookController::class)
    ->middleware($webhookMiddleware)
    ->name('paythefly.webhook');
