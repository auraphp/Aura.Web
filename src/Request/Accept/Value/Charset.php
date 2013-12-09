<?php
namespace Aura\Web\Request\Accept\Value;

class Charset extends AbstractValue
{
    public function match(Charset $avail)
    {
        return strtolower($this->value) == strtolower($avail->getValue())
            && $this->matchParameters($avail);
    }
}
