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
use Pllano\Interfaces\ModelInterface;
use Pllano\Core\{Model, Data};

class ModelAreaStreet extends Model implements ModelInterface
{

	private $street = [];

	public function __construct(Container $app)
    {
        parent::__construct($app);
		$this->street = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'area_street';
        $this->_idField = 'street_id';
    }

}
 