<?php
namespace Aura\Web\Request\Accept;

use Aura\Web\Request\Accept\Value\ValueFactory;
use Countable;
use IteratorAggregate;

abstract class AbstractValues implements IteratorAggregate, Countable
{
    protected $acceptable = array();

    protected $server_key;
    
    protected $value_type;
    
    /**
     * @param array $server A copy of $_SERVER.
     */
    public function __construct(
        ValueFactory $value_factory,
        array $server = array()
    ) {
        $this->value_factory = $value_factory;
        $this->add($server);
    }
    
    public function isEmpty()
    {
        return empty($this->acceptable);
    }

    public function get($key = null)
    {
        if ($key === null) {
            return $this->acceptable;
        }
        return $this->acceptable[$key];
    }
    
    protected function set($values)
    {
        $this->acceptable = array();
        $this->add($values);
    }
    
    /**
     * @param string|array $values $_SERVER of an Accept* value
     */
    protected function add($values)
    {
        $key = $this->server_key;
        
        if (is_array($values)) {
            if (! isset($values[$key])) {
                $this->acceptable = array();
                return;
            }
            $values = $values[$key];
        }

        $values = $this->parseAcceptable($values, $key);
        $values = $this->qualitySort(array_merge($this->acceptable, $values));

        $values = $this->removeDuplicates($values);

        $this->acceptable = $values;
    }

    protected function parseAcceptable($values)
    {
        $values = explode(',', $values);

        foreach ($values as $key => $value) {
            $pairs = explode(';', $value);
            $value = $pairs[0];
            unset($pairs[0]);

            $params = array();
            foreach ($pairs as $pair) {
                $param = array();
                preg_match('/^(?P<name>.+?)=(?P<quoted>"|\')?(?P<value>.*?)(?:\k<quoted>)?$/', $pair, $param);

                $params[$param['name']] = $param['value'];
            }

            $quality = 1.0;
            if (isset($params['q'])) {
                $quality = $params['q'];
                unset($params['q']);
            }

            $values[$key] = $this->value_factory->newInstance(
                $this->value_type,
                trim($value),
                (float) $quality,
                $params
            );
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
            $bucket[$value->getQuality()][] = $value;
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
        return new \ArrayIterator($this->acceptable);
    }

    public function count()
    {
        return count($this->acceptable);
    }
    
    /**
     * @return A matching string from the original $available array, *not*
     * a Accept\Value object.
     */
    abstract public function negotiate(array $available);
}
