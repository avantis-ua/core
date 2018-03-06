<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core\Managers;

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;
use Pllano\Core\Interfaces\ManagerInterface;
 
class Modules implements ManagerInterface
{
    private $app;
    private $package;
    private $route;
    private $block;
    private $modules = [];

    function __construct(Container $app, string $route = null, string $block = null)
    {
        $this->app = $app;
        $this->block = $block;
        $this->route = $route;
        $this->package = $app->get('package');
        if (isset($app->get('config')['modules'][$this->block])) {
            $this->modules = $app->get('config')['modules'][$this->block];
        }
    }

    public function get(Request $request): array
    {
        $resp = [];
        if (isset($this->modules)) {
            foreach($this->modules as $modulKey => $modulVal)
            {
                if($modulVal['state'] == 1) {
                    if (isset($modulVal['vendor'])) {
                        $plugin = new $modulVal['vendor']($this->app, $this->route, $this->block, $modulKey, $modulVal);
                        $function = $modulVal['function'];
                        $arr = $plugin->$function($request);
                    } else {
                        if ($this->block == $this->route) {
                            $arr['content']['modules'][$modulKey]['config'] = $this->modules[$modulKey];
                        } else {
                            $arr[$this->block]['modules'][$modulKey]['config'] = $this->modules[$modulKey];
                        }
                    }
                    $resp = array_replace_recursive($resp, $arr);
                }
            }
        }
        return $resp;
    }

    public function post(Request $request)
    {
        $resp = [];
        $modulKey = $this->block;
        if (isset($this->modules[$modulKey]) && $this->modules[$modulKey]['state'] == 1) {
            if (isset($this->modules[$modulKey]['vendor'])) {
                $modulVal = $this->modules[$modulKey];
                $plugin = new $this->modules[$modulKey]['vendor']($this->app, $this->route, $this->block, $modulKey, $modulVal);
                $function = $this->modules[$modulKey]['function'];
                $arr = $plugin->$function($request);
            }
            $resp = array_replace_recursive($resp, $arr);
        }
        return $resp;
    }
 
}
 