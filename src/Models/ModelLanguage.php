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
        $this->config = $config;
        $this->cache = $cache;
        $this->session = $session;
        $this->_table = 'language';
		$this->_routerDb = $routerDb;
		$this->_database = $this->_routerDb->ping($this->_table);
		if (isset($this->config['db']['resource'][$this->_database])) {
			$configDatabase = $this->config['db']['resource'][$this->_database];
		    if (isset($configDatabase['driver'])) {
			    $this->_driver = $configDatabase['driver'];
		    }
		    if (isset($configDatabase['adapter'])) {
			    $this->_adapter = $configDatabase['adapter'];
		    }
		    if (isset($configDatabase['format'])) {
			    $this->_format = $configDatabase['format'];
		    }
		}
        $this->_routerDb->setConfig([], $this->_driver, $this->_adapter, $this->_format);
        $this->db = $this->_routerDb->run($this->_database);
    }

    public function get(Request $request)
    {
        $getParams = $request->getQueryParams();
        $langs = new $this->config['vendor']['detector']['language']();
		$this->language = 'ru';
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
        } elseif ($this->config['settings']['language']) {
            $this->language = $this->config['settings']['language'];
        }

        $host = $request->getUri()->getHost();
        $return = [];
        if ($this->cache->run($host.'/'.$this->_table.'/'.$this->language, $this->cacheLifetime) === null) {
            // Database GET
            $responseArr = $this->db->get($this->_table) ?? [];
            $arr = [];
            if (isset($responseArr)) {
                foreach ($responseArr as $value)
                {
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
        if (isset($cacheLifetime)) {
            $this->cacheLifetime = $cacheLifetime;
        }
    }

}
 