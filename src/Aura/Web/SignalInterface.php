<?php
namespace Aura\Web;

interface SignalInterface
{
    public function handler($origin, $signal, $callback);
    public function send($origin, $signal);
}
