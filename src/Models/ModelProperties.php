<?php /**
    * Pllano Core (https://pllano.com)
    *
    * @link https://github.com/pllano/core
    * @version 1.0.1
    * @copyright Copyright (c) 2017-2018 PLLANO
    * @license http://opensource.org/licenses/MIT (MIT License)
*/
namespace Pllano\Core\Models;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Pllano\Interfaces\ModelInterface;
use Pllano\Core\{Model, Data};

class ModelProperties extends Model implements ModelInterface
{
    private $filters = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->filters = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'product';
        $this->_idField = 'product_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }

    public function getArr(array $typesArr = [], string $text = '')
    {
        return $this->data;
    }

    public function getProductProperties($productsArr)
    {
        return $this->data;
    }

    public function getFilters($typesArr)
    {
        return $this->filters;
    }

    public function getCounters($propertiesSelectedArr, $filters)
    {
        return $this->filters;
    }

    public function getCountersPlus($propertyValueIds, $filters)
    {
        return $this->filters;
    }

    public function getSliderValuesArr($slider, $propertyValueIds)
    {
        return $this->data;
    }

    public function getBrands($productsSelectedArr)
    {
        $brands = [];
        return $brands;
    }

}
 