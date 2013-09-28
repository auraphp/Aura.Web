<?php
namespace Aura\Web\Response;

use Aura\Web\Exception;

class Status
{
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

    public function get()
    {
        return array(
            'version' => $this->version,
            'code'    => $this->code,
            'phrase'  => $this->phrase,
        );
    }
    
    public function set($code, $phrase, $version = null)
    {
        $this->setCode($code);
        $this->setPhrase($phrase);
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
        $this->setPhrase(null);
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
     * @return void
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
     * @return void
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
