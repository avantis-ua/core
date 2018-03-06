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

class Controller
{

    protected $config = [];
    protected $time_start;
    protected $package = [];
    protected $session;
	protected $cache;
    protected $languages;
    protected $logger;
    protected $template;
    protected $view;
    protected $route = 'index';
    protected $query;

    public function __construct(Container $app, string $route = null)
    {
        $this->app = $app;
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
		
    }

}
 