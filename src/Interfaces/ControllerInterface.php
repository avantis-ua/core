<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core\Interfaces;

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;
use Pllano\Core\Interfaces\DataInterface;

interface ControllerInterface extends DataInterface
{

    public function __construct(Container $app, string $route = null);

    public function get(Request $request, Response $response, array $args = []);

    public function post(Request $request, Response $response, array $args = []);
	
    /*************************************
    * Data Interface
    *************************************/
    public function createData(array $data = []);
    public function addData($key, $value = null, $pop = false);
    public function hasId();
    public function getId();
    public function setId($newId = null);
    public function setData($key, $value = null);
    public function setDataOne($key, $value);
    public function getData($key, $default = null);
    public function getDataOne($key, $default = null);
    public function pullData($key = null, $default = null);
    public function replaceData(array $data = []);
    public function allData();
    public function keysData();
    public function hasDataOne($key);
    public function hasData($key);
    public function existsData($array, $key);
    public function deleteData($key);
    public function deleteDataOne($key);
    public function removeData($key);
    public function clearDataAll();
    public function clearData($key = null);
    public function sortData($key = null);
    public function sortRecursiveData($key = null, $array = null);
    public function sortArrayData($array);
    public function accessibleData($value);
    public function isAssocData($array = null);
    public function setArrayData($array);
    public function setReferenceData(&$array);

    /*************************************
    * ArrayAccess Interface
    *************************************/
    public function offsetSet($offset, $value);
    public function offsetExists($offset);
    public function offsetUnset($offset);
    public function offsetGet($offset);

    /*************************************
    * Countable Interface
    *************************************/
    public function count();

    /*************************************
    * ArrayIterator Interface
    *************************************/
    public function getIterator();

    /*************************************
    * Magic Methods
    *************************************/
    public function __set($key, $value = null);
    public function __get($key);
    public function __isset($key);
    public function __unset($key);

}
 