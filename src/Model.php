<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core;

use Psr\Container\ContainerInterface as Container;
use Pllano\Interfaces\ModelInterface;
use Pllano\Core\Data;

class Model extends Data implements ModelInterface
{
    protected $db;
	protected $app;
    protected $data;
    protected $config = [];
    protected $time_start;
    protected $package = [];
    protected $session;
    protected $languages;
    protected $logger;
    protected $template;
    protected $view;
    protected $cache;
    protected $siteId = 1;
    protected $pdo;
    protected $slim_pdo;
    protected $routerDb;
	protected $_adapter = 'Json'; // Mysql, Pllano, Elasticsearch, Json
	protected $_driver = 'Apis'; // Apis, Pdo and VendorName
	protected $_format = 'Default'; // Default=Array, Object, Apis
    protected $_database;
    protected $_table;
    protected $_resource;
    protected $_idField = 'id';
    protected $_fieldMap = [];
    protected $_lastQuery = null;

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->data = new Data([]);
        $this->config = $this->app->get('config');
		if (isset($this->_table)) {
            $this->connectDatabases();
			// print("<br>class Model construct: {$this->_database} - {$this->_table} - {$this->_driver} - {$this->_adapter} - {$this->_format}<br>");
		}
    }

    public function connectDatabases()
    {
        $this->_routerDb = $this->app->get('routerDb');
		$this->_database = $this->_routerDb->ping($this->_table);
		$resource = $this->config['db']['resource'][$this->_database] ?? null;
		$this->_driver = $resource['driver'] ?? null;
		$this->_adapter = $resource['adapter'] ?? null;
		$this->_format = $resource['format'] ?? null;
        $this->_routerDb->setConfig([], $this->_driver, $this->_adapter, $this->_format);
        $this->db = $this->_routerDb->run($this->_database);
    }

    public function connectContainer()
    {
        $this->time_start = $this->app->get('time_start');
        $this->package = $this->app->get('package');
        $this->session = $this->app->get('session');
        $this->languages = $this->app->get('languages');
        $this->logger = $this->app->get('logger');
        $this->template = $this->app->get('template');
        $this->view = $this->app->get('view');
        $this->cache = $this->app->get('cache');
        $this->siteId = $this->app->get('siteId');
    }

    public function connectPdo()
    {
        $this->pdo = $this->app->get('pdo');
    }

    public function connectSlimPdo()
    {
        $this->slim_pdo = $this->app->get('slim_pdo');
    }

    public function lastId(string $resource = null): int
    {
        return (int)$this->db->lastId($resource);
    }

    public function getIdByAlias(string $alias = null): int
    {
        if (isset($alias)) {
			// Database GET
		    $this->data = $this->db->get($this->_table, ["alias" => $alias, "state" => 1]);
			$id = $this->data['id'] ?? null;
            return (int)$id;
		} else {
		    return 0;
		}
    }

    public function getList($filters = null, $joinTables = null, $orderBy = null, $limit = null, $offset = null)
    {
		$r = [];
		$query = [];
		if($joinTables !== null) {
			throw new Exception("::joinTables not implemented yet!");
		}
		if($filters !== null) {
			if(is_array($filters)) {
				foreach ($filters as $filterKey => $filterValue)
				{
					$query[$filterKey] = $filterValue;
				}
			} else {
				throw new Exception("::filters must be an array!");
			}
		}
		if ($orderBy !== null) {
			if (is_array($orderBy)) {
				foreach($orderBy AS $k => $v) 
				{
					$query['order'] = $k;
					$query['sort'] = $v;
				}
			} else {
			    $query['order'] = $orderBy;
			}
		}
		if ($offset !== null) {
			$query['offset'] = $offset;
		}
		if($limit !== null ) {
			$query['limit'] = $count;	
		}

		// Database GET
		$rows = $this->db->get($this->_table, $query, null, $this->_idField);

        if (is_array($rows)) {
            foreach($rows as $row)
            {
                $class = get_class($this);
                $model = new $class();
                $model->setTable($this->_table);
                $model->fromArray($row);
                $r[] = $model;
            }
        }
        return $r;
    }

    public function getOne($id = null)
    {
        $r = false;
		$query = [];
		$rows = [];
		if ($id !== null) {
			if (is_array($id)) {
			    foreach($id as $filterKey => $filterValue)
			    {
				    $query[$filterKey] = $filterValue;
			    }
				$rows = $this->db->get($this->_table, $query, null, $this->_idField);
			} else {
				$rows = $this->db->get($this->_table, [], (int)$id, $this->_idField);
				$this->setId((int)$id);
			}
		} elseif ($this->hasId()) {
		    $id = $this->getId();
			$rows = $this->db->get($this->_table, [], (int)$id, $this->_idField);
		} else {
		    return $r;
		}
		if (is_array($rows)) {
			if (isset($rows[0])) {
				$this->fromArray($rows[0]);
				$r = true;
			}
		}
        return $r;
    }

    public function save()
    {
        $this->data['modified'] = today_date();
        $this->data['visited'] = today_date();
        if(!$this->hasId()) {
            $this->data['created'] = today_date();
			$id = $this->db->post($this->_table, $this->toArray());
            $this->setId($id);
            return $this->getId();
        } else {
		    if ($this->hasId()) {
		        $this->db->put($this->_table, $this->toArray(), $this->getId(), $this->_idField);
			}
            return null;
        }
    }

    public function delete()
    {
        $this->db->delete($this->_table, [], $this->getId());
		$this->clearDataAll();
    }

    public function count($table, $whereState)
    {
        return $this->db->count($table, $whereState);
    }

    public function getFieldMap($table = null)
    {
        if (isset($table)) {
             $this->_fieldMap = $this->db->fieldMap($table);
        }
    }

    public function fieldMap($table = null)
    {
        $fieldMap = null;
        if (isset($table)) {
             $fieldMap = $this->db->fieldMap($table);
        }
        return $fieldMap;
    }

    public function tableSchema($table = null)
    {
        if (isset($table)) {
            return $this->db->tableSchema($table);
        } else {
            return null;
        }
    }

    public function setTable($table = null)
    {
        if (isset($table)) {
            $this->_table = $table;
            if($this->_idField === null) {
                $this->_idField = "id";
            }
            if($this->_table !== 'abstract') {
                $this->_fieldMap = $this->db->fieldMap($this->_table);
            }
        }
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function getIdField()
    {
        return $this->_idField;
    }

    public function setIdField($idField = null)
    {
        if(isset($idField)) {
            $this->_idField = $idField;
        }
    }

    protected function _buildSelectQuery()
    {
        return null;
    }

}
 