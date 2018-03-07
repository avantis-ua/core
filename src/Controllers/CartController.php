<?php /**
    * Pllano Core (https://pllano.com)
    *
    * @link https://github.com/pllano/core
    * @version 1.0.1
    * @copyright Copyright (c) 2017-2018 PLLANO
    * @license http://opensource.org/licenses/MIT (MIT License)
*/
namespace Pllano\Core\Controllers;

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;
use Pllano\Core\Interfaces\ControllerInterface;
use Pllano\Core\Controller;
use Pllano\Core\Plugins\PluginCsrf;
use Pllano\Core\Models\{
    ModelInstall, 
    ModelSessionUser, 
    ModelSite, 
    ModelSecurity
};

class CartController extends Controller implements ControllerInterface
{
    
    public function post_add_to_cart(Request $request, Response $response, array $args = [])
    {
        $callbackStatus = 400;
        $callbackTitle = 'Соообщение системы';
        $callbackText = '';
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');
        
        $csrf = new PluginCsrf($this->core);
        if ($csrf->check($request, $response, $args) === true) {
            
            $language = $this->languages->get($request);
            $session_name = $this->config['settings']['session']['name'];
            $cookie_key = $this->config['key']['cookie'];
            $crypt = $this->config['vendor']['crypto']['crypt'];
            // Разбираем post
            $post = $request->getParsedBody();
            $id = sanitize($post['id']) ?? null;
            $product_id = sanitize($post['product_id']) ?? null;
            $price = sanitize($post['price']) ?? null;
            $num = sanitize($post['num']) ?? null;
            $cookie = $crypt::decrypt(get_cookie($session_name), $cookie_key);
            
            if ($this->session->authorize == 1) {
                $user_id = $this->session->user_id;
            } else {
                $user_id = 0;
            }
            
            // Ресурс (таблица) к которому обращаемся
            $resource = "cart";
            // Отдаем роутеру RouterDb конфигурацию
            $routerDb = new RouterDb($this->config, 'Apis');
            // Пингуем для ресурса указанную и доступную базу данных
            // Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
            $db = $routerDb->run($routerDb->ping($resource));
            // Массив для запроса
            $query = [
            'user_id' => $user_id,
            'cookie' => $cookie,
            'product_id' => $product_id,
            'num' => $num,
            'price' => $price,
            'currency_id' => $this->config['seller']['currency_id'],
            'order_id' => null,
            'status_id' => 1,
            'state' => 1
            ];
            // Отправляем запрос к БД в формате адаптера. В этом случае Apis
            $responseArr = $db->post($resource, $query);
            
            if ($responseArr >= 1) {
                $callbackStatus = 200;
                $callbackTitle = $language["23"];
                $callbackText = $language["126"]." ".$language["124"]."<br>".$language["194"]." ".$price;
            } else {
                $callbackText = 'Действие заблокировано';
            }
            
        } else {
            $callbackTitle = "Ошибка";
            $callbackText = 'Действие заблокировано';
        }
        
        $callback = ['status' => $callbackStatus, 'title' => $callbackTitle, 'text' => $callbackText];
        // Выводим заголовки
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');
        // Выводим json
        return $response->write(json_encode($callback));
        
    }
    
