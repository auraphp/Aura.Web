<?php
namespace Aura\Web\Request\Accept;

abstract class AbstractValues implements \IteratorAggregate, \Countable, \ArrayAccess {
    protected $values = array();

    protected $server_key;
    
    /**
     * @param array $server A copy of $_SERVER.
     */
    public function __construct(array $server = array())
    {
        $this->addValues($server);
    }

    public function setValues($values)
    {
        $this->values = array();
        $this->addValues($values);
    }
    
    /**
     * @param string|array $values $_SERVER of an Accept* value
     */
    public function addValues($values)
    {
        $key = $this->server_key;
        
        if (is_array($values)) {
            if (! isset($values[$key])) {
                $this->values = array();
                return;
            }
            $values = $values[$key];
        }

        $values = $this->parseValues($values, $key);
        $values = $this->qualitySort(array_merge($this->values, $values));

        $values = $this->removeDuplicates($values);

        $this->values = $values;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getValuesAsString()
    {
        foreach ($this->values as $value) {
            $values[] = $value->getValue();
        }

        return implode(',', $values);
    }

    protected function parseValues($values)
    {
        $key = $this->server_key;
        
        $classes = array(
            'HTTP_ACCEPT'          => 'Media',
            'HTTP_ACCEPT_CHARSET'  => 'Charset',
            'HTTP_ACCEPT_LANGUAGE' => 'Language',
            'HTTP_ACCEPT_ENCODING' => 'Encoding',
        );
        
        $class = 'Aura\Web\Request\Accept\Value\\' . $classes[$key];

        $values = explode(',', $values);

        foreach ($values as &$value) {
            $pairs = explode(';', $value);
            $value = $pairs[0];
            unset($pairs[0]);

            $params = array();
            foreach ($pairs as $pair) {
                $param = array();
                preg_match('/^(?P<name>.+?)=(?P<quoted>"|\')?(?P<value>.*?)(?:\k<quoted>)?$/', $pair, $param);

                $params[$param['name']] = $param['value'];
            }

            $priority = 1.0;
            if (isset($params['q'])) {
                $priority = $params['q'];
                unset($params['q']);
            }

            /** @todo needs a factory here */
            $obj = new $class();
            $obj->setValue(trim($value));
            $obj->setPriority((float) $priority);
            $obj->setParameters($params);
            $value = $obj;
        }

        return $values;
    }

    /**
     * 
     * Sorts an Accept header value set according to quality levels.
     * 
     * This is an unusual sort. Normally we'd think a reverse-sort would
     * order the array by q values from 1 to 0, but the problem is that
     * an implicit 1.0 on more than one value means that those values will
     * be reverse from what the header specifies, which seems unexpected
     * when negotiating later.
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @param string $key The key to look up in $_SERVER.
     * 
     * @return array An array of values sorted by quality level.
     * 
     */
    protected function qualitySort($values)
    {
        $var    = array();
        $bucket = array();

        // sort into q-value buckets
        foreach ($values as $value) {
            $bucket[$value->getPriority()][] = $value;
        }

        // reverse-sort the buckets so that q=1 is first and q=0 is last,
        // but the values in the buckets stay in the original order.
        krsort($bucket);

        // flatten the buckets into the var
        foreach ($bucket as $q => $values) {
            foreach ($values as $value) {
                $var[] = $value;
            }
        }

        return $var;
    }

    protected function removeDuplicates($values)
    {
        $unique = array();
        foreach ($values as $value) {
            $unique[$value->getValue()] = $value;
        }

        return array_values($unique);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    public function __toString()
    {
        return $this->getValuesAsString();
    }

    public function count()
    {
        return count($this->values);
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }
}