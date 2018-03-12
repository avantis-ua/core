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
 
class PluginTemplate implements PluginInterface
{
    protected $template = null;
    protected $config;
 
    function __construct($config, $template = null)
    {
        // Устанавливаем название шаблона
        if ($template != null) {
            $this->template = $template;
        }
        $this->config = $config;
    }

    public function get()
    {
        $resp["templates"] = [];
        $templates = [];
        $directory = $this->config["template"]["front_end"]["themes"]["dir"]."/".$this->config["template"]["front_end"]["themes"]["templates"];
        $scanned = array_diff(scandir($directory), ['..', '.']);
        if (count($scanned) >= 1) {
            foreach($scanned as $dir)
            {
                if (is_dir($directory.'/'.$dir)) {
                    $json_dir = $this->config["template"]["front_end"]["themes"]["dir"].'/'.$this->config["template"]["front_end"]["themes"]["templates"].'/'.$dir.'/config/';
                    if (file_exists($json_dir."config.json")) {
                        $json = json_decode(file_get_contents($json_dir."config.json"), true);
                         $template = $json;
                         $templates["alias"] = $template["alias"];
                         $templates["template_engine"] = $template["template_engine"];
                         $templates["name"] = $template["name"];
                         $templates["dir"] = $dir;
                         $templates["version"] = $template["version"];
                         $templates["url"] = $template["url"];
                         if(isset($template["demo"])){
                             $templates["demo"] = $template["demo"];
                         } else {
                             $templates["demo"] = "https://".$dir.".pllano.com/";
                         }
 
                         $resp["templates"][] = $templates;
                    }
                }
            }
        }
        return $resp;
    }

    public function config()
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

    public function getOne()
    {
        if ($this->template != null) {
            $json_dir = $this->config["template"]["front_end"]["themes"]["dir"].'/'.$this->config["template"]["front_end"]["themes"]["templates"].'/'.$this->template.'/config/';
            if (file_exists($json_dir."config.json")) {
                return json_decode(file_get_contents($json_dir."config.json"), true);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function put($param)
    {
        $arr = array_replace_recursive($this->get(), $param);
        $newArr = json_encode($arr);
        $json_dir = $this->config["template"]["front_end"]["themes"]["dir"].'/'.$this->config["template"]["front_end"]["themes"]["templates"].'/'.$this->template.'/config/';
        file_put_contents($json_dir."config.json", $newArr);
        return true;
    }
 
}
 