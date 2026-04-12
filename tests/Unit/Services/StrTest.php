<?php

use Srmklive\PayPal\Services\Str;

// ---------------------------------------------------------------------------
// Valid JSON — all standard JSON value types
// ---------------------------------------------------------------------------

it('returns true for a JSON object', function () {
    expect(Str::isJson('{}'))->toBeTrue();
    expect(Str::isJson('{"key":"value"}'))->toBeTrue();
    expect(Str::isJson('{"nested":{"a":1},"arr":[1,2],"flag":true}'))->toBeTrue();
});

it('returns true for a JSON array', function () {
    expect(Str::isJson('[]'))->toBeTrue();
    expect(Str::isJson('[1,2,3]'))->toBeTrue();
    expect(Str::isJson('[{"id":1},{"id":2}]'))->toBeTrue();
});

it('returns true for a JSON string primitive', function () {
    expect(Str::isJson('"hello"'))->toBeTrue();
    expect(Str::isJson('""'))->toBeTrue();
});

it('returns true for a JSON number primitive', function () {
    expect(Str::isJson('0'))->toBeTrue();
    expect(Str::isJson('123'))->toBeTrue();
    expect(Str::isJson('-3.14'))->toBeTrue();
});

it('returns true for JSON boolean and null primitives', function () {
    expect(Str::isJson('true'))->toBeTrue();
    expect(Str::isJson('false'))->toBeTrue();
    expect(Str::isJson('null'))->toBeTrue();
});

// ---------------------------------------------------------------------------
// Invalid JSON — malformed strings
// ---------------------------------------------------------------------------

it('returns false for an empty string', function () {
    expect(Str::isJson(''))->toBeFalse();
});

it('returns false for a bare word', function () {
    expect(Str::isJson('not json'))->toBeFalse();
    expect(Str::isJson('undefined'))->toBeFalse();
});

it('returns false for a JS-style object with unquoted keys', function () {
    expect(Str::isJson('{key: "value"}'))->toBeFalse();
});

it('returns false for a truncated JSON string', function () {
    expect(Str::isJson('{"key":'))->toBeFalse();
    expect(Str::isJson('[1, 2,'))->toBeFalse();
});

it('returns false for trailing comma JSON', function () {
    expect(Str::isJson('{"key":"value",}'))->toBeFalse();
});

// ---------------------------------------------------------------------------
// Non-string input — must return false immediately
// ---------------------------------------------------------------------------

it('returns false for an integer', function () {
    expect(Str::isJson(123))->toBeFalse();
});

it('returns false for null', function () {
    expect(Str::isJson(null))->toBeFalse();
});

it('returns false for an array', function () {
    expect(Str::isJson([]))->toBeFalse();
    expect(Str::isJson(['key' => 'value']))->toBeFalse();
});

it('returns false for a boolean', function () {
    expect(Str::isJson(true))->toBeFalse();
    expect(Str::isJson(false))->toBeFalse();
});

it('returns false for a float', function () {
    expect(Str::isJson(3.14))->toBeFalse();
});
