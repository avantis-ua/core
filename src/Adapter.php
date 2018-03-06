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

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;
use Pllano\Core\Interfaces\AdapterInterface;

class Adapter implements AdapterInterface
{

    protected $app;
    protected $config = [];
    protected $time_start;
    protected $package = [];
    protected $session;
    protected $languages;
    protected $logger;
    protected $template;
    protected $view;

    function __construct(Container $app)
    {
        $this->app = $app;
        $this->config = $app->get('config');
        $this->time_start = $app->get('time_start');
        $this->package = $app->get('package');
        $this->session = $app->get('session');
        $this->languages = $app->get('languages');
        $this->logger = $app->get('logger');
        $this->template = $app->get('template');
        $this->view = $app->get('view');
    }

}
 