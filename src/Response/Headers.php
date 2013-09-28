<?php
namespace Aura\Web\Response;

class Headers
{
    /**
     * 
     * The response headers.
     * 
     * @var array
     * 
     */
    protected $headers = array();

    /**
     * 
     * Sets a header value in `$headers`.
     * 
     * @param string $key The header label.
     * 
     * @param string $val The value for the header.
     * 
     * @return void
     * 
     */
    public function set($key, $val)
    {
        $key = $this->sanitizeLabel($key);
        $val = $this->sanitizeValue($val);
        $this->headers[$key] = $val;
    }

    /**
     * 
     * Adds to a header value in $this->headers.
     * 
     * @param string $key The header label.
     * 
     * @param string $val The value for the header.
     * 
     * @return void
     * 
     */
    public function add($key, $val)
    {
        $key = $this->sanitizeLabel($key);
        $val = $this->sanitizeValue($val);
        $this->headers[$key][] = $val;
    }

    /**
     * 
     * Returns the value of a single header.
     * 
     * @param string $key The header name.
     * 
     * @return string|array A string if the header has only one value, or an
     * array if the header has multiple values, or null if the header does not
     * exist.
     * 
     */
    public function get($key = null)
    {
        if (! $key) {
            return $this->headers;
        }
        
        $key = $this->sanitizeLabel($key);
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }
    }

    /**
     * 
     * Normalizes and sanitizes a header label.
     * 
     * @param string $label The header label to be sanitized.
     * 
     * @return string The sanitized header label.
     * 
     */
    protected function sanitizeLabel($label)
    {
        $label = preg_replace('/[^a-zA-Z0-9-]/', '', $label);
        $label = ucwords(strtolower(str_replace('-', ' ', $label)));
        $label = str_replace(' ', '-', $label);
        return $label;
    }

    /**
     * 
     * Sanitizes a header value.
     * 
     * @param string $value The header value to be sanitized.
     * 
     * @return string The sanitized header value.
     * 
     */
    protected function sanitizeValue($value)
    {
        return str_replace(array("\r", "\n"), '', $value);
    }
}
