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
use Pllano\Core\Interfaces\ModelInterface;
use Pllano\Core\Model;

class ModelSite extends Model implements ModelInterface
{

	protected $templates;
	private $cache_lifetime = 30*24*60*60;

	public function __construct(Container $app)
    {
        parent::__construct($app);
		$this->connectContainer();
		$this->connectDatabases();
        $this->_table = 'site';
		$this->_idField = 'site_id';
		$this->templates = $this->config["template"]["front_end"]["themes"]["template"];
    }

    public function get()
    {
        $cache_run = $this->cache->run($this->_table, $this->cache_lifetime);
        if ($cache_run === null) {

			$responseArr = [];
			// Отдаем роутеру RouterDb конфигурацию
			$this->routerDb->setConfig([], 'Apis');
			// Пингуем для ресурса указанную и доступную базу данных
			$this->_database = $this->routerDb->ping($this->_table);
			// Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
			$this->db = $this->routerDb->run($this->_database);
			// Отправляем запрос к БД в формате адаптера. В этом случае Apis
			$responseArr = $this->db->get($this->_table);

			$site = null;
			if(isset($responseArr["body"]["items"]["0"]["item"])) {
			    if ($responseArr != null) {
			        $site = $responseArr["body"]["items"]["0"]["item"];
			    }
			}
			if ($this->cache->state() == 1) {
			    $this->cache->set($site);
			}
			return $site;

        } else {
             return $this->cache->get();
        }
    }

    public function template()
    {
        return $this->templates;
    }

    public function cache_lifetime($cache_lifetime)
    {
        $this->cache_lifetime = $cache_lifetime;
    }

}
 