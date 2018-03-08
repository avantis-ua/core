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

class ModelCart extends Model implements ModelInterface
{

    const MODE_HOME = 2;
    const MODE_DEFAULT = 2;
    const MODE_PRODUCTS_ALL = 1;
    const MODE_PRODUCTS_CART = 2;
    const MODE_PRODUCTS_WISHLIST = 3;
    const MODE_ORDERS_ALL = 4;
    const MODE_ORDERS_CART = 5;
    const MODE_ORDERS_CRM = 6;
    const MODE_ORDERS_CLOSED = 7;
    const MODE_REGISTRATION = 11;
    const MODE_ADDRESSING = 12;
    const MODE_PAYING = 13;
    const MODE_ORDERING = 14;
    const MODE_FINALE = 15;
    const MODE_AUTHORIZE = 21;
    const MODE_ADDRESSINGEDIT = 98;
    const MODE_ADDRESSINGSAVE = 99;

    private $cart = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->cart = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'cart';
        $this->_idField = 'id';
        
        $this->id = $this->session->cart_id ?? null;

    }

    public function hasProducts()
    {
        
    }

    public function shortContentlist()
    {

    }

    public function hasActiveProducts()
    {

    }

    public function hasWishlistProducts()
    {

    }

    public function hasOrderedProducts()
    {

    }

    public function isProductAtCart($product_id=null)
    {

    }

    public function shortContent()
    {

        
    }

    public function wishlistContent()
    {

    }

    public function orderedContent()
    {

    }

    public function shortContentoo()
    {

    }
    
    public function wishlistContentoo()
    {

    }
    
    public function orderedContentoo()
    {

    }

    public function shortContentbottomdiv()
    {

    }
    
    public function wishlistContentbottomdiv()
    {

    }
    
    public function orderedContentbottomdiv()
    {

    }

    public function fullContent($cartMode)
    {

    }

    public function authorizeContent()
    {

    }

    public function addressingContent()
    {

    }
    
    public function addressingeditContent()
    {

    }
    
    public function addressingsaveContent()
    {

    }
    
    public function payingContent()
    {

    }
    
    public function orderingContent()
    {

    }
    
    public function finaleContent()
    {

    }
    
}
 