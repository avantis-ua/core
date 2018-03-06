<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core;

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;
use Pllano\Core\Interfaces\ModuleInterface;

class Module extends Model implements ModuleInterface
{

	protected $block;
    protected $route;
    protected $modulKey;
	protected $modulVal;

	public function __construct(Container $app, string $route = null, string $block = null, string $modulKey = null, array $modulVal = [])
    {
		parent::__construct($app);
        $this->block = $block;
        $this->route = $route;
		$this->modulKey = $modulKey;
		$this->modulVal = $modulVal;
    }

    public function get(Request $request){}

	public function post(Request $request){}

}
 