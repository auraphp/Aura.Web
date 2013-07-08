<?php
namespace Aura\Web\Request;

class Files extends Values
{
    public function __construct(array $files = [])
    {
        $this->init($files, $this->data);
    }
    
    /**
     * 
     * Recursively initialize the data structure to be more like $_POST.
     * 
     * @param array $src The source array.
     * 
     * @param array &$tgt Where we will store the restructured data when we
     * find it.
     * 
     * @return void
     * 
     */
    protected function init($src, &$tgt)
    {
        // an array with these keys is a "target" for us (pre-sorted)
        $tgtkeys = ['error', 'name', 'size', 'tmp_name', 'type'];

        // the keys of the source array (sorted so that comparisons work
        // regardless of original order)
        $srckeys = array_keys((array) $src);
        sort($srckeys);

        // is the source array a target?
        if ($srckeys == $tgtkeys) {
            // get error, name, size, etc
            foreach ($srckeys as $key) {
                if (is_array($src[$key])) {
                    // multiple file field names for each error, name, size, etc.
                    foreach ((array) $src[$key] as $field => $value) {
                        $tgt[$field][$key] = $value;
                    }
                } else {
                    // the key itself is error, name, size, etc., and the
                    // target is already the file field name
                    $tgt[$key] = $src[$key];
                }
            }
        } else {
            // not a target, create sub-elements and init them too
            foreach ($src as $key => $val) {
                $tgt[$key] = [];
                $this->init($val, $tgt[$key]);
            }
        }
    }
}
