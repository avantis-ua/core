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

class ModelCurrency extends Model implements ModelInterface
{
    const USD = 2;
    const EUR = 3;
    private $currency = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->currency = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'currency';
        $this->_idField = 'currency_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }

    static public function getSystemCourse($currency_id)
    {
        $cacheId = 'currency_'.$this->siteId.'_'.$currency_id;
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

}
 