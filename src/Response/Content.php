<?php
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

    protected $charset;
    
    protected $disposition;
    
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

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    
    public function getCharset()
    {
        return $this->charset;
    }
    
    public function setDisposition($disposition, $filename = null)
    {
        $this->disposition = $disposition;
        $this->filename = basename($filename);
    }
    
    public function getDisposition()
    {
        return $this->disposition;
    }
    
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
