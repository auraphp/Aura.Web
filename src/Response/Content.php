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

use Aura\Web\Response;

/*
Accept-Ranges 	What partial content range types this server supports 	Accept-Ranges: bytes 	Permanent
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

    /**
     * 
     * The response characterset
     * 
     * @var string
     * 
     */
    protected $charset;
    
    /**
     * 
     * Content disposition header
     * 
     * @var string
     * 
     */
    protected $disposition;
    
    /**
     * 
     * Filename
     * 
     * @var string
     * 
     */
    protected $filename;
    
    /**
     * 
     * The Content-Type of the response.
     * 
     * @var string
     * 
     */
    protected $type = null;

    /**
     * 
     * Sets the content of the response.
     * 
     * @param string $content The body content of the response.
     * 
     * @return void
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
     * @return string The body content of the response.
     * 
     */
    public function get()
    {
        return $this->content;
    }

    /**
     * 
     * Set the characterset
     * 
     * @param string $charset
     * 
     * @return void
     * 
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    
    /**
     * 
     * Get the character set
     * 
     * @return string
     * 
     */
    public function getCharset()
    {
        return $this->charset;
    }
    
    /**
     * 
     * Set the content disposition header
     * 
     * @param string $disposition
     * 
     * @param string $filename
     * 
     * @return void
     * 
     */
    public function setDisposition($disposition, $filename = null)
    {
        $this->disposition = $disposition;
        $this->filename = basename($filename);
    }
    
    /**
     * 
     * Get the content disposition header
     * 
     * @return string
     * 
     */
    public function getDisposition()
    {
        return $this->disposition;
    }
    
    /**
     * 
     * Get the filename
     * 
     * @return string
     * 
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * 
     * Sets the Content-Type of the response.
     * 
     * @param string The Content-Type of the response.
     * 
     * @return void
     * 
     * @see negotiateContentType()
     * 
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * 
     * Gets the Content-Type of the response.
     * 
     * @return string The Content-Type of the response.
     * 
     */
    public function getType()
    {
        return $this->type;
    }
}
