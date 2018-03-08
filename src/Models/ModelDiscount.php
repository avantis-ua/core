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

class ModelDiscount extends Model implements ModelInterface
{

    private $product_discount = [];
    public $paginator = null;

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->product_discount = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'product_discount';
        $this->_idField = 'id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
    }

    public function getArr(int $paginatorOn = 1)
    {
        return $this->data;
    }

    public function getSlider(int $id, int $limit = 10)
    {
        return $this->data;
    }

    public function getRow(int $id)
    {
        return $this->data;
    }

}

