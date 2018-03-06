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

class ModelError extends Model implements ModelInterface
{

    public function __construct(Container $app)
    {
        parent::__construct($app);
		// $this->connectContainer();
		//$this->connectDatabases();
    }

    public function get(Request $request, Response $response, array $args)
    {
        $response->withStatus(404);
		return null;
    }

    public function post(Request $request, Response $response, array $args)
    {
        $response->withStatus(404);
		return null;
    }

}
 