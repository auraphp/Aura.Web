<?php
namespace Aura\Web\Request\Accept;

class Language extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT_LANGUAGE';

    protected $value_class = 'Aura\Web\Request\Accept\Value\Language';
}
