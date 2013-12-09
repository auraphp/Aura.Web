<?php
namespace Aura\Web\Request\Accept\Value;

class Encoding extends AbstractValue
{
    public function match(Encoding $avail)
    {
        return strtolower($this->value) == strtolower($avail->getValue())
            && $this->matchParameters($avail);
    }
}
