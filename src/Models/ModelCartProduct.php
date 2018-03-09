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
use Pllano\Core\Models\{
    ModelProduct, 
    ModelUserData, 
    ModelSite
};

class ModelCartProduct extends Model implements ModelInterface
{
    
    private $cart_product = [];
    
    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->cart_product = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'cart_product';
        $this->_idField = 'id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
    }
    
    public function getList($filters = null, $joinTables = null, $orderBy = null, $count = null, $offset = null)
    {
        $this->data = parent::getList($filters, $joinTables, $orderBy, $count, $offset);
        foreach($this->data as &$one)
        {
            $this->_data = ModelCartProduct::getProduct($one->product_id);
            $one->image = $this->_data->image;
            $one->fullname = $this->_data->fullname;
            $one->fullnamecart = $this->_data->fullnamecart;
            $one->discount = $this->_data->discount;
            $one->url = $this->_data->url;
            $one->type = $this->_data->type;
            $one->brand = $this->_data->brand;
            $one->serie = $this->_data->serie;
            $one->brand_id = $this->_data->brand_id;
            $one->serie_id = $this->_data->serie_id;
            $one->type_id = $this->_data->type_id;
            $one->mod_id = $this->_data->mod_id;
            $one->pay_online = $this->_data->pay_online;
            $one->payment_options = $this->_data->payment_options;

            if($this->session->language == 'ru') {
                if($this->_data->type_ru){$one->type = $this->_data->type_ru;}
                if($this->_data->brand_ru){$one->brand = $this->_data->brand_ru;}
                if($this->_data->serie_ru){$one->serie = $this->_data->serie_ru;}
            }
            if($this->session->language == 'ua') {
                if($this->_data->type_ua){$one->type = $this->_data->type_ua;}
                if($this->_data->brand_ua){$one->brand = $this->_data->brand_ua;}
                if($this->_data->serie_ua){$one->serie = $this->_data->serie_ua;}
            }
            if($this->session->language == 'de') {
                if($this->_data->type_de){$one->type = $this->_data->type_de;}
                if($this->_data->brand_de){$one->brand = $this->_data->brand_de;}
                if($this->_data->serie_de){$one->serie = $this->_data->serie_de;}
            }
            if($this->session->language == 'en') {
                if($this->_data->type_en){$one->type = $this->_data->type_en;}
                if($this->_data->brand_en){$one->brand = $this->_data->brand_en;}
                if($this->_data->serie_en){$one->serie = $this->_data->serie_en;}
            }
            $one->articul = $this->_data->articul;
            // коррекция скидочной цены
            if ($one->discount['flag']) {
                $one->price_prediscount = $one->price;
                $one->price = $one->discount['price'];
            }
        }
        return $this->data;
    }
    
    public function getOne($id = null)
    {
        parent::getOne($id);
        $this->_data = ModelCartProduct::getProduct($this->product_id);
        $this->image = $this->_data->image;
        $this->fullname = $this->_data->fullname;
        $this->fullnamecart = $this->_data->fullnamecart;
        $this->discount = $this->_data->discount;
        $this->type = $this->_data->type;
        $this->brand = $this->_data->brand;
        $this->serie = $this->_data->serie;
        $this->articul = $this->_data->articul;
        // коррекция скидочной цены
        if ($this->discount['flag'])
        {
            $this->price_prediscount = $this->price;
            $this->price = $this->discount['price'];
        }
    }
    
    // кладем товар в корзину текущего посетителя
    public function addProductToCart($product_id = null, $cart_id = null, $status)
    {
        if ($product_id === null) die(__METHOD__ . ' - no product_id');
        if ($cart_id === null) die(__METHOD__ . ' - no cart_id');

        $product_id = intval($product_id);
        $cart_id = intval($cart_id);

        $this->id = null; // чистим на всякий случай

        // нужна текущая цена товара
        $product = new ModelProduct($this->app);
        $data = $product->getRow($product_id);

        $userData = new ModelUserData($this->app);
        $this->status_id = ($status == 'wish') ? CART_WISHLIST_PRODUCT : CART_ACTIVE_PRODUCT;

        // $this->user_analytics = $users_Analytics;
        $this->product_id = $product_id;
        $this->brand_id = $data['brand_id'];
        $this->serie_id = $data['serie_id'];
        $this->type_id = $data['type_id'];
        $this->mod_id = $data['mod_id'];
        $this->userdata_id = $userData->id;
        $this->cart_supplier_id = $data['supplier_id'];
        $this->pay_online = $data['pay_online'];
        $this->payment_options = $data['payment_options'];
        $this->cart_id = $cart_id;
        $this->num = 1;
        $this->price = money($data['price']);
        $site = new ModelSite($this->app);
        $this->site->getOne($this->siteId);
        $this->site_id = $this->site->site_id;
        // если есть скидка - корректируем цену
        if ($data['discount']['flag'])
        {
            $this->price_prediscount = $this->price;
            $this->price = $data['discount']['price'];
        }
        return $this->save();
    }
    
    // Выключаем товар в корзине. Он остается в неактивном статусе. Берем по уникальному id
    public function removeProductFromCart($id = null)
    {
        if ($id === null) die(__METHOD__ . ' - no id');
        $this->getOne(intval($id));
        $this->status_id = CART_NOACTIVE_PRODUCT;
        $this->save();
    }

    static function getProduct($product_id=null)
    {

    }

    public function setProductToOrder($order_id)
    {

    }
    
}    