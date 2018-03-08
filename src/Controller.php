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
use Pllano\Interfaces\DataInterface;
use Pllano\Core\Data;
use Pllano\Core\Models\{
    ModelSite,
    ModelUser, 
    ModelUserData,
    ModelSessionUser
};

class Controller extends Data implements DataInterface
{

    protected $siteId;
    protected $config = [];
    protected $package = [];
    protected $modules = [];
    protected $session;
    protected $sessionUser;
    protected $routers;
    protected $cache;
    protected $languages;
    protected $language;
    protected $lang;
    protected $logger;
    protected $template;
    protected $view;
    protected $time_start;
    protected $route = 'index';
    protected $query;
    protected $render;
    protected $site;
    protected $user;
    protected $userData;
    protected $currencyName;
    protected $data;
    protected $error;
    protected $admin_uri = '/_';
    protected $post_id = '/_'; 

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
        $this->modules = $this->app->get('modules');
        $this->routers = $this->config['routers'];
        $this->session = $this->app->get('session');
        $this->sessionUser =(new ModelSessionUser($this->app))->get();
        $this->cache = $this->app->get('cache');
        $this->languages = $this->app->get('languages');
        $this->logger = $this->app->get('logger');
        $this->template = $this->app->get('template');
        $this->view = $this->app->get('view');
        $this->siteId = $this->app->get('siteId');
        $this->site = new ModelSite($this->app);
        $this->site->getOne($this->siteId);
        $this->currencyName = $this->site->shortname;
        //$this->userData = new ModelUserData($this->app);
        //$this->user = new ModelUser($this->app);
        //$this->error = new ModelError($this->app);
        $this->render = $this->template['layouts']['404'] ? $this->template['layouts']['404'] : '404.html';
        if(!empty($this->session->admin_uri)) {
            $this->admin_uri = '/'.$this->session->admin_uri;
        }
        if(!empty($this->session->post_id)) {
            $this->post_id = '/'.$this->session->post_id;
        }
    }

}
 