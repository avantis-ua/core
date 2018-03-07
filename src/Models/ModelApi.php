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
use Pllano\Core\Model;

class ModelApi extends Model implements ModelInterface
{

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->connectContainer();
        $this->connectDatabases();
		$this->_table = 'api';
        $this->_idField = 'id';
    }

    public function get(Request $request, Response $response, array $args)
    {
        return null;
    }

    public function post(Request $request, Response $response, array $args)
    {
        return null;
    }

	public function getApi($public_key)
	{
		// Отдаем роутеру RouterDb конфигурацию
		$this->routerDb->setConfig([], 'Apis');
		// Пингуем для ресурса указанную и доступную базу данных
		$this->_database = $this->routerDb->ping($this->_table);
		// Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
		$this->db = $this->routerDb->run($this->_database);
		// Массив для запроса
		$query = [
		    "public_key" => $public_key, 
		    "state" => 1
		];
		// Отправляем запрос к БД в формате адаптера. В этом случае Apis
		$this->data = $this->db->get($this->_table, $query);
		if($this->data) {
			return $this->data['id'];
		} else {
			return null;
		}
	}

	public function getKeyId($seller_id)
	{
		// Отдаем роутеру RouterDb конфигурацию
		$this->routerDb->setConfig([], 'Apis');
		// Пингуем для ресурса указанную и доступную базу данных
		$this->_database = $this->routerDb->ping($this->_table);
		// Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
		$this->db = $this->routerDb->run($this->_database);
		// Массив для запроса
		$query = [
		    "seller_id" => $seller_id, 
		    "state" => 1
		];
		// Отправляем запрос к БД в формате адаптера. В этом случае Apis
		$this->data = $this->db->get($this->_table, $query);
		if($this->data) {
			return $this->data['public_key'];
		} else {
			return null;
		}
	}

}
 