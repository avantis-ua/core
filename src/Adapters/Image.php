<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core\Adapters;

use Pllano\Core\Adapter;
use Pllano\Interfaces\AdapterInterface;

class Image extends Adapter implements AdapterInterface
{

    protected $width = null;
    protected $height = null;
    protected $mode = null;
    protected $basename = null;
    protected $dir_size = null;
    protected $dir = null;
    protected $temp = null;
    protected $subdir = null;
    protected $image = null;
    protected $image_path = null;
    protected $image_temp = null;

    // Функция контроля изображений если есть отдает локальный url, если нет загружает с платформы
    public function get($id = null, $url = null, $width = null, $height = null, $dir = null)
    {
        if(isset($width)) {
            $this->width = $width;
        }
        if(isset($height)) {
            $this->height = $height;
        }
        if(isset($id)) {
            $this->subdir($id);
        }
 
        if(isset($dir)) {
            $this->dir = $dir;
        } else {
            $this->dir = 'images/';
        }
        $this->temp = $this->dir.'temp/';
        
        if(isset($this->width) && isset($this->height)) {
            $this->dir_size($this->width.'x'.$this->height);
        }
 
        $this->run_dir();
 
        if(isset($url)) {
            $images_url = filter_var($url, FILTER_VALIDATE_URL);
            if ($images_url == true) {
                $path_image = pathinfo($url);
            }
        }
        if (isset($path_image["extension"])) {
            if ($path_image["extension"] == 'jpg' || $path_image["extension"] == 'png' || $path_image["extension"] == 'jpeg') {
                if (isset($path_image["basename"])) {
 
                    $this->basename = $path_image["basename"];
                    // Папка для загрузки картинок
                    $this->image_temp = $this->temp.''.$this->basename;
                    // Формируем название картинки
                    $this->image = $this->dir.''.$this->dir_size.'/'.$this->subdir.'/'.$this->basename;
 
                    if (!file_exists($this->image)) {
 
                            if (!file_exists($this->image_temp) && (@fopen($url,"r")==true)) {
                                file_put_contents($this->image_temp, file_get_contents($url));
                            }
                            if (file_exists($this->image_temp)) {
 
                                // Генерируем миниатюру изображения
                                $thumbnail = $this->thumbnail();
 
                                if ($thumbnail == true) {
 
                                    unlink($this->image_temp);
 
                                    if (file_exists($this->image)) {
 
                                        if($this->config['vendor']['image']['optimize'] != '') {
 
                                            // Оптимизируем изображение
                                            $this->optimize();
 
                                        }
                                        return $this->image;
 
                                    } else {
                                        return null;
                                    }
                                } else {
                                    return null;
                                }
                            } else {
                                return null;
                            }
                    } else {
                    
                        $this->optimize();
                        //print("<br>optimize<br>");
 
                        return $this->image;
 
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
 
    public function optimize($open = null, $save = null)
    {
         try {
            if(isset($this->config['vendor']['image']['optimize'])) {
                $vendor = null;
                if (class_exists($this->config['vendor']['image']['optimize'])) {
                    $vendor = $this->config['vendor']['image']['optimize'];
                } else {
                    print("<br>Class {$this->config['vendor']['image']['optimize']} not found<br>");
                    return null;
                }
                if(isset($vendor)) {
                    if($vendor == '\Spatie\ImageOptimizer\OptimizerChainFactory') {
                        $optimizer = \Spatie\ImageOptimizer\OptimizerChainFactory::create();
                        $optimizer->optimize($this->image);
                    } elseif ($vendor == '\ImageOptimizer\OptimizerFactory') {
                        
                        $factory = new \ImageOptimizer\OptimizerFactory();
                        $optimizer = $factory->get();
                        //print("<br>OptimizerFactory");
                        
                    } else {
                        $optimizer = $vendor();
                    }
                    if (isset($this->image)) {
                        $optimizer->optimize($this->image);
                        //print(" - OK<br>");
                    } elseif(isset($open) && isset($save)) {
                        $optimizer->optimize($open, $save);
                    } else {
                        print("<br>optimizer image - NO<br>");
                        return null;
                    }
                    return true;
                } else {
                    print("<br>optimizer vendor - NO<br>");
                    return null;
                }
            } else {
                return null;
            }
        } catch (\Exception $e) {
            print("<br>Exception ERROR! Class {$this->config['vendor']['image']['optimize']}<br>");
            return null;
        }
    }
 
    public function run_dir()
    {
        if (!file_exists($this->dir)) {mkdir($this->dir);}
        if (!file_exists($this->dir.''.$this->dir_size.'/')) {mkdir($this->dir.''.$this->dir_size.'/');}
        if (!file_exists($this->dir.''.$this->dir_size.'/'.$this->subdir.'/')) {mkdir($this->dir.''.$this->dir_size.'/'.$this->subdir.'/');}
        if (!file_exists($this->temp)) {mkdir($this->temp);}
    }
    
    public function dir($dir = null)
    {
        if(isset($dir)) {
            $this->dir = $dir;
        }
    }
    
    public function subdir($id = null)
    {
        $this->subdir = 'default';
        if ($id >= 1) {
            $this->subdir = ($id - ($id % 1000)) / 1000;
        }
        return $this->subdir;
    }
    
    public function dir_size($dir = null)
    {
        if(isset($this->width) && isset($this->height)) {
            $this->dir_size = $this->width.'x'.$this->height;
            return $this->dir_size;
        } elseif (isset($dir)) {
            $this->dir_size = $dir;
            return $this->dir_size;
        } else {
            return null;
        }
    }
 
    public function open($image_temp = null)
    {
        if(isset($image_temp)) {
            $this->image_temp = $image_temp;
        }
    }
 
    public function make($image_temp = null)
    {
        if(isset($image_temp)) {
            $this->image_temp = $image_temp;
        }
    }
 
    public function load($image_temp = null)
    {
        if(isset($image_temp)) {
            $this->image_temp = $image_temp;
        }
    }
 
    public function width($width = null)
    {
        if(isset($width)) {
            $this->width = $width;
        }
    }
 
    public function height($height = null)
    {
        if(isset($height)) {
            $this->height = $height;
        }
    }
 
    public function resize($width = null, $height = null)
    {
        if(isset($width)) {
            $this->width = $width;
        }
        if(isset($height)) {
            $this->height = $height;
        }
    }
 
    public function save($image = null)
    {
        if(isset($image)) {
            $this->image = $image;
        }
        if (isset($this->image) && isset($this->image_temp) && isset($this->height) && isset($this->width)) {
            $this->thumbnail();
        }
    }
 
    public function thumbnail($width = null, $height = null)
    {
        if(isset($width)) {
            $this->width = $width;
        }
        if(isset($height)) {
            $this->height = $height;
        }
 
        try {
            if(isset($this->config['vendor']['image']['thumbnail']) && $this->config['vendor']['image']['thumbnail'] != '') {
                $vendor = null;
                if (class_exists($this->config['vendor']['image']['thumbnail'])) {
                    $vendor = $this->config['vendor']['image']['thumbnail'];
                } else {
                    return null;
                }
                if(isset($vendor)) {
                    if($vendor == '\Imagine\Gd\Imagine') {
                        // \Imagine\Gd\Imagine
                        // \Imagine\Imagick\Imagine
                        // \Imagine\Gmagick\Imagine
                        $images = new $vendor();
                        $size = new \Imagine\Image\Box($this->width, $this->height);
                        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                        if($this->config['vendor']['image']['thumbnail_mode'] == 'THUMBNAIL_OUTBOUND') {
                            $mode = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                        }
                        $images->open($this->image_temp)->thumbnail($size, $mode)->save($this->image);
 
                    } elseif ($vendor == '\Intervention\Image\ImageManager') {
                        // \Intervention\Image\ImageManager
                        $images = new \Intervention\Image\ImageManager(['driver' => $this->mode]);
                        $images->make($this->image_temp)->resize($this->width, $this->height)->save($this->image, 60);
 
                    } elseif ($vendor == '\Spatie\Image') {
                        // \Spatie\Image
                        $images = \Spatie\Image::load($this->image_temp);
                        $images->width($this->width);
                        $images->height($this->height);
                        $images->save($this->image);
 
                    } else {
 
                            $this->mode = $this->config['vendor']['image']['thumbnail_mode'];
 
                            if($this->mode == 'default') {
                                $images = new $vendor($this->mode);
                            } elseif ($this->mode == 'none') {
                                $images = new $vendor();
                            } elseif ($this->mode == 'path') {
                                $images = new $vendor($this->image_temp);
                            } else {
                                $images = new $vendor();
                            }
 
                            if(method_exists($images,'open')) {
                                $images->open($this->image_temp);
                            } elseif(method_exists($images,'make')) {
                                $images->make($this->image_temp);
                            } elseif(method_exists($images,'get')) {
                                $images->get($this->image_temp);
                            } elseif(method_exists($images,'load')) {
                                $images->load($this->image_temp);
                            } else {
                                return null;
                            }
 
                            if(method_exists($images,'resize')) {
                                $images->resize($this->width, $this->height);
                            } elseif (method_exists($images,'thumbnail')) {
                                $images->thumbnail($this->width, $this->height);
                            } elseif (method_exists($images,'width') && method_exists($images,'width')) {
                                $images->width = $this->width;
                                $images->height = $this->height;
                            } else {
                                return null;
                            }
 
                            if(method_exists($images,'save')) {
                                $images->save($this->image);
                            } elseif(method_exists($images,'run')) {
                                $images->run($this->image);
                            } else {
                                return null;
                            }
 
                        }
 
                        return true;
 
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
 
}
 