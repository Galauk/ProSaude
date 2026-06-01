<?php

class Elotech_Controller_Action_Helper_Array extends Zend_Controller_Action_Helper_Abstract{
    public function array_put(&$array, $object, $position, $name = null)
        {
                $count = 0;
                $return = array();
                foreach ($array as $k => $v)
                {  
                        // insert new object
                        if ($count == $position)
                        {  
                                if (!$name) $name = $count;
                                $return[$name] = $object;
                                $inserted = true;
                        }  
                        // insert old object
                        $return[$k] = $v;
                        $count++;
                }  
                if (!$name) $name = $count;
                if (!$inserted) $return[$name];
                $array = $return;
                return $array;
        }
    
}