<?php
namespace Aura\Web;

use Aura\Framework\Test\WiringAssertionsTrait;

class WiringTest extends \PHPUnit_Framework_TestCase
{
    use WiringAssertionsTrait;

    protected function setUp()
    {
        $this->loadDi();
    }

    public function testServices()
    {
        $this->assertGet('web_accept',   'Aura\Web\Accept');
        $this->assertGet('web_context',  'Aura\Web\Context');
        $this->assertGet('web_response', 'Aura\Web\Response');
    }

    public function testInstances()
    {
        $this->assertNewInstance('Aura\Web\Accept');
        $this->assertNewInstance('Aura\Web\Context');
        $this->assertNewInstance('Aura\Web\Controller\AbstractPage', 'Aura\Web\MockPage');
    }
}
