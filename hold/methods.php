    /**
     * 
     * Is the current request a cross-site forgery?
     * 
     * Note: if the key does not exist this method will return true.
     * 
     * @throws Aura\Web\Exception\Context If a CSRF library has not been provided.
     * 
     * @param string $key The name of the $_POST key containing the CSRF token.
     * 
     * @return bool
     * 
     */
    public function isCsrf($key = '__csrf_token')
    {
        if (! $this->csrf) {
            throw new Exception\Context('A CSRF library has not been provided');
        }
        
        $token = $this->getValue('post', $key, 'invalid-token');
        
        try {
            // if the token is valid return false. This is not a csrf attack.
            return ! $this->csrf->isValidToken($token);
        } catch (Exception\MalformedToken $e) {
            return true;
        }
    }
    
    public function testIsCsrf()
    {
        $this->reset();
        $csrf  = $this->newCsrf();
        $_POST['__csrf_token'] = $csrf->generateToken();
        $context   = $this->newContext($this->newCsrf());
        
        $this->assertFalse($context->isCsrf());
        $this->assertTrue($context->isCsrf('invalid_key'));
        
        // if Csrf library is not provided an exception is thrown
        $this->reset();
        $context = $this->newContext();
        
        $this->setExpectedException('Aura\Web\Exception\Context');
        $context->isCsrf();
    }

