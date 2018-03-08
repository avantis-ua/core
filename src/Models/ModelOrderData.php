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

class ModelOrderData extends Model implements ModelInterface
{

    private $orderdata = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->orderdata = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'cart_orderdata';
        $this->_idField = 'id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }

    public function getRow($id)
    {
        return $this->data;
    }

    public function getIdByAlias($alias)
    {
        if($this->data) {
            return $this->data['id'];
        } else {
            return 0;
        }
    }

    public function getIdByAliasuser($alias)
    {
        if($this->data) {
            return $this->data['id'];
        } else {
            return 0;
        }
    }

}
 