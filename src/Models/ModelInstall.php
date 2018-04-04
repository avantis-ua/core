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

class ModelInstall extends Model implements ModelInterface
{

    public function __construct(Container $app)
    {
        $this->app = $app;
		parent::__construct($this->app);
		$this->_routerDb = $this->app->get('routerDb');
    }

    public function stores_list()
    {
        $responseArr = [];
        // Ресурс к которому обращаемся
        $this->_table = "stores_list";
		$this->_routerDb->setConfig([], 'Pllano', 'Apis', 'Apis');
        $this->_database = $this->_routerDb->ping($this->_table);
        $this->db = $this->_routerDb->run($this->_database);
        // Отправляем запрос к БД в формате адаптера. В этом случае Apis
        $responseArr = $this->db->get($this->_table);
        $this->data = $responseArr["body"]["items"] ?? [];
        return $this->data;
    }

    public function templates_list($store = null)
    {
        $responseArr = [];
        // Ресурс к которому обращаемся
        $this->_table = "templates_list";
		$this->_routerDb->setConfig([], 'Pllano', 'Apis', 'Apis');
        $this->_database = $this->_routerDb->ping($this->_table);
        $this->db = $this->_routerDb->run($this->_database);
        // Отправляем запрос к БД в формате адаптера. В этом случае Apis
        if (isset($store)) {
            $responseArr = $this->db->get($this->_table, ["store_id" => $store]);
        } else {
            $responseArr = $this->db->get($this->_table);
        }
        $this->data = $responseArr["body"]["items"] ?? [];
        return $this->data;
    }

}
 