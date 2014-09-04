<?php
namespace Aura\Web\_Config;

use Aura\Di\ContainerAssertionsTrait;

class CommonTest extends \PHPUnit_Framework_TestCase
{
    use ContainerAssertionsTrait;

    public function setUp()
    {
        $this->setUpContainer(array(
            'Aura\Web\_Config\Common',
        ));
    }

    public function test()
    {
        $this->assertGet('web_response_headers', 'Aura\Web\Response\Headers');
        $this->assertGet('web_response_status', 'Aura\Web\Response\Status');
        $this->assertGet('web_response_cache', 'Aura\Web\Response\Cache');

        $this->assertNewInstance('Aura\Web\Request');
        $this->assertNewInstance('Aura\Web\Request\Accept');
        $this->assertNewInstance('Aura\Web\Request\Client');
        $this->assertNewInstance('Aura\Web\Request\Content');
        $this->assertNewInstance('Aura\Web\Request\Globals');
        $this->assertNewInstance('Aura\Web\Request\Headers');
        $this->assertNewInstance('Aura\Web\Request\Method');
        $this->assertNewInstance('Aura\Web\Request\Url');
        $this->assertNewInstance('Aura\Web\Response');
        $this->assertNewInstance('Aura\Web\Response\Headers');
        $this->assertNewInstance('Aura\Web\Response\Content');
        $this->assertNewInstance('Aura\Web\Response\Cache');
        $this->assertNewInstance('Aura\Web\Response\Redirect');
    }
}
