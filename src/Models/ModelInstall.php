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
		$this->_table = "stores_list";
		$this->_database = $this->_routerDb->ping($this->_table);
		$resource = $this->config['db']['resource'][$this->_database] ?? null;
		$this->_driver = $resource['driver'] ?? null;
		$this->_adapter = $resource['adapter'] ?? null;
		$this->_format = $resource['format'] ?? null;
		$this->_routerDb->setConfig([], $this->_driver, $this->_adapter, $this->_format);
		$this->db = $this->_routerDb->run($this->_database);

        $responseArr = [];
		$responseArr = $this->db->get($this->_table);
        $this->data = $responseArr ?? [];
        return $this->data;
    }

    public function templates_list($store = null)
    {
        $this->_table = "templates_list";
		$this->_database = $this->_routerDb->ping($this->_table);
		$resource = $this->config['db']['resource'][$this->_database] ?? null;
		$this->_driver = $resource['driver'] ?? null;
		$this->_adapter = $resource['adapter'] ?? null;
		$this->_format = $resource['format'] ?? null;
		$this->_routerDb->setConfig([], $this->_driver, $this->_adapter, $this->_format);
		$this->db = $this->_routerDb->run($this->_database);
		
		$responseArr = [];
        if (isset($store)) {
            $responseArr = $this->db->get($this->_table, ["store_id" => $store]);
        } else {
            $responseArr = $this->db->get($this->_table);
        }
        $this->data = $responseArr ?? [];
        return $this->data;
    }

}
 