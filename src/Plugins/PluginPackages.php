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
use Pllano\Core\Plugins\PluginFile;

class PluginPackages implements PluginInterface
{
    private $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    public function get()
    {
        $vendor_dir = $this->config["dir"]["vendor"].'/';
        if (file_exists($vendor_dir."auto_require.json")) {
            return json_decode(file_get_contents($vendor_dir."auto_require.json"), true);
        } else {
            return null;
        }
    }
 
    public function getOne($vendor = null, $package = null)
    {
        $getArr = $this->get();
        if (isset($vendor) && isset($package)) {
            $require = [];
            $require['require'][$vendor.'.'.$package] = $getArr['require'][$vendor.'.'.$package];
            return $require;
        } else {
            return null;
        } 
    }
 
    public function post(array $param = [])
    {
        $getArr = $this->get();
        $count = count($getArr['require']);
        $new_count=$count;
        $newParam['require'] = [];
        if (count($param) >= 1) {
            foreach($param as $param_key => $param_val)
            {
                $i=0;
                foreach($getArr['require'] as $get_key => $get_val)
                {
                    if (strtolower($param_val['name']) == strtolower($get_val['name'])) {
                        $newParam['require'][$get_key] = $param_val;
                    } else {
                        $i+=1;
                    }
                    if($i == $count) {
                        $new_count+=1;
                        $newParam['require'][$new_count] = $param_val;
                    }
                }
            }
        }
        $arr = array_replace_recursive($getArr, $newParam);
        $newArr = json_encode($arr);
        $vendor_dir = $this->config["dir"]["vendor"].'/';
        file_put_contents($vendor_dir."auto_require.json", $newArr);
        return true;
    }
 
    public function put__(array $param = [])
    {
        $getArr = $this->get();
        $count = count($getArr['require']);
        $new_count=$count;
        $newParam['require'] = [];
        $return = false;
        if (count($param) >= 1) {
            foreach($param as $param_key => $param_val)
            {
                foreach($getArr['require'] as $get_key => $get_val)
                {
                    if (strtolower($param_val['name']) == strtolower($get_val['name'])) {
                        $newParam['require'][$get_key] = $param_val;
                        $return = true;
                    } else {
                        $i+=1;
                    }
                    if($i == $count) {
                        $newParam['require'][$param_key] = $param_val;
                        $return = 'new';
                    }
                }
            }
        }
        $arr = array_replace_recursive($getArr, $newParam);
        $newArr = json_encode($arr);
        $vendor_dir = $this->config["dir"]["vendor"].'/';
        file_put_contents($vendor_dir."auto_require.json", $arr);
        return $return;
    }
 
    public function put($param)
    {
        $arr = array_replace_recursive($this->get(), $param);
        $newArr = json_encode($arr);
        $newArr = str_replace('"1"', 1, $newArr);
        $newArr = str_replace('"0"', 0, $newArr);
        $vendor_dir = $this->config["dir"]["vendor"].'/';
        file_put_contents($vendor_dir."auto_require.json", $newArr);
        return true;
    }
    
    // Проверяем разрешен ли этот тип запроса для данного ресурса
    public function test($resource)
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
 
    public function delete_package($dir)
    {
       $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delete("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
 
    public function del($vendor, $package)
    {
        if (isset($vendor) && isset($package)) {
            $getArr = $this->get();
            $key = $vendor.'.'.$package;
            $dir = $getArr[$key]["dir"];
            if (isset($dir)) {
                $directory = $this->config["dir"]["vendor"].'/'.$dir;
                // Подключаем класс
                $file = new PluginFile();
                // Удаляем директорию
                $file->delete_dir($directory);
                // Удалить переменую в массиве
                unset($getArr['require'][$key]);
                $newArr = json_encode($getArr);
                $vendor_dir = $this->config["dir"]["vendor"].'/';
                file_put_contents($vendor_dir."auto_require.json", $newArr);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(array $param = [])
    {
        $getArr = $this->get();
        if (count($param) >= 1) {
            foreach($param as $param_key => $param_val)
            {
                foreach($getArr['require'] as $get_key => $get_val)
                {
                    if (strtolower($param_val['name']) == strtolower($get_val['name'])) {
                        unset($getArr['require'][$get_key]);
                    }
                }
            }
        }
        $newArr = json_encode($getArr);
        $vendor_dir = $this->config["dir"]["vendor"].'/';
        file_put_contents($vendor_dir."auto_require.json", $newArr);
        return true;
    }
 
    public function state($vendor = null, $package = null, $state = null)
    {
        if (isset($vendor) && isset($package) && isset($state)) {
            $getArr = $this->get();
            $key = $vendor.'.'.$package;
            $newParam['require'][$key]['state'] = $state;
            $arr = array_replace_recursive($getArr, $newParam);
            $newArr = json_encode($arr);
            $newArr = str_replace('"1"', 1, $newArr);
            $newArr = str_replace('"0"', 0, $newArr);
            $vendor_dir = $this->config["dir"]["vendor"].'/';
            file_put_contents($vendor_dir."auto_require.json", $newArr);
            return true;
        } else {
            return false;
        }
    }
 
}
 