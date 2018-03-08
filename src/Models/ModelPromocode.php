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

class ModelPromocode extends Model implements ModelInterface
{
    private $promocode = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->promocode = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'promo_code';
        $this->_idField = 'promocode';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }

}
 