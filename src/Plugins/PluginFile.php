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

class PluginFile implements PluginInterface
{
    public function delete_dir($dir)
    {
       $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delete("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
 