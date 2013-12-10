<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web\Request\Accept;

/**
 * 
 * Represents a collection of `Acccept-Language` header values, sorted in
 * quality order.
 * 
 * @package Aura.Web
 * 
 */
class Language extends AbstractValues
{
    /**
     * 
     * The $_SERVER key to use when populating acceptable values.
     * 
     * @var string
     * 
     */
    protected $server_key = 'HTTP_ACCEPT_LANGUAGE';

    /**
     * 
     * The type of value object to create using the ValueFactory.
     * 
     * @var string
     * 
     */
    protected $value_type = 'language';
}
