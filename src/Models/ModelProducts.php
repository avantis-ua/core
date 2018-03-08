<?php 
/**
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
use Pllano\RouterDb\Router as RouterDb;
use Pllano\Core\Models{
    ModelProduct,
    ModelCrmApi
}

class ModelProducts extends Model implements ModelInterface
{

    private $product = [];
    public $paginator = null;

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->product = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'product';
        $this->_idField = 'product_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
    }

    public function getArr($typesArr = [], $text = '', $propertyValueIds = [], $slidersData = [], $paginatorOn = 1, $brandId = 0, $serieId = 0, $price = [], $productsOrdering = 'priority', $limit = 0, $onlyAvailable = 0, $product_id = 0, $mod_id = 0, $mod_only = 0, $special = 0, $discountsArr = [], $productIds = [])
    {
        return $this->data;
    }
    
    public function getRow($id, $onlyPublished = 1)
    {
        return $this->data;
    }

    public function setDiscounts(&$productsArr)
    {
        foreach($productsArr as $key => &$product)
        {
            $product['discount'] = ModelProduct::findDiscount($product);
        }
        unset($product);
    }

    public function getProperties($productsArr)
    {
        $propertiesArr = [];
        return $propertiesArr;
    }

    public function updateHits($rows)
    {
        foreach($rows as $row)
        {

        }
    }

    public function getSeo($tablename, $id)
    {
        $seo_data = '';
        return $seo_data;
    }

    public function getBuytogether(int $type_id, int $paginatorOn = 1)
    {
        $cacheId = 'productsGetBuytogetherList_'.$this->siteId.'_'.$type_id;
        // $cacheId = $host.''.$params.'/'.$this->lang.'/'.$this->route;
        if ($this->cache->run($cacheId) === null) {
            $this->data = '';
            if ((int)$this->cache->state() == 1) {
                 $this->cache->set($this->data);
            }
        } else {
            $this->data = $this->cache->get();
        }
        return $this->data;
    }

}
 