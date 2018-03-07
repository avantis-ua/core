<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core\Plugins;

use Pllano\Interfaces\PluginInterface;

class PluginResources implements PluginInterface
{
    private $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    // Проверяем разрешен ли этот тип запроса для данного ресурса
    public function test_query($resource)
    {
        // Если ресурс активен
        if (isset($this->config["settings"]["admin"]["resource"][$resource])) {
            if ($this->config["settings"]["admin"]["resource"][$resource] == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
 