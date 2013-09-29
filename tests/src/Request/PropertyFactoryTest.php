<?php
namespace Aura\Web\Request;

class PropertyFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->property_factory = new PropertyFactory(array(
            '_SERVER' => array(
                'HTTP_CONTENT_TYPE' => 'text/html',
                'HTTP_X_JSON' => 'delete-me',
            ),
            '_GET' => array(
                'name' => 'aura',
            ),
            '_POST' => array(
                'name' => 'postvalue'
            ),
            '_FILES' => $_FILES,
            '_ENV' => $_ENV,
            '_COOKIE' => $_COOKIE            
        ));
    }
    
    public function testNewQuery()
    {
        $query = $this->property_factory->newQuery();
        $this->assertSame('aura', $query->get('name'));
        $this->assertSame('defaultvalue', $query->get('default', 'defaultvalue'));
    }
    
    public function testNewPost()
    {
        $post = $this->property_factory->newPost();
        $this->assertSame('postvalue', $post->get('name'));
        $this->assertSame('defaultvalue', $post->get('default', 'defaultvalue'));
    }
}
