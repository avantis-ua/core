<?php /**
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

class ModelAutocomplete extends Model implements ModelInterface
{
    protected $table;
    protected $field_id;
    protected $text;
    protected $arr;

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->arr = new Data([]);
        $this->table = 'product';
        $this->field_id = 'product_id';
    }

    public function getResults(string $text, string $table = null)
    {
        $this->table = $table;
        $this->text = $text;
    }
}
 