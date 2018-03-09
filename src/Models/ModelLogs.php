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
use Pllano\Core\Models\ModelUserData;

class ModelLogs extends Model implements ModelInterface
{
    private $logs = [];
    
    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->logs = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'cart_userdata';
        $this->_idField = 'id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }
    
    public function TokenLog($controller, $action, $log_url = null, $info = null)
    {
        $site_id = $this->site->site_id;
        $userData = new ModelUserData($this->app);
        $user_id = '';
        $user_id = $userData->id;
        $user_rate = '';
        $user_rate = $userData->rating;
        $user_role = $userData->user_role;
        $moderation = 0;
        if ($user_rate >= 1) {$moderation = 1;}
        $random_alias = RandomToken();
        
    }
    
    // Подключение 
    // $logs = new ModelLogs();
    // $logs->TokenLog('MarketplacePriceLoaderController', 'priceLoaderAction');
    
    //return false;
    
    public function CronLog($action, $text = '')
    {
        $site_id = $this->site->site_id;
        $random_alias = RandomToken();
        $userData = new ModelUserData($this->app);
        $user_id = '';
        $user_id = $userData->id;
        $user_rate = '';
        $user_rate = $userData->rating;
        $user_role = $userData->user_role;

        // Подключение 
        // $logs = new ModelLogs();
        // $logs->CronLog(''.$action.'', 'Текст описывающий проблему');
        
    }
    
    public function UserLog($controller, $action, $model, $table_name = '', $field, $field_id, $text = '', $supplier_id = '', $supplier_name = '', $seller_id = '', $seller_name = '')
    {
        $site_id = $this->site->site_id;
        $userData = new ModelUserData($this->app);
        $user_id = '';
        $user_id = $userData->id;
        $user_rate = '';
        $user_rate = $userData->rating;
        $user_role = $userData->user_role;
        $moderation = 0;
        if ($user_rate >= 1) {$moderation = 1;}
        $random_alias = RandomToken();
        
        // Подключение 
        // $logs = new ModelLogs();
        // $logs->UserLog($controller, $action, $model, $table_name, $field, $field_id, $text = '', $supplier_id = '', $supplier_name = '', $seller_id = '', $seller_name = '');
        //return false;
    }
    
}