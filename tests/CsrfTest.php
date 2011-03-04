<?php

namespace aura\web;


class CsrfTest extends \PHPUnit_Framework_TestCase
{
    protected $secret = 'a-secret-key';
    protected $user   = 'foo-user';
    
    protected function newCsrf()
    {
        return new Csrf($this->secret, $this->user);
    }

    public function test__set()
    {
        $csrf = $this->newCsrf();
        
        // test that we can access and set without causing an exception
        $csrf->hash_algo  = 'foo-hash';
        $this->assertSame('foo-hash', $csrf->hash_algo);
        
        $csrf->timeout    = 42;
        $this->assertSame(42, $csrf->timeout);
        
        $csrf->secret_key = 'keys to the kingdom';
        $actual = $this->readAttribute($csrf, 'secret_key');
        $this->assertSame('keys to the kingdom', $actual);
        
        // invalid or protected should cause an exception
        $this->setExpectedException('\UnexpectedValueException');
        $csrf->invalid = 'xxx';
    }
    
    public function test__get()
    {
        $csrf = $this->newCsrf();
        
        // test that we can access without causing an exception
        $csrf->hash_algo;
        $csrf->timeout;
        
        // invalid or protected should cause an exception
        $this->setExpectedException('\UnexpectedValueException');
        $csrf->invalid;
    }
    
    public function testGenerateToken()
    {
        $csrf  = $this->newCsrf();
        $token = $csrf->generateToken();
        
        // extract the hash & raw token
        list ($hmac, $rawtoken) = explode('|', $token, 2);
        
        // recreate the hash based on the raw token & constructor arguments
        $rawtoken_hmac = hash_hmac($csrf->hash_algo, $rawtoken . $this->user,
                                    $this->secret);
        
        $this->assertSame($hmac, $rawtoken_hmac);
    }

    public function testIsValidToken()
    {
        $csrf  = $this->newCsrf();
        $token = $csrf->generateToken();
        
        $this->assertTrue($csrf->isValidToken($token));

        list($hmac, $time, $uniqueid) = explode('|', $token, 3);
        
        // token time changed
        $alt_time = $time + 1;
        $token    = $hmac . '|' . $alt_time . '|' . $uniqueid;
        $this->assertFalse($csrf->isValidToken($token));
        
        // token unique id changed
        $alt_uid  = $uniqueid . 'X';
        $token    = $hmac . '|' . $time . '|' . $alt_uid;
        $this->assertFalse($csrf->isValidToken($token));
        
        // token hmac changed
        $alt_hmac = $hmac . 'X';
        $token    = $alt_hmac . '|' . $time . '|' . $uniqueid;
        $this->assertFalse($csrf->isValidToken($token));
        
        // test timeout
        $csrf->timeout = 1;
        $token = $csrf->generateToken();
        sleep(2);
        $this->assertFalse($csrf->isValidToken($token));
        
        // invalid token format, should cause a exception
        $this->setExpectedException('aura\web\Exception_InvalidTokenFormat');
        $csrf->isValidToken('invalid-token');
    }
}
