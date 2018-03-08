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
use Pllano\Core\Models{
    ModelProducts,
    ModelCrmApi
}

class ModelProduct extends Model implements ModelInterface
{
    
    private $product = [];
    
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

    static public function avaliabilityList($product)
    {
        $cacheId = 'productAvaliabilityList_'.$this->siteId.'_'.$product['product_id'];
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

    public function getRow($id, $onlyPublished = 1)
    {
        $cacheId = 'productGetRow_'.$this->siteId.'_'.$id.'_'.$onlyPublished;        
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

    public function getSeo($data)
    {
        return $this->data;
    }

    public function getAlias(int $id)
    {
        $alias = 0;
        return $alias;
    }

    public function getIdByAlias($alias)
    {
        if ($this->data) {
            return $this->data['id'];
        }
        return 0;
    }

    static function getRowForDiscount($id)
    {
        return $this->data;
    }
    
    public function getProperties($p, $mod_only = 0)
    {
        if(!$p['type_id']) return [];
        $cacheId = 'productGetProperties_'.$this->siteId.'_'.$p['product_id'].'_'.$mod_only;
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
    
    public function getSame($propertiesArr, $productData)
    {
        $propertyIds = [];
        $valueIds = [];
        foreach ($propertiesArr as $property_id => $property)
        {
            if($property['same']) {
                $propertyIds[$property_id] = $property_id;
                foreach($property['values'] as $value_id => $value)
                {
                    $valueIds[$value_id] = $value_id;
                }
            }
        }
        if($propertyIds && $valueIds) {
            //--------------//
        }
        if(isset($rows) and count($rows)) {
            return $this->data;
        } else {
            return false;
        }
    }
    
    public function getOther($propertiesArr, $productData)
    {
        $propertyIds = [];
        return $propertiesArr;
    }
    
    public function getSerieProducts($productData)
    {
        if(!$productData['product_id']) return false;
    }
    
    public function getRelatedProducts($productData)
    {

    }
    
    public function updateHits($row)
    {

    }

    static public function findDiscount($product)
    {
        $cacheId = 'productsGetBuytogetherList_'.$this->siteId.'_'.$product['product_id'];
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

    static public function findMarkdown($product, $mode = 'single')
    {
        $product_id = (is_array($product)) ? $product['product_id'] : $product;
        $_apidata = [
        "action" =>"getMarkdown",
        "item" => "current",
        "value" => $product_id,
        "mode" => $mode
        ];
        return ModelCrmApi::get($_apidata);
    }

    static public function getComplect($product_id)
    {

    }

    public function getMods($parent)
    {

    }
    
    public function getBuytogether($id)
    {

    }
}

