<?php

namespace aura\web;

require_once 'PhpStream.php';

class ContextTest extends \PHPUnit_Framework_TestCase
{
    protected $get    = array();
    protected $post   = array();
    protected $server = array();
    protected $cookie = array();
    protected $env    = array();
    protected $files  = array();
    
    
    protected function newContext($csrf = null)
    {
        return new Context($this->get, $this->post, $this->server, $this->cookie, 
                           $this->env, $this->files, $csrf);
    }
    
    protected function newCsrf()
    {
        return new Csrf('secret', 'usrid');
    }
    
    protected function reset()
    {
        $this->get    = array();
        $this->post   = array();
        $this->server = array();
        $this->cookie = array();
        $this->env    = array();
        $this->files  = array();
    }

    public function testHttpMethodOverload()
    {
        $this->reset();
        $this->server['REQUEST_METHOD']              = 'POST';
        $this->server['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';
        $req    = $this->newContext();
        $actual = $req->getServer('REQUEST_METHOD');
        
        $this->assertSame('PUT', $actual);
    }
    
    public function test__get()
    {
        $this->reset();
        $req = $this->newContext();
        
        // test that we can access without causing an exception
        $req->get;
        $req->post;
        $req->server;
        $req->cookie;
        $req->env;
        $req->files;
        $req->header;
        
        // invalid or protected should cause an exception
        $this->setExpectedException('\UnexpectedValueException');
        $req->invalid;
    }

    public function testIsGet()
    {
        $this->reset();
        $req = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($req->isGet());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'GET';
        $req = $this->newContext();
        $this->assertTrue($req->isGet());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'NOT-GET';
        $req = $this->newContext();
        $this->assertFalse($req->isGet());
    }

    public function testIsPost()
    {
        $this->reset();
        $req = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($req->isPost());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'POST';
        $req = $this->newContext();
        $this->assertTrue($req->isPost());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'NOT-POST';
        $req = $this->newContext();
        $this->assertFalse($req->isPost());
    }

    public function testIsPut()
    {
        $this->reset();
        $req = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($req->isPut());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'PUT';
        $req = $this->newContext();
        $this->assertTrue($req->isPut());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'NOT-PUT';
        $req = $this->newContext();
        $this->assertFalse($req->isPut());
    }

    public function testIsDelete()
    {
        $this->reset();
        $req = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($req->isDelete());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'DELETE';
        $req = $this->newContext();
        $this->assertTrue($req->isDelete());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'NOT-DELETE';
        $req = $this->newContext();
        $this->assertFalse($req->isDelete());
    }

    public function testIsHead()
    {
        $this->reset();
        $req = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($req->isHead());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'HEAD';
        $req = $this->newContext();
        $this->assertTrue($req->isHead());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'NOT-HEAD';
        $req = $this->newContext();
        $this->assertFalse($req->isHead());
    }

    public function testIsOptions()
    {
        $this->reset();
        $req = $this->newContext();
        
        // REQUEST_METHOD not set
        $this->assertFalse($req->isOptions());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'OPTIONS';
        $req = $this->newContext();
        $this->assertTrue($req->isOptions());
        
        $this->reset();
        $this->server['REQUEST_METHOD'] = 'NOT-OPTIONS';
        $req = $this->newContext();
        $this->assertFalse($req->isOptions());
    }

    public function testIsXhr()
    {
        $this->reset();
        $this->server['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $req = $this->newContext();
        $this->assertTrue($req->isXhr());
        
        $this->reset();
        $this->server['HTTP_X_REQUESTED_WITH'] = 'XXX';
        $req = $this->newContext();
        $this->assertFalse($req->isXhr());
        
        $this->reset();
        $req = $this->newContext();
        
        // HTTP_X_REQUESTED_WITH not set
        $this->assertFalse($req->isXhr());
    }

    public function testIsCsrf()
    {
        $this->reset();
        $csrf  = $this->newCsrf();
        $this->post['__csrf_token'] = $csrf->generateToken();
        $req   = $this->newContext($this->newCsrf());
        
        $this->assertFalse($req->isCsrf());
        $this->assertTrue($req->isCsrf('invalid_key'));
        
        // if Csrf library is not provided an exception is thrown
        $this->reset();
        $req = $this->newContext();
        
        $this->setExpectedException('aura\web\Exception_Context');
        $req->isCsrf();
    }

    public function testIsSsl()
    {
        $this->reset();
        $req = $this->newContext();
        
        // HTTPS & SERVER_PORT not set
        $this->assertFalse($req->isSsl());
        
        $this->reset();
        $this->server['HTTPS'] = 'on';
        $req = $this->newContext();
        $this->assertTrue($req->isSsl());
        
        $this->reset();
        $this->server['SERVER_PORT'] = '443';
        $req = $this->newContext();
        $this->assertTrue($req->isSsl());
    }

    public function testGetQuery()
    {
        $this->reset();
        $this->get['foo'] = 'bar';
        $req = $this->newContext();
        
        $actual = $req->getQuery('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $req->getQuery('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $req->getQuery('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $req->getQuery();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }
    
    public function testGetRawRequestBody()
    {
        $GLOBALS['aura\web\PhpStream'] = 'Hello World';
        stream_wrapper_unregister('php');
        stream_wrapper_register('php', 'aura\web\PhpStream');
        
        $this->reset();
        $this->server['CONTENT_TYPE']  = 'multipart/form-data';
        $req = $this->newContext();
        
        // if 'multipart/form-data' return null
        $actual = $req->getInput();
        $this->assertNull($actual);
        
        $this->reset();
        $this->server['CONTENT_TYPE'] = 'text/text';
        $req = $this->newContext();
        
        $actual = $req->getInput();
        $this->assertSame('Hello World', $actual);
        
        stream_wrapper_restore('php');
    }

    public function testPost()
    {
        $this->reset();
        $this->post['foo'] = 'bar';
        $req = $this->newContext();
        
        $actual = $req->getInput('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $req->getInput('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $req->getInput('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $req->getInput();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testGetCookie()
    {
        $this->reset();
        $this->cookie['foo'] = 'bar';
        $req = $this->newContext();
        
        $actual = $req->getCookie('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $req->getCookie('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $req->getCookie('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $req->getCookie();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testGetEnv()
    {
        $this->reset();
        $this->env['foo'] = 'bar';
        $req = $this->newContext();
        
        $actual = $req->getEnv('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $req->getEnv('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $req->getEnv('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $req->getEnv();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testGetServer()
    {
        $this->reset();
        $this->server['foo'] = 'bar';
        $req = $this->newContext();
        
        $actual = $req->getServer('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $req->getServer('baz');
        $this->assertNull($actual);
        
        // return alt
        $actual = $req->getServer('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $actual = $req->getServer();
        $this->assertSame(array('foo' => 'bar'), $actual);
    }

    public function testFiles()
    {
        $this->reset();
        // single file
        $this->files['foo'] = array(
            'error'     => null,
            'name'      => 'bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        // bar[]
        $this->files['bar'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        // upload[file1]
        $this->files['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $this->files['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        
        $req = $this->newContext();
        
        $actual = $req->getInput('foo');
        $this->assertSame('bar', $actual['name']);
        
        $actual = $req->getInput('bar');
        $this->assertSame('foo',  $actual[0]['name']);
        $this->assertSame('fooz', $actual[1]['name']);
        
        $actual = $req->getInput('upload');
        $this->assertSame('file1.bar', $actual['file1']['name']);
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        $actual = $req->getInput('baz');
        $this->assertNull($actual);
        
        // return default
        $actual = $req->getInput('baz', 'dib');
        $this->assertSame('dib', $actual);
        
        // return all
        $this->reset();
        $this->files['foo'] = array(
            'error'     => null,
            'name'      => 'bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        
        $req    = $this->newContext();
        $actual = $req->getInput();
        $this->assertSame($this->files, $actual);
    }

    public function testGetInput()
    {
        $this->reset();
        $this->post['foo']  = 'bar';
        $this->files['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $req = $this->newContext();
        
        // match in post, not in files
        $actual = $req->getInput('foo');
        $this->assertSame('bar', $actual);
        
        // match in files, not in post
        $actual = $req->getInput('baz');
        $this->assertSame('dib', $actual['name']);
        
        // no matches returns null
        $actual = $req->getInput('zim');
        $this->assertNull($actual);
        
        // no matches returns alt
        $actual = $req->getInput('zim', 'gir');
        $this->assertSame('gir', $actual);
    }

    public function testgetInputWithPostAndFile()
    {
        $this->reset();
        $this->files['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $this->post['baz']  = 'foo';
        $req                = $this->newContext();
        $actual             = $req->getInput('baz');
        
        $this->assertSame('dib', $actual['name']);
        $this->assertSame('foo', $actual[0]);
    }

    public function testgetInputWithMultiplePostsAndFile()
    {
        $this->reset();
        $this->files['baz'] = array(
            'error'     => null,
            'name'      => 'dib',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $this->post['baz']  = array(
            'foo', 
            'name' => 'files-take-precedence',
            'var'  => 123,
            );
        $req                = $this->newContext();
        $actual             = $req->getInput('baz');
        
        $this->assertSame('dib', $actual['name']);
        $this->assertSame(123,   $actual['var']);
        $this->assertSame('foo', $actual[0]);
    }

    public function testgetInputWithPostAndMultipleFiles()
    {
        $this->reset();
        // baz[]
        $this->post['baz']  = 'bars';
        $this->files['baz'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        // upload[file1]
        $this->post['upload']  = 'bars';
        $this->files['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $this->files['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $req    = $this->newContext();
        $actual = $req->getInput('baz');
        
        $this->assertSame('foo',  $actual[0]['name']);
        $this->assertSame('fooz', $actual[1]['name']);
        
        // post value is inserted into each file
        $this->assertSame('bars', $actual[0][0]);
        $this->assertSame('bars', $actual[1][0]);
        
        $actual = $req->getInput('upload');
        
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
        $this->post['baz']  = array(
            'mars', 
            array(
                0      => 'bars',
                'name' => 'files-take-precedence',
        ));
        $this->files['baz'] = array(
            'error'     => array(null, null),
            'name'      => array('foo', 'fooz'),
            'size'      => array(null, null),
            'tmp_name'  => array(null, null),
            'type'      => array(null, null),
        );
        
        // upload[file1]
        $this->post['upload']  = array(
            'file1' => 'mars', 
            'file2' => array(
                0      => 'bars',
                'name' => 'files-take-precedence'
        ));
        $this->files['upload']['file1'] = array(
            'error'     => null,
            'name'      => 'file1.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        $this->files['upload']['file2'] = array(
            'error'     => null,
            'name'      => 'file2.bar',
            'size'      => null,
            'tmp_name'  => null,
            'type'      => null,
        );
        
        $req    = $this->newContext();
        $actual = $req->getInput('baz');
        
        $this->assertSame('fooz', $actual[1]['name']);
        
        // post values is inserted
        $this->assertSame('mars', $actual[0][0]);
        $this->assertSame('bars', $actual[1][0]);
        
        $actual = $req->getInput('upload');
        
        $this->assertSame('file2.bar', $actual['file2']['name']);
        
        // post value is inserted
        $this->assertSame('mars', $actual['file1'][0]);
        $this->assertSame('bars', $actual['file2'][0]);
    }
    
    public function testGetHeader()
    {
        $this->reset();
        $this->server['HTTP_FOO'] = 'bar';
        $req = $this->newContext();
        
        $actual = $req->getHeader('foo');
        $this->assertSame('bar', $actual);
        
        $actual = $req->getHeader('baz');
        $this->assertNull($actual);
        
        $actual = $req->getHeader('baz', 'dib');
        $this->assertSame('dib', $actual);
    }
    
    public function testGetAccept()
    {
        $this->reset();
        $this->server['HTTP_ACCEPT'] = 'text/*;q=0.9, text/html ,text/xhtml;q=0.8';
        $this->server['HTTP_ACCEPT_LANGUAGE'] = 'en-US';
        
        $req    = $this->newContext();
        $expect = array(
            0 => array(0 => 'text/html',  1 => 1.0),
            1 => array(0 => 'text/*',     1 => 0.9),
            2 => array(0 => 'text/xhtml', 1 => 0.8),
        );
        $actual = $req->getAccept('type');
        $this->assertEquals($expect, $actual);
        
        $actual = $req->getAccept('language');
        $expect = array(
            0 => array(0 => 'en-US',  1 => 1.0),
        );
        $this->assertEquals($expect, $actual);
        
        $actual = $req->getAccept('charset', 'alt');
        $this->assertSame('alt', $actual);
        
        $expect = array(
            'type' => array(
                0 => array(0 => 'text/html',  1 => 1.0),
                1 => array(0 => 'text/*',     1 => 0.9),
                2 => array(0 => 'text/xhtml', 1 => 0.8),
            ),
            'language' => array(
                0 => array(0 => 'en-US',  1 => 1.0),
            ),
        );
        $actual = $req->getAccept();
        $this->assertEquals($expect, $actual);
    }
    
    public function testXJsonIsRemoved()
    {
        $this->reset();
        $this->server['HTTP_X_JSON'] = 'remove-me';
        $req = $this->newContext();
        
        $actual = $req->getHeader('x-json');
        $this->assertNull($actual);
        
        $actual = $req->getServer('HTTP_X_JSON');
        $this->assertNull($actual);
    }
}
