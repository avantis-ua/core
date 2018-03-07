<?php 
/**
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
use Pllano\Core\Models\{
    ModelSessionUser, 
    ModelMenu, 
    ModelSite
};

class ErrorController extends Controller implements ControllerInterface
{

    public function post(Request $request, Response $response, array $args)
    {
        $callbackStatus = 200;
        $callbackTitle = 'Сообщение системы';
        $callbackText = 'Ошибка';
        $callback = ['status' => $callbackStatus, 'title' => $callbackTitle, 'text' => $callbackText];
        // Выводим заголовки
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');
        // Выводим json
        return $response->write(json_encode($callback));
    }
    
    public function get(Request $request, Response $response, array $args)
    {
        // Получаем параметры из URL
        $host = $request->getUri()->getHost();
        $path = $request->getUri()->getPath();
        // Конфигурация роутинга
        $routers = $this->config['routers'];
        $language = $this->languages->get($request);
        // Меню, берет название класса из конфигурации
        $menu = (new ModelMenu($this->app))->get();
        // Подключаем сессию
        $session = new $this->config['vendor']['session']['session']($this->config['settings']['session']['name']);
        // Данные пользователя из сессии
        $sessionUser =(new ModelSessionUser($this->config))->get();
        // Читаем ключи
        $token_key = $this->config['key']['token'];
        // Генерируем токен
        $token = random_token();
        // Записываем токен в сессию
        $session->token = $this->config['vendor']['crypto']['crypt']::encrypt($token, $token_key);
        // Контент по умолчанию
        $content = [];

        $post_id = '/_';
        $admin_uri = '/_';
        if(!empty($session->admin_uri)) {
            $admin_uri = '/'.$session->admin_uri;
        }
        if(!empty($session->post_id)) {
            $post_id = '/'.$session->post_id;
        }
        // Настройки сайта
        $site = new ModelSite($this->config);
        $site_config = $site->get();
        // Шаблон по умолчанию 404
        $this->render = $this->template['layouts']['404'] ? $this->template['layouts']['404'] : '404.html';

        // Заголовки по умолчанию из конфигурации
        $headArr = explode(',', str_replace([" ", "'"], "", $this->config['settings']['seo']['head']));
        $head = ["page" => $this->route, "host" => $host, "path" => $path, "scheme" => $this->config["server"]["scheme"].'://'];
        foreach($headArr as $headKey => $headVal)
        {
            $head_arr[$headVal] = $this->config['settings']['site'][$headVal];
            $head = array_replace_recursive($head, $head_arr);
        }

        $this->_data = [
            "head" => $head,
            "routers" => $routers,
            "site" => $site_config,
            "config" => $this->config['settings']['site'],
            "language" => $language,
            "template" => $this->template,
            "token" => $session->token,
            "post_id" => $post_id,
            "admin_uri" => $admin_uri,
            "session" => $sessionUser,
            "menu" => $menu,
            "content" => $content
        ];
        
        $response->withStatus(404);

        return $response->write($this->view->render($response, $this->render, $this->_data));
    }
    
}
 