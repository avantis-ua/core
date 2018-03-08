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

class ModelCategories extends Model implements ModelInterface
{

    private $category = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->category = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'category';
        $this->_idField = 'category_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }

    public function getArr($parent_id = -1)
    {
        return $this->data;
    }

    public function getRow($id)
    {
        return $this->data;
    }

    public function getBrand(int $id)
    {
        if ($id == 0) return '';
        $this->_table = 'product_brand';
        $this->_idField = 'brand_id';
        $this->data = $this->db->get($this->_table, [], $id);
        if ($this->data) {
            return $data['title'];
        } else {
            return '';
        }
    }

    public function getSerie(int $id)
    {
        if ($id == 0) return '';
        $this->_table = 'product_serie';
        $this->_idField = 'serie_id';
        $this->data = $this->db->get($this->_table, [], $id);
        if ($this->data) {
            return $data['title'];
        } else {
            return '';
        }
    }

    public function getProperties($ids, $typesArr)
    {
        return $properties;
    }

    public function getTypes($category)
    {
        return $this->data;
    }

    public function getTypesMulti($categories)
    {
        return $this->data;
    }

    public function productsOrder(int $category_id)
    {
        
    }

    protected function _getSubcategories($categories, $parents, &$subcategories)
    {
        
    }

    function getSubcategories($parents)
    {
        $categoriesArr = $this->getArr();
        
        return $categoriesArr;
    }

    function getSubmenuProducts($categoriesArr, $maxProducts)
    {
        return $productsArr;
    }

}
 