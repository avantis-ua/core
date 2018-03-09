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

class ModelSiteProductPrice extends Model implements ModelInterface
{

    private $site_price = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->site_price = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'site_product_price';
        $this->_idField = 'product_price_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }
    
    public function getIdItem($alias)
    {

    }
    
    public function getPriceItem($seller_id, $sort, $order, $state = 1, $type = null, $brand = null, $serie = null, $articul = null, $brand_id = null, $product_id = null, $search = null, $paginatorOn = 1)
    {

    }
    
    public function getElastic($seller_id, $limit = null, $offset = null, $sort = null, $order = null, $state = null, $brand_id = null, $product_id = null, $search = null, $operator = null)
    {

    }

}
 