<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core\Models\Admin;

use Pllano\Interfaces\ModelInterface;
use Pllano\Core\Model;
use Pllano\RouterDb\Router as RouterDb;

class AdminIndex extends Model implements ModelInterface
{

    public function get()
    {
        // Получаем список виджетов для вывода на главную
        $resource_list = explode(',', str_replace(['"', "'", " "], '', $this->config['admin']['index_widget']));
        $resp = [];
        foreach($resource_list as $resource)
        {
            if($resource == 'templates') {
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
            } else {
			    $this->_table = $resource;
			    $this->_routerDb = $this->app->get('routerDb');
			    $this->_database = $this->_routerDb->ping($this->_table);
			    $resourceConfig = $this->config['db']['resource'][$this->_database] ?? null;
			    $this->_driver = $resourceConfig['driver'] ?? null;
			    $this->_adapter = $resourceConfig['adapter'] ?? null;
			    $this->_format = $resourceConfig['format'] ?? null;
			    $this->_routerDb->setConfig([], $this->_driver, $this->_adapter, $this->_format);
			    $this->db = $this->_routerDb->run($this->_database);
                $query = [
                    "offset" => 0,
                    "limit" => 5,
                    "sort" => "id",
                    "order" => "DESC"
                ];
                $resp[$resource] = $this->db->get($resource, $query);
            }
        }

        return $resp;

    }
 
}
 