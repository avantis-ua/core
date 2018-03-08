<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core;

use Pllano\Interfaces\DataInterface;

class Data implements DataInterface
{
    /**
     * Data
     *
     * @var array
     * @access protected
    */
    protected $_data = [];

    /**
     * Create a new Data instance
     *
     * @param array $array Array to store
     */
    public function __construct($data = [])
    {
        $this->setArrayData($data);
    }

    public function hasId()
    {
        return (isset($this->_data[ $this->_idField ]) and $this->_data[ $this->_idField ] > 0);
    }
    
    public function getId() // Ok
    {
        if ($this->hasId() && isset($this->_idField)) {
            return $this->_data[$this->_idField];
        }
    }
    
    public function setId($newId = null) // Ok
    {
        if (isset($newId) && isset($this->_idField)) {
            $this->_data[$this->_idField] = $newId;
        }
    }

    /**
     * Create new collection
     *
     * @param array $items Pre-populate collection with this key-value array
     */
    public function createData(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->setDataOne($key, $value);
        }
    }

    /**
     * Set collection item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
    */
    public function setDataOne($key, $value)
    {
        $this->_data[$key] = $value;
    }
    
    /**
     * Set a value to a given path or an array of paths and values
     *
     * @param mixed $key   Path or an array of paths and values
     * @param mixed $value Value to set if the path is not an array
     */
    public function setData($key, $value = null)
    {
        if (is_string($key)) {
            if (is_array($value) && !empty($value)) {
                // Iterate the values
                foreach ($value as $k => $v) {
                    $this->setData("$key.$k", $v);
                }
            } else {
                // Iterate a path
                $keys = explode('.', $key);
                $array = &$this->_data;

                foreach ($keys as $key) {
                    if (!isset($array[$key]) || !is_array($array[$key])) {
                        $array[$key] = [];
                    }

                    $array = &$array[$key];
                }

                // Set a value
                $array = $value;
            }
        } elseif (is_array($key)) {
            // Iterate an array of paths and values
            foreach ($key as $k => $v) {
                $this->setDataOne($k, $v);
            }
        }
    }

    /**
     * Get collection item for key
     *
     * @param string $key     The data key
     * @param mixed  $default The default value to return if data key does not exist
     *
     * @return mixed The key's value, or the default value
    */
    public function getDataOne($key, $default = null)
    {
        return $this->hasData($key) ? $this->_data[$key] : $default;
    }

    /**
     * Get a value from a path or default value if the path doesn't exist
     *
     * @param  string $key     Path
     * @param  mixed  $default Default value
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        $keys = explode('.', (string)$key);
        $array = &$this->_data;

        foreach ($keys as $key) {
            if (!$this->existsData($array, $key)) {
                return $default;
            }

            $array = &$array[$key];
        }

        return $array;
    }
    
    /**
     * Add a value or an array of values to path
     *
     * @param mixed $key   Path or an array of paths and values
     * @param mixed $value Value to set if the path is not an array
     * @param bool  $pop   Helper to pop out the last key if the value is an array
     */
    public function addData($key, $value = null, $pop = false)
    {
        if (is_string($key)) {
            if (is_array($value)) {
                // Iterate the values
                foreach ($value as $k => $v) {
                    $this->addData("$key.$k", $v, true);
                }
            } else {
                // Iterate a path
                $keys = explode('.', $key);
                $array = &$this->_data;

                if ($pop === true) {
                    array_pop($keys);
                }

                foreach ($keys as $key) {
                    if (!isset($array[$key]) || !is_array($array[$key])) {
                        $array[$key] = [];
                    }

                    $array = &$array[$key];
                }

                // Add a value
                $array[] = $value;
            }
        } elseif (is_array($key)) {
            // Iterate an array of paths and values
            foreach ($key as $k => $v) {
                $this->addData($k, $v);
            }
        }
    }
    
    /**
     * Get a value from a path or all the stored values and remove them
     *
     * @param  string|null $key     Path
     * @param  mixed       $default Default value
     * @return mixed
     */
    public function pullData($key = null, $default = null)
    {
        if (is_string($key)) {
            // Get a value from a path
            $value = $this->getData($key, $default);
            $this->deleteData($key);

            return $value;
        }

        if (is_null($key)) {
            // Get all the stored values
            $value = $this->allData();
            $this->clearData();

            return $value;
        }
    }

    /**
     * Add item to collection
     *
     * @param array $items Key-value array of data to append to this collection
    */
    public function replaceData(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->setDataOne($key, $value);
        }
    }

    /**
     * Get all items in collection
     *
     * @return array The collection's source data
    */
    public function allData()
    {
        return $this->_data;
    }

    /**
     * Get collection keys
     *
     * @return array The collection's source data keys
     */
    public function keysData()
    {
        return array_keys($this->_data);
    }

    /**
     * Does this collection have a given key?
     *
     * @param string $key The data key
     *
     * @return bool
     */
    public function hasDataOne($key)
    {
        return array_key_exists($key, $this->_data);
    }
    
    /**
     * Check if a path exists
     *
     * @param  string $key Path
     * @return bool
     */
    public function hasData($key)
    {
        $keys = explode('.', (string)$key);
        $array = &$this->_data;

        foreach ($keys as $key) {
            if (!$this->existsData($array, $key)) {
                return false;
            }

            $array = &$array[$key];
        }

        return true;
    }

    /**
     * Determine if the given key exists in the provided array
     *
     * @param  ArrayAccess|array $array
     * @param  string|int        $key
     * @return bool
     */
    public function existsData($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return isset($array[$key]);
        }

        return array_key_exists($key, $array);
    }
    
    /**
     * Delete a path or an array of paths
     *
     * @param mixed $key Path or an array of paths to delete
     */
    public function deleteData($key)
    {
        if (is_string($key)) {
            // Iterate a path
            $keys = explode('.', $key);
            $array = &$this->_data;
            $last = array_pop($keys);

            foreach ($keys as $key) {
                if (!$this->existsData($array, $key)) {
                    return;
                }

                $array = &$array[$key];
            }

            unset($array[$last]);
        } elseif (is_array($key)) {
            // Iterate an array of paths
            foreach ($key as $k) {
                $this->deleteData($k);
            }
        }
    }

    /**
     * Remove item from collection
     *
     * @param string $key The data key
     */
    public function deleteDataOne($key)
    {
        $this->removeData($key);
    }

    public function removeData($key)
    {
        unset($this->_data[$key]);
    }

    /**
     * Remove all items from collection
     */
    public function clearDataAll()
    {
        $this->_data = [];
    }

    /**
     * Delete all values from a given path,
     * from an array of paths or clear all the stored values
     *
     * @param mixed $key Path or an array of paths to clean
     */
    public function clearData($key = null)
    {
        if (is_string($key)) {
            // Clear the path
            $this->setData($key, []);
        } elseif (is_array($key)) {
            // Iterate an array of paths
            foreach ($key as $k) {
                $this->clearData($k);
            }
        } elseif (is_null($key)) {
            // Clear all the stored arrays
            $this->_data = [];
        }
    }

    /**
     * Sort the values of a path or all the stored values
     *
     * @param  string|null $key Path to sort
     * @return array
     */
    public function sortData($key = null)
    {
        if (is_string($key)) {
            // Sort values of a path
            $values = $this->getData($key);

            return $this->sortArrayData((array)$values);
        } elseif (is_null($key)) {
            // Sort all the stored values
            return $this->sortArrayData($this->_data);
        }
    }

    /**
     * Recursively sort the values of a path or all the stored values
     *
     * @param  string|null $key   Path to sort
     * @param  array       $array Array to sort
     * @return array
     */
    public function sortRecursiveData($key = null, $array = null)
    {
        if (is_array($array)) {
            // Loop through an array
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = $this->sortRecursiveData(null, $value);
                }
            }
            return $this->sortArrayData($array);
        } elseif (is_string($key)) {
            // Sort values of a path
            $values = $this->getData($key);

            return $this->sortRecursiveData(null, (array)$values);
        } elseif (is_null($key)) {
            // Sort all the stored values
            return $this->sortRecursiveData(null, $this->_data);
        }
    }

    /**
     * Sort the given array
     *
     * @param  array $array Array to sort
     * @return array
     */
    public function sortArrayData($array)
    {
        $this->isAssocData($array) ? ksort($array) : sort($array);

        return $array;
    }

    /**
     * Determine whether the given value is array accessible
     *
     * @param  mixed $value Array to verify
     * @return bool
     */
    public function accessibleData($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if an array is associative
     *
     * @param  array|null $array Array to verify
     * @return bool
     */
    public function isAssocData($array = null)
    {
        $keys = is_array($array) ? array_keys($array) : array_keys($this->_data);

        return array_keys($keys) !== $keys;
    }

    /**
     * Store an array
     *
     * @param array $array
     */
    public function setArrayData($array)
    {
        if ($this->accessibleData($array)) {
            $this->_data = $array;
        }
    }

    /**
     * Store an array as a reference
     *
     * @param array $array
     */
    public function setReferenceData(&$array)
    {
        if ($this->accessibleData($array)) {
            $this->_data = &$array;
        }
    }
    
    public function __invoke()
    {
        return $this->_data;
    }

     /*************************************
     * Magic Methods
     *************************************/

    /**
     * Get a data by key
     *
     * @param string The key data to retrieve
     * @access public
     */
    public function &__get($key)
    {
        return $this->_data[$key];
        //return $this->getData($key);
    }

    /**
     * Assigns a value to the specified data
     * 
     * @param string The data key to assign the value to
     * @param mixed  The value to set
     * @access public 
     */
    public function __set($key, $value = null)
    {
        $this->_data[$key] = $value;
        // $this->setData($key, $value);
    }

    /**
     * Whether or not an data exists by key
     *
     * @param string An data key to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function __isset($key)
    {
        return isset($this->_data[$key]);
        // return $this->hasData($key);
    }

    /**
     * Unsets an data by key
     *
     * @param string The key to unset
     * @access public
     */
    public function __unset($key)
    {
        //$this->deleteData($key);
        unset($this->_data[$key]);
    }

     /*************************************
     * ArrayAccess interface
     * ArrayAccess Abstract Methods
     *************************************/

    /**
     * Assigns a value to the specified offset
     *
     * @param string The offset to assign the value to
     * @param mixed  The value to set
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        //$this->setData($offset, $value);
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    /**
     * Whether or not an offset exists
     *
     * @param string An offset to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function offsetExists($offset)
    {
        // return $this->hasData($offset);
        return isset($this->_data[$offset]);
    }

    /**
     * Unsets an offset
     *
     * @param string The offset to unset
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetUnset($offset)
    {
        // $this->deleteData($offset);
        if ($this->offsetExists($offset)) {
            unset($this->_data[$offset]);
        }
    }

    /**
     * Returns the value at specified offset
     *
     * @param string The offset to retrieve
     * @access public
     * @return mixed
     * @abstracting ArrayAccess
     */
    public function offsetGet($offset)
    {
        // return $this->getData($offset);
        return $this->offsetExists($offset) ? $this->_data[$offset] : null;
    }
    
    /**
     * Get number of items in collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

    /*************************************
     * IteratorAggregate interface
     *************************************/

    /**
     * Get collection iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_data);
    }
    
     /*********************************************
     * Устаревшые функции. Будут удалены в v1.3.0
     *********************************************/

    /**
     * Удалить все данные в модели и записать в нее данные из массива
     *
     * @param array $array
    */
    public function fromArray(array $array = []) // Ok
    { 
        $this->_data = $array; 
    }
    
    /**
     * Заменить только те данные в модели, которые есть в массиве. Остальные оставить.
     *
     * @param array $array
    */
    public function mixArray($array) // Ok
    {
        foreach($array as $key => $value) 
        {
            $this->_data[$key] = clean($value);
        }
    }

    // Вставляем только те данные из массива, ключи которых уже имеются в массиве данных модели (т.н. выборочная вставка)
    public function ownArray($array) // Ok
    {
        foreach($array as $key => $value)
        {
            if(isset($this->_data[$key])) {
                $this->_data[$key] = $value;
            }
        }
    }

    public function toArray() // Ok
    {
        return $this->_data; 
    }

    // Функция для дебаггинга
    public function lastQuery() // Ok
    {
        return $this->_lastQuery;
    }

}
 