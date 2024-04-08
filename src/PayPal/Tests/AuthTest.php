<?php

namespace EwertonDaniel\PayPal\Tests;

use EwertonDaniel\PayPal\Auth;
use EwertonDaniel\PayPal\Traits\DisplayColor;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    use DisplayColor;

    const CLIENT_ID = 'Client id';
    const CLIENT_SECRET = 'Client secret';
    protected Auth $auth;

    function setUp(): void
    {
        $this->auth = new Auth(self::CLIENT_ID, self::CLIENT_SECRET);
    }


    function testScopes()
    {
        $scopes = $this->auth->getScopes();
        if (!empty($scopes)) {
            print $this->success('Scopes');
            foreach ($scopes as $scope) {
                print $this->attention($scope);
            }
        }
        $this->assertIsArray($scopes);
    }

    function testAccessToken()
    {
        $access_token = $this->auth->getAccessToken();
        if ($access_token) {
            print $this->success('Access Token');
            print $this->information($access_token, true);
        }
        $this->assertNotNull($access_token);
    }

    function testTokenType()
    {
        $token_type = $this->auth->getTokenType();
        if ($token_type) {
            print $this->success('Token Type');
            print $this->information($token_type, true);
        }
        $this->assertNotNull($token_type);
    }

    function testAppId()
    {
        $app_id = $this->auth->getAppId();
        if ($app_id) {
            print  $this->success('Application ID');
            print $this->information($app_id, true);
        }
        $this->assertIsString($app_id);
    }

    function testExpiresIn()
    {
        $expires_in = $this->auth->getExpiresIn();
        if ($expires_in) {
            print  $this->success('Expires in');
            print $this->information($expires_in, true);
        }
        $this->assertIsInt($expires_in);
    }

    function testNonce()
    {
        $nonce = $this->auth->getNonce();
        if ($nonce) {
            print  $this->success('Nonce');
            print $this->information($nonce, true);
        }
        $this->assertIsString($nonce);
    }

}