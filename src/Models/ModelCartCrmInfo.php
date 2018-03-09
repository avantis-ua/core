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

class ModelCartCrmInfo extends Model implements ModelInterface
{

    private $_lastquery = '';
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

    public function table($tablename)
    {

    }
    
    public function primary($primaryname)
    {

    }

    public function field($primary_id, $fieldname)
    {

    }

    public function row($primary_id)
    {

        return $this->_data;
    }

    public function data()
    {
        return $this->_data;
    }

    public function lastQuery()
    {
        return $this->_lastquery;
    }

    private function tableExists()
    {

    }

}
 