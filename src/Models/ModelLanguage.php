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
    private $language = "ru";
    private $cacheLifetime = 30*24*60*60;
    private $cache;
	private $session;
    private $db;
    private $_routerDb;
    private $_database;
    private $_table;
	private $_idField;
	private $_driver;
	private $_adapter;
	private $_format;

    public function __construct($config, $routerDb, $cache, $session)
    {
        $this->_table = 'language';
		$this->config = $config;
        $this->cache = $cache;
        $this->session = $session;
		$this->_routerDb = $routerDb;
		$this->language = 'en';
    }

    public function get(Request $request)
    {
        $return = null;
		$getParams = $request->getQueryParams();
        $langs = new $this->config['vendor']['detector']['language']();
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
        } elseif ($langs->getLanguage() != null) {
            $this->language = $langs->getLanguage();
        } elseif (isset($this->config['settings']['language'])) {
            $this->language = $this->config['settings']['language'];
        }

        $host = $request->getUri()->getHost() ?? 'site';
        
        if ($this->cache->run($host.'/'.$this->_table.'/'.$this->language, $this->cacheLifetime) === null) {

            $this->_database = $this->_routerDb->ping($this->_table);
            $resource = $this->config['db']['resource'][$this->_database] ?? null;
            $this->_driver = $resource['driver'] ?? null;
            $this->_adapter = $resource['adapter'] ?? null;
            $this->_format = $resource['format'] ?? null;
            $this->_routerDb->setConfig([], $this->_driver, $this->_adapter, $this->_format);
            $this->db = $this->_routerDb->run($this->_database);
            // Database GET
            $responseArr = $this->db->get($this->_table) ?? [];
            $arr = [];
            if (isset($responseArr)) {
                foreach ($responseArr as $value)
                {
					if(is_object($value)) {
                        $value = (array)$value;
                    }
                    $arr[$value['id']] = $value[$this->language];
                }
                if ($this->cache->state() == 1) {
                    $this->cache->set($arr);
                }
            }
            $return = $arr;
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
        $this->cacheLifetime = $cacheLifetime ?? null;
    }

}
 