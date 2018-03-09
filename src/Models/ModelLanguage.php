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

use Psr\Http\Message\ServerRequestInterface as Request;

class ModelLanguage
{
    private $language = "en";
    private $cacheLifetime = 30*24*60*60;
    private $cache;
    private $routerDb;
    private $_database;
    private $_table;
    private $db;
    private $session;

    public function __construct($config, $routerDb, $cache, $session)
    {
        $this->config = $config;
        $this->cache = $cache;
        $this->session = $session;
        $this->routerDb = $routerDb;
        $this->_table = 'language';
        //$this->_idField = "language_id";
        //$this->_adapter = 'Pdo';
        //$this->db->setAdapter($this->_adapter);
        //$this->connectDatabases();
    }

    // Ресурс language доступен только на чтение
    public function get(Request $request)
    {
        $getParams = $request->getQueryParams();
        // Подключаем определение языка в браузере
        $langs = new $this->config['vendor']['detector']['language']();
        // Получаем массив данных из таблицы language на языке из $this->session->language
        if (isset($getParams['lang'])) {
            if ($getParams['lang'] == "ru" || $getParams['lang'] == "ua" || $getParams['lang'] == "en" || $getParams['lang'] == "de") {
                $this->language = $getParams['lang'];
                $this->session->language = $getParams['lang'];
            } elseif (isset($this->session->language)) {
                $this->language = $this->session->language;
            } else {
                $this->language = $langs->getLanguage();
            }
        } elseif (isset($this->session->language)) {
            $this->language = $this->session->language;
        } elseif ($langs->getLanguage()) {
            $this->language = $langs->getLanguage();
        } else {
            $this->language = $this->config['settings']['language'];
        }

        $host = $request->getUri()->getHost();

        $return = [];

        if ($this->cache->run($host.'/'.$this->_table.'/'.$this->language, $this->cacheLifetime) === null) {

            $responseArr = [];
            // Отдаем роутеру RouterDb конфигурацию
            $this->routerDb->setConfig([], 'Apis');
            // Пингуем для ресурса указанную и доступную базу данных
            $this->_database = $this->routerDb->ping($this->_table);
            // Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
            $this->db = $this->routerDb->run($this->_database);
            // Отправляем запрос к БД в формате адаптера. В этом случае Apis
            $responseArr = $this->db->get($this->_table);

            if ($responseArr != null) {
                $arr = [];
                foreach($responseArr['body']['items'] as $value)
                {
                    $array = (array)$value['item'];
                    $arr[$array["id"]] = $array[$this->language];
                }
                if ($this->cache->state() == 1) {
                    $this->cache->set($arr);
                }

                $return = $arr;
 
            } else {
                $return = $this->cache->get();
            }
        } else {
            $return = $this->cache->get();
        }
        
        
        
        return $return;
 
    }

    public function lang()
    {
        return $this->language ?? null;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function cacheLifetime($cacheLifetime = null)
    {
        if (isset($cacheLifetime)) {
            $this->cacheLifetime = $cacheLifetime;
        }
    }

}
 