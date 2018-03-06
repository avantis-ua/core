<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core\Interfaces;

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;

interface ControllerInterface
{

    public function __construct(Container $app, string $route = null);

    public function get(Request $request, Response $response, array $args = []);

    public function post(Request $request, Response $response, array $args = []);

}
 