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
use Pllano\Core\Interfaces\ModelInterface;
use Pllano\Core\Data;

class Model extends Data implements ModelInterface
{

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

    protected $db;
    protected $_adapter = 'Apis'; // Pdo
    protected $_driver = '';
    protected $_database = 'mysql';
    protected $_table = 'abstract';
    protected $_resource = 'abstract';
    protected $_idField = 'id';
    protected $_fieldMap = [];
    protected $_lastQuery = null;

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->data = new Data([]);
		$this->config = $this->app->get('config');
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

    public function connectDatabases()
    {
        $this->routerDb = $this->app->get('routerDb');
        $this->routerDb->setConfig($this->config, $this->_adapter, $this->_driver);
        $this->database = $this->routerDb->ping($this->_table);
        $this->db = $this->routerDb->run($this->database);

    }

    public function connectRouterDb()
    {
        $this->connectDatabases();
    }

    public function connectPdo()
    {
        $this->pdo = $this->app->get('pdo');
    }

    public function connectSlimPdo()
    {
        $this->slim_pdo = $this->app->get('slim_pdo');
    }

    /*************************************
    * Интерфейс работы с БД
    *************************************/

    // Строим запрос для списка или выборки одного объекта
    public function select()
    {
        $this->connectRouterDb();
        return $this->db->select()->from($this->_table);
    }

    public function query($query)
    {
        return $this->select()->query($query)->fetchAll();
    }

    public function getList($filters = [], $joinTables = null, $orderBy = null, $count = null, $offset = null) // Нужно тестить
    {
        $this->connectDatabases();

        $r = [];
        $select = $this->select();
        if (isset($joinTables)) {
            throw new \Exception("::joinTables not implemented yet!");
        }
        if (isset($filters)) {
            if(is_array($filters)) {
                foreach($filters as $filterKey => $filterValue)
                {
                    $select->where($filterKey, $filterValue);
                }
            } else {
                throw new \Exception("::filters must be an array!");
            }
        }
        if (isset($orderBy)) {
            if(is_array($orderBy)) {
                foreach($orderBy AS $k => $v)
                {
                    $select->order("{$k} {$v}");
                }
            } else {
                $select = $select->order($orderBy);
            }
        }
        if (isset($count)) {
            if($offset !== null) {
                $select = $select->limit($count, $offset);
            } else {
                $select = $select->limit($count);
            }
        }

        $this->_lastQuery = $select->__toString();

        $rows = $this->db->query($select)->fetchAll();
        if(is_array($rows))
        {
            foreach($rows as $row)
            {
                $modelClassName = get_class($this);
                $model = new $modelClassName();
                $model->setTable($this->_table);
                $model->fromArray($row);
                $r[] = $model;
            }
        }
        return $r;
    }

    // Если аргумент === массив, работаем со свободным WHERE-AND!
    public function getOne($id = null) // Нужно тестить
    {
        $this->connectDatabases();
        
        $select = $this->select();
        if (isset($id)) {
            if(is_array($id)) 
            {
                foreach($id as $filterKey => $filterValue)
                {
                    $select->where($filterKey, $filterValue);
                }
            } else {
                $select->where($this->_idField, '=', intval($id));
            }
        } elseif($this->hasId()) { 
            $id = $this->getId(); // если не обозначен аргумент, берем из данных модели
        } else {
            return false; // throw new Exception(__METHOD__ . " : not ID#");
        }

        //$this->_lastQuery = $select->__toString();

        $rows = $this->db->query($select)->fetchAll(PDO::FETCH_ASSOC);

        $r = false;
        if(is_array($rows)) {
            if(count($rows) > 0) {
                $this->fromArray($rows[0]);
                $r = true;
            }
        }
        return $r;
    }

    public function getIdByAlias($alias)
    {
        $this->connectDatabases();
        $query = [
            "alias" => $alias,
            "site_id" => $this->siteId
        ];
        $id = null;
        $this->data = $this->db->get($this->_table, $query, $id, $this->_idField);
        return $this->data['id'] ?? null;
    }

    // Save
    public function save() // Нужно тестить
    {
        $this->connectDatabases();
        $current_date = $this->selectDate();
        $this->data['modified'] = $current_date;
        $this->data['visited'] = $current_date;
        if(!$this->hasId())
        {
            $this->data['created'] = $current_date;

            $this->db->insert($this->toArray());
            
            $this->setId($this->db->lastInsertId());
            
            return $this->getId();
        } else {
            $where = $this->db->quoteInto($this->_idField." = ?", $this->getId());
            $rows_affected = $this->db->update($this->toArray(), $where);
            return null;
        }
    }

    // Delete
    public function delete() // Ok
    {
        $this->connectDatabases();
        if(!$this->hasId()) throw new \Exception("::Trying to remove nonexistent property!");
        $this->db->delete()
                 ->from($this->_table)
                 ->where($this->_idField, '=', $this->getId());
    }

    // Подсчитываем количество полей в указанной таблице
    public function countIt($table, $whereState) // Ok
    {
        $this->connectDatabases();
        $select = 'SELECT COUNT(*) AS `num` FROM `'.$table.'` WHERE '. $whereState;
        $row = $this->db->query($select)->fetch();
        return $row['num'];
    }

    public function getFieldMap($table = null) // Ok
    {
        if (isset($table)) {
             $this->connectDatabases();
             $this->_fieldMap = $this->db->fieldMap($table);
        }
    }

    public function fieldMap($table = null) // Ok
    {
        $fieldMap = null;
        if (isset($table)) {
             $this->connectDatabases();
             $fieldMap = $this->db->fieldMap($table);
        }
        return $fieldMap;
    }

    public function tableSchema($table = null) // Ok
    {
        $table_schema = [];
        if (isset($table)) {
            $fieldMap = $this->fieldMap($table);
            foreach($fieldMap as $column)
            {
                $field = $column['Field'];
                $field_type = $column['Type'];
                $table_schema[$field] = $field_type;
            }
        }
        return $table_schema;
    }

    public function setTable($table) // Ok
    {
        $this->_table = $table;
        if($this->_idField === null) {
            $this->_idField = "id";
        }
        if($this->_table !== 'abstract')
        {
            $this->connectDatabases();
            $this->_fieldMap = $this->db->fieldMap($this->_table);
        }
    }

    public function getTable() // Ok
    {
        return $this->_table;
    }

    public function getIdField() // Ok
    {
        return $this->_idField;
    }

    public function setIdField($fieldName = null) // Ok
    {
        if(isset($fieldName)) {
            $this->_idField = $fieldName;
        }
    }

    // Получаем дату в формате "0000-00-00 00:00:00"
    // $minutes должно быть целым положительным числом
    static public function selectDate($minutes = null) // Ok
    {
        $this->connectDatabases();
        if (isset($minutes)) {
            $query = "SELECT DATE_FORMAT(NOW() + INTERVAL '".intval($minutes)."' MINUTE, '%Y-%m-%d %H:%i:%s') AS selected_date";
        } else {
            $query = "SELECT DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS selected_date";
        }
        $row = $this->db->query($query)->fetch(PDO::FETCH_OBJ);
        return $row->selected_date;
    }
    
    // Строим запрос для списка или выборки одного объекта
    protected function _buildSelectQuery() // Ok
    {
        $this->connectDatabases();
        return $this->db->select()->from($this->_table);
    }

}
 