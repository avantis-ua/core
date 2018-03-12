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

class ModelSite extends Model implements ModelInterface
{

    private $site;
	private $templates;
    private $cache_lifetime = 30*24*60*60;

    public function __construct(Container $app)
    {
		$this->_table = 'site';
        $this->_idField = 'site_id';
		$this->site = new Data([]);
		parent::__construct($app);
        $this->connectContainer();
        $this->templates = $this->config["template"]["front_end"]["themes"]["template"];
    }

    public function get()
    {
        $cache_run = $this->cache->run($this->_table, $this->cache_lifetime);
        if ($cache_run === null) {
			// Database GET
            $responseArr = $this->db->get($this->_table);
			if(is_object($responseArr)) {
                $responseArr = (array)$responseArr;
            }
            if(isset($responseArr['0'])) {
                    $this->data = $responseArr['0'];
            }
            if ($this->cache->state() == 1) {
                $this->cache->set($this->data);
            }
            return $this->data;
        } else {
            return $this->cache->get();
        }
    }
    
    public function getOne($id = null)
    {
        
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
 