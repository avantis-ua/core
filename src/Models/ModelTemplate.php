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

class ModelTemplate
{

    private $template;
    private $config;

    public function __construct($config, $template)
    {
        $this->config = $config;
        $this->template = $template;
    }

    public function get()
    {
        if(isset($this->template)) {
            $json_dir = $this->config['template']['front_end']['themes']['dir'].'/'.$this->config['template']['front_end']['themes']['templates'].'/'.$this->template.'/config/';
            if (file_exists($json_dir."config.json")) {
                return json_decode(file_get_contents($json_dir."config.json"), true);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

}
 