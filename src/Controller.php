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
use Pllano\Core\Interfaces\DataInterface;
use Pllano\Core\Data;

class Controller extends Data implements DataInterface
{

    protected $siteId;
	protected $config = [];
    protected $package = [];
    protected $session;
    protected $cache;
    protected $languages;
    protected $logger;
    protected $template;
    protected $view;
	protected $time_start;
    protected $route = 'index';
    protected $query;
	protected $render;
	protected $data;

    public function __construct(Container $app, string $route = null)
    {
        $this->app = $app;
		$this->data = new Data([]);
        if(isset($route)) {
            $this->route = $route;
        }
        $this->config = $this->app->get('config');
        $this->time_start = $this->app->get('time_start');
        $this->package = $this->app->get('package');
        $this->session = $this->app->get('session');
        $this->cache = $this->app->get('cache');
        $this->languages = $this->app->get('languages');
        $this->logger = $this->app->get('logger');
        $this->template = $this->app->get('template');
        $this->view = $this->app->get('view');
		$this->siteId = $this->app->get('siteId');
		$this->render = $this->template['layouts']['404'] ? $this->template['layouts']['404'] : '404.html';
    }

}
 