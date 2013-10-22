<?php
namespace Aura\Web;

trait AssertHeadersTrait
{
    protected function assertHeaders(array $expect)
    {
        $actual = $this->headers->get();
        $this->assertSame($expect, $actual);
    }
}
