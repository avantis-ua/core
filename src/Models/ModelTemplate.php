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

use Pllano\Core\Plugins\PluginTemplate;

class ModelTemplate
{

    private $plugin;

    public function __construct($config, $template)
    {
        $this->plugin = new PluginTemplate($config, $template);
    }

    public function get()
    {
        return $this->plugin->config();
    }

    public function getAll()
    {
        return $this->plugin->get();
    }

    public function getOne()
    {
        return $this->plugin->getOne();
    }

    public function put($param)
    {
        return $this->plugin->put($param);
    }

}
