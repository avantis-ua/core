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

class ModelAreaCity extends Model implements ModelInterface
{

    public function __construct(Container $app)
    {
        parent::__construct($app);
        // $this->connectContainer();
        $this->connectDatabases();
    }

    public function stores_list()
    {
        $responseArr = [];
        // Ресурс к которому обращаемся
        $this->_table = "stores_list";
        // Отдаем роутеру RouterDb конфигурацию
        $this->routerDb->setConfig([], 'Pllano', 'Apis');
        // Пингуем для ресурса указанную и доступную базу данных
        $this->_database = $this->routerDb->ping($this->_table);
        // Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
        $this->db = $this->routerDb->run($this->_database);
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
        // Отдаем роутеру RouterDb конфигурацию
        $this->routerDb->setConfig([], 'Pllano', 'Apis');
        // Пингуем для ресурса указанную и доступную базу данных
        $this->_database = $this->routerDb->ping($this->_table);
        // Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
        $this->db = $this->routerDb->run($this->_database);
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
 