    public function post_new_order(Request $request, Response $response, array $args = [])
    {
        $callbackStatus = 400;
        $callbackTitle = 'Соообщение системы';
        $callbackText = '';
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');
        
        $csrf = new PluginCsrf($this->core);
        if ($csrf->check($request, $response, $args) === true) {
            
            // Разбираем post
            $post = $request->getParsedBody();
            
            $language = $this->languages->get($request);
            $session_name = $this->config['settings']['session']['name'];
            // Читаем ключи
            $session_key = $this->config['key']['session'];
            $cookie_key = $this->config['key']['cookie'];
            $crypt = $this->config['vendor']['crypto']['crypt'];
            
            
            
            $id = sanitize($post['id']);
            $iname = sanitize($post['iname']);
            $fname = sanitize($post['fname']);
            $phone = sanitize($post['phone']);
            $email = sanitize($post['email']);
            $city_name = sanitize($post['city_name']);
            $street = sanitize($post['street']);
            $build = sanitize($post['build']);
            $apart = sanitize($post['apart']);
            $product_id = sanitize($post['product_id']);
            $price = sanitize($post['price']);
            $num = sanitize($post['num']);
            $description = sanitize($post['description']);
            
            $cookie = $crypt::decrypt(get_cookie($session_name), $cookie_key);
            
            if ($this->session->authorize == 1) {
                $user_id = $crypt::decrypt($this->session->user_id, $session_key);
            } else {
                // Ресурс (таблица) к которому обращаемся
                $resource = "user";
                // Отдаем роутеру RouterDb конфигурацию
                $routerDb = new RouterDb($this->config, 'Apis');
                // Пингуем для ресурса указанную и доступную базу данных
                // Подключаемся к БД через выбранный Adapter: Sql, Pdo или Apis (По умолчанию Pdo)
                $db = $routerDb->run($routerDb->ping($resource));
                // Массив для запроса
                $query = [
                "site_id" => 1,
                "cookie" => $cookie,
                "iname" => $iname,
                "fname" => $fname,
                "phone" => $phone,
                "email" => $email,
                "password" => ""
                ];
                // Отправляем запрос к БД в формате адаптера. В этом случае Apis
                $user = $db->post($resource, $query);
                
                if (isset($user['response']['id'])) {
                    $user_id = $user['response']['id'];
                    $this->session->user_id = $crypt::encrypt($user_id, $session_key);
                }
            }
            
            if ($user_id >= 1) {
                
                $addressArr = [
                "table_name" => "user",
                "user_id" => $user_id,
                "city_id" => 1,
                "street_id" => 2,
                "number" => $build,
                "apartment" => $apart
                ];
                
                // Ресурс (таблица) к которому обращаемся
                $resource = "address";
                // Отдаем роутеру RouterDb конфигурацию.
                $router = new Router($this->config);
                // Получаем название базы для указанного ресурса
                $name_db = $router->ping($resource);
                // Подключаемся к базе
                $db = new Db($name_db, $this->config);
                // Отправляем запрос и получаем данные
                $address = $db->post($resource, $addressArr);
                
                if ($address >= 1) {
                    
                    $orderArr = [
                    "site_id" => 1,
                    "order_type" => 1,
                    "user_id" => $user_id,
                    "status_id" => 1,
                    "delivery_id" => 1,
                    "address_id" => $address,
                    "note" => $description
                    ];
                    
                    // Ресурс (таблица) к которому обращаемся
                    $resource = "order";
                    // Отдаем роутеру RouterDb конфигурацию.
                    $router = new Router($this->config);
                    // Получаем название базы для указанного ресурса
                    $name_db = $router->ping($resource);
                    // Подключаемся к базе
                    $db = new Db($name_db, $this->config);
                    // Отправляем запрос и получаем данные
                    $order = $db->post($resource, $orderArr);
                    
                    if ($order >= 1) {
                        
                        $cartArr = [
                        'user_id' => $user_id,
                        'cookie' => $cookie,
                        'product_id' => $product_id,
                        'order_id' => $order,
                        'num' => $num,
                        'price' => $price,
                        'currency_id' => $this->config['settings']['site']['currency_id'],
                        'status_id' => 1,
                        'state' => 1
                        ];
                        
                        // Ресурс (таблица) к которому обращаемся
                        $resource = "cart";
                        // Отдаем роутеру RouterDb конфигурацию.
                        $router = new Router($this->config);
                        // Получаем название базы для указанного ресурса
                        $name_db = $router->ping($resource);
                        // Подключаемся к базе
                        $db = new Db($name_db, $this->config);
                        // Отправляем запрос и получаем данные
                        $cart = $db->post($resource, $cartArr);
                        
                        if ($cart >= 1) {
                            $callbackStatus = 200;
                            $callbackTitle = 'Спасибо за заказ';
                            $callbackText = '<div class="text-center">Копию заказа мы отправили вам на почту.</div>';
                        }
                    } else {
                        $callbackText = 'Ошибка !';
                    }
                } else {
                    $callbackText = 'Ошибка !';
                }
            } else {
                $callbackText = 'Ошибка !';
            }
        } else {
            $callbackTitle = "Ошибка";
            $callbackText = 'Действие заблокировано';
            $response->withStatus(403);
        }
        
        $callback = ['status' => $callbackStatus, 'title' => $callbackTitle, 'text' => $callbackText];
        // Выводим json
        return $response->write(json_encode($callback));
    }
    
}
 