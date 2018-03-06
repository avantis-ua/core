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

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Pllano\Core\Interfaces\ModelInterface;
use Pllano\Core\Model;

class ModelSecurity extends Model implements ModelInterface
{

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->connectContainer();
    }

    // Сообщение об Атаке или подборе токена
    public function token(Request $request)
    {
        // Отправляем сообщение администратору
    }
 
    // Сообщение об Атаке или подборе csrf
    public function csrf(Request $request)
    {
        // Отправляем сообщение администратору
    }

}
 