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

class ModelImages extends Model implements ModelInterface
{

    private $image = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->image = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'product_image';
        $this->_idField = 'image_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }

    public function getProduct($id, $mod_only = 0)
    {
        $cacheId = 'imagesGetProduct_'.$this->siteId.'_'.$id.'_'.$mod_only;
        if ($this->cache->run($cacheId) === null) {

            $this->data = $this->db->get($this->_table, [], $currency_id);

            if ((int)$this->cache->state() == 1) {
                 $this->cache->set($this->data);
            }
        } else {
            $this->data = $this->cache->get();
        }
        return $this->data ?? null;
    }
    
    public function getProducts($productsArr)
    {

    }
    
    public function imageResize($original, $width, $height)
    {

    }

    static public function formatPaths($product_id, $imagesArray)
    {

    }

}
 