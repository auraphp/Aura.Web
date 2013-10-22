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

/**
 * @todo Add the following:
 * 
 * Accept-Ranges -- what partial content range types this server supports
 * Accept-Ranges: bytes
 */

class Content
{
    /**
     * 
     * The response body content.
     * 
     * @var string
     * 
     */
    protected $content = null;

    protected $headers;
    
    /**
     * 
     * The response character set
     * 
     * @var string
     * 
     */
    protected $charset;
    
    protected $type;
    
    public function __construct(Headers $headers)
    {
        $this->headers = $headers;
    }
    
    /**
     * 
     * Sets the content of the response.
     * 
     * @param mixed $content The body content of the response.
     * 
     * @return null
     * 
     */
    public function set($content)
    {
        $this->content = $content;
    }

    /**
     * 
     * Gets the content of the response.
     * 
     * @return mixed The body content of the response.
     * 
     */
    public function get()
    {
        return $this->content;
    }

    /**
     * 
     * Set the character set
     * 
     * @param string $charset
     * 
     * @return null
     * 
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        $this->setContentType();
    }
    
    /**
     * 
     * Sets the Content-Type of the response.
     * 
     * @param string The Content-Type of the response.
     * 
     * @return null
     * 
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->setContentType();
    }
    
    protected function setContentType()
    {
        if (! $this->type) {
            $this->headers->set('Content-Type', null);
            return;
        }
        
        $value = $this->type;
        if ($this->charset) {
            $value .= "; charset={$this->charset}";
        }
        $this->headers->set('Content-Type', $value);
    }
    
    public function setEncoding($encoding)
    {
        $this->headers->set('Content-Encoding', $encoding);
    }
    
    /**
     * 
     * Set the content disposition header
     * 
     * @param string $disposition
     * 
     * @param string $filename
     * 
     * @return null
     * 
     */
    public function setDisposition($disposition, $filename = null)
    {
        if ($disposition && $filename) {
            $filename = basename($filename);
            $disposition .='; filename="'. rawurlencode($filename) . '"';
        }
        $this->headers->set('Content-Disposition', $disposition);
    }
}
