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
namespace Aura\Web\Response;

use Aura\Web\Exception;

class Status
{
    /**
     * 
     * Standard status codes and phrases, per
     * <http://www.iana.org/assignments/http-status-codes/http-status-codes.txt>.
     * 
     * @var array
     * 
     */
    protected $code_phrase = array(
        
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unassigned',
        426 => 'Upgrade Required',
        427 => 'Unassigned',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    );
    
    /**
     * 
     * The response status code.
     * 
     * @var int
     * 
     */
    protected $code = 200;

    /**
     * 
     * The response status phrase.
     * 
     * @var string
     * 
     */
    protected $phrase = 'OK';

    /**
     * 
     * The HTTP version to send as.
     * 
     * @var string
     * 
     */
    protected $version = 1.1;

    /**
     * 
     * Returns an array of status information
     * 
     * @return array
     * 
     */
    public function get()
    {
        return array(
            'version' => $this->version,
            'code'    => $this->code,
            'phrase'  => $this->phrase,
        );
    }
    
    /**
     * 
     * @param int $code
     * 
     * @param string $phrase
     * 
     * @param string $version
     * 
     */
    public function set($code, $phrase = null, $version = null)
    {
        $this->setCode($code);
        if ($phrase) {
            $this->setPhrase($phrase);
        }
        if ($version) {
            $this->setVersion($version);
        }
    }
    
    /**
     * 
     * Sets the HTTP status code for the response.
     * 
     * Automatically resets the status phrase to null.
     * 
     * @param int $code An HTTP status code, such as 200, 302, 404, etc.
     * 
     */
    public function setCode($code)
    {
        $code = (int) $code;
        if ($code < 100 || $code > 599) {
            throw new Exception\InvalidStatusCode($code);
        }
        $this->code = $code;
        if (isset($this->code_phrase[$code])) {
            $this->setPhrase($this->code_phrase[$code])''
        } else {
            $this->setPhrase(null);
        }
    }

    /**
     * 
     * Returns the HTTP status code for the response.
     * 
     * @return int
     * 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 
     * Sets the HTTP status phrase for the response.
     * 
     * @param string $phrase The status phrase.
     * 
     * @return null
     * 
     */
    public function setPhrase($phrase)
    {
        $phrase = trim(str_replace(array("\r", "\n"), '', $phrase));
        $this->phrase = $phrase;
    }

    /**
     * 
     * Returns the HTTP status phrase for the response.
     * 
     * @return string
     * 
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * 
     * Sets the HTTP version for the response to 1.0 or 1.1.
     * 
     * @param string $version The HTTP version to use for this response.
     * 
     * @return null
     * 
     */
    public function setVersion($version)
    {
        $version = (float) $version;
        if ($version != 1.0 && $version != 1.1) {
            throw new Exception\InvalidVersion($version);
        }
        $this->version = $version;
    }

    /**
     * 
     * Returns the HTTP version for the response.
     * 
     * @return string
     * 
     */
    public function getVersion()
    {
        return $this->version;
    }
}
