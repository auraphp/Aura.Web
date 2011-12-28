<?php
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

    /**
     * 
     * Retrieves an **unfiltered** value from a user input.
     * 
     * A value by key from the `$post` *and* `$files` properties, or an 
     * alternate default value if that key does not exist in either location.
     * Files takes precedence over post.
     * 
     * If the key is null and the content type isn't `multipart/form-data` and 
     * `$post` and `$files` are empty, the raw data from the request body 
     * is returned. 
     * 
     * @param string $key The $post and $files key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist in
     * either $post or $files.
     * 
     * @return mixed The value of $post[$key] combined with $files[$key], or the
     * raw request body, or the alternate default value.
     * 
     */
    public function getInput($key = null, $alt = null)
    {
        $post  = $this->getValue('post', $key, false);
        $files = $this->getValue('files', $key, false);
        
        $parts = explode(';', $this->getServer('CONTENT_TYPE'), 2);
        $ctype = trim(array_shift($parts));
        
        // POST or PUT data. It could be anything, a urlencoded string, xml, json, etc
        // So it is returned the way PHP received it.
        $use_raw = null === $key
                && 'multipart/form-data' != $ctype
                && empty($post)
                && empty($files);
                
        if ($use_raw) {
            return $this->raw_input;
        }
        
        // no matches in post or files
        if (! $post && ! $files) {
            return $alt;
        }
        
        // match in post, not in files
        if ($post && ! $files) {
            return $post;
        }
        
        // match in files, not in post
        if (! $post && $files) {
            return $files;
        }
        
        // are either or both arrays?
        $post_array  = is_array($post);
        
        // files is always an array so we test for a multidimensional array
        $files_array = is_array($files[key($files)]);
        
        // neither are arrays, append to files
        if (! $post_array && ! $files_array) {
            array_push($files, $post);
            return $files;
        }
        
        // files array single/array post, append to files
        if ($files_array) {
            foreach ($files as $key => $file) {
                if ($post_array) {
                    if (isset($post[$key])) {
                        $files[$key] = array_merge((array) $post[$key], $files[$key]);
                        unset($post[$key]);
                    }
                } else {
                    $files[$key][] = $post;
                }
            }
            // merge the remaining post values
            return ($post_array && ! empty($post)) ?
                        array_merge((array) $post, $files) : $files;
        }
        
        // post array but single files, append to post
        return array_merge($post, $files);
    }
    
    public function testGetInput()
    {
        $this->reset();
        $_POST['foo']  = 'bar';
        $_FILES['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $context = $this->newContext();
        
        // match in post, not in files
        $actual = $context->getInput('foo');
        $this->assertSame('bar', $actual);
        
        // match in files, not in post
        $actual = $context->getInput('baz');
        $this->assertSame('dib', $actual['name']);
        
        // no matches returns null
        $actual = $context->getInput('zim');
        $this->assertNull($actual);
        
        // no matches returns alt
        $actual = $context->getInput('zim', 'gir');
        $this->assertSame('gir', $actual);
    }

    public function testgetInputWithPostAndFile()
    {
        $this->reset();
        $_FILES['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_POST['baz']  = 'foo';
        $context                = $this->newContext();
        $actual             = $context->getInput('baz');
        
        $this->assertSame('dib', $actual['name']);
        $this->assertSame('foo', $actual[0]);
    }

    public function testgetInputWithMultiplePostsAndFile()
    {
        $this->reset();
        $_FILES['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_POST['baz']  = array(
            'foo', 
            'name' => 'files-take-precedence',
            'var'  => 123,
            );
        $context                = $this->newContext();
        $actual             = $context->getInput('baz');
        
        $this->assertSame('dib', $actual['name']);
        $this->assertSame(123,   $actual['var']);
        $this->assertSame('foo', $actual[0]);
    }

    public function testgetInputWithPostAndMultipleFiles()
    {
        $this->reset();
        // baz[]
        $_POST['baz']  = 'bars';
        $_FILES['baz'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        // upload[file1]
        $_POST['upload']  = 'bars';
        $_FILES['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_FILES['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $context    = $this->newContext();
        $actual = $context->getInput('baz');
        
        $this->assertSame('foo',  $actual[0]['name']);
        $this->assertSame('fooz', $actual[1]['name']);
        
        // post value is inserted into each file
        $this->assertSame('bars', $actual[0][0]);
        $this->assertSame('bars', $actual[1][0]);
        
        $actual = $context->getInput('upload');
        
        $this->assertSame('file1.bar', $actual['file1']['name']);
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        // post value is inserted into each file
        $this->assertSame('bars', $actual['file1'][0]);
        $this->assertSame('bars', $actual['file2'][0]);
    }

    public function testgetInputWithMultiplePostsAndMultipleFiles()
    {
        $this->reset();
        // baz[]
        $_POST['baz']  = array(
            'mars', 
            array(
                0      => 'bars',
                'name' => 'files-take-precedence',
        ));
        $_FILES['baz'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        
        // upload[file1]
        $_POST['upload']  = array(
            'file1' => 'mars', 
            'file2' => array(
                0      => 'bars',
                'name' => 'files-take-precedence'
        ));
        $_FILES['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $_FILES['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        
        $context    = $this->newContext();
        $actual = $context->getInput('baz');
        
        $this->assertSame('fooz', $actual[1]['name']);
        
        // post values is inserted
        $this->assertSame('mars', $actual[0][0]);
        $this->assertSame('bars', $actual[1][0]);
        
        $actual = $context->getInput('upload');
        
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        // post value is inserted
        $this->assertSame('mars', $actual['file1'][0]);
        $this->assertSame('bars', $actual['file2'][0]);
    }
    
