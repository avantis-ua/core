<?php 
/**
 * Pllano Core (https://pllano.com)
 *
 * @link https://github.com/pllano/core
 * @version 1.0.1
 * @copyright Copyright (c) 2017-2018 PLLANO
 * @license http://opensource.org/licenses/MIT (MIT License)
 */
namespace Pllano\Core\Managers;

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;
use Pllano\Core\Interfaces\ControllerInterface;
use Pllano\Core\Controller;
use Pllano\Core\Plugins\PluginCsrf;
use Pllano\Core\Models\{
    ModelInstall, 
    ModelSessionUser, 
    ModelSite
};

class Controllers extends Controller implements ControllerInterface
{

    public function get(Request $request, Response $response, array $args = [])
    {
        $time_start = microtime_float();
        $this->query = $request->getMethod();

        // Передаем данные Hooks для обработки ожидающим классам
        // Default Pllano\Hooks\Hook
        $hook = new $this->config['vendor']['hooks']['hook']($this->config, $this->query, $this->route, 'site');
        $hook->http($request);
        $request = $hook->request();
        // true - Если все хуки отказались подменять контент
        if($hook->state() === true) {

            $host = $request->getUri()->getHost();
            $path = '';
            if($request->getUri()->getPath() != '/') {$path = $request->getUri()->getPath();}
            $params = '';
            // Параметры из URL
            $params_query = str_replace('q=/', '', $request->getUri()->getQuery());
            if ($params_query) {$params = '/'.$params_query;}
            // Данные пользователя из сессии
            $sessionUser =(new ModelSessionUser($this->app))->get();
            // Генерируем токен. Читаем ключ. Записываем токен в сессию.
            // Default Defuse\Crypto\Crypto
            $crypt = $this->config['vendor']['crypto']['crypt'];
            $this->session->token = $crypt::encrypt(random_token(), $this->config['key']['token']);
            $language = $this->languages->get($request);
            $lang = $this->languages->lang();
            // Настройки сайта
            $site = new ModelSite($this->app);
            $siteConfig = $site->get();
            // Конфигурация роутинга
            $routers = $this->config['routers'];
            $admin_uri = '/_'; if(!empty($this->session->admin_uri)) {$admin_uri = '/'.$this->session->admin_uri;}
            $post_id = '/_'; if(!empty($this->session->post_id)) {$post_id = '/'.$this->session->post_id;}
            // Заголовки по умолчанию из конфигурации
            $headArr = explode(',', str_replace([" ", "'"], "", $this->config['settings']['seo']['head']));
            $head = ["page" => $this->route, "host" => $host, "path" => $path, "scheme" => $this->config["server"]["scheme"].'://'];
            foreach($headArr as $headKey => $headVal)
            {
                $head_arr[$headVal] = $this->config['settings']['site'][$headVal];
                $head = array_replace_recursive($head, $head_arr);
            }

            if ($this->config["settings"]["install"]["status"] != null) {

                $pluginsArr = [];
                $dataArr = [];
                $arr = [];
                if ($this->cache->run($host.''.$params.'/'.$lang.'/'.$this->route) === null) {
                    $dataArr = [
                        "head" => $head,
                        "routers" => $routers,
                        "site" => $siteConfig,
                        "config" => $this->config['settings']['site'],
                        "template" => $this->template
                    ];
                    $mods = explode(',', str_replace([" ", "'"], "", $this->config['routers']['site'][$this->route]['blocks']));
                    foreach($mods as $key => $block)
                    {
                        $modules = new $this->config['vendor']['modules']['manager']($this->app, $this->route, $block);
                        $arr = $modules->get($request);
                        $dataArr = array_replace_recursive($dataArr, $arr);
                    }
                    if ((int)$this->cache->state() == 1) {
                        $this->cache->set($dataArr);
                    }
                } else {
                    $dataArr = $this->cache->get();
                }

                // Модули могут поменять layout
                $this->render = $dataArr['content']['modules'][$this->route]['content']['layout'] ?? $this->template['layouts']['layout'];

                // Массив данных который нельзя кешировать
                $userArr = [
                    "language" => $language,
                    "token" => $this->session->token,
                    "post_id" => $post_id,
                    "admin_uri" => $admin_uri,
                    "session" => $sessionUser
                ];

                // Формируем данные для шаблонизатора. Склеиваем два массива.
                $this->data = array_replace_recursive($userArr, $dataArr);
            } else {

                $sessionTemp = new $this->config['vendor']['session']['session']("_temp");
                $this->render = "index.html";
                // Если ключа доступа у нет, значит сайт еще не активирован
                $content = '';
                if (isset($this->session->install)) {
                    if ($this->session->install == 1) {
                        $this->render = "stores.html";
                        $content = (new ModelInstall($this->app))->stores_list();
                    } elseif ($this->session->install == 2) {
                        $this->render = "templates.html";
                        $install_store = $this->session->install_store ?? null;
                        $content = (new ModelInstall($this->app))->templates_list($install_store);
                    } elseif ($this->session->install == 3) {
                        $this->render = "welcome.html";
                    } elseif ($this->session->install == 10) {
                        $this->render = "templates.html";
                        $content = (new ModelInstall($this->app))->templates_list(null);
                    } elseif ($this->session->install == 11) {
                        $this->render = "key.html";
                    }
                }

                $this->data = [
                    "head" => $head,
                    "template" => "install",
                    "routers" => $routers,
                    "config" => $this->config['settings']['site'],
                    "language" => $language,
                    "token" => $this->session->token,
                    "post_id" => $post_id,
                    "session" => $sessionUser,
                    "session_temp" => $sessionTemp,
                    "content" => $content
                ];
            }

            // Передаем данные Hooks для обработки ожидающим классам
            $hook->get($this->render, $this->data);
        }

        $time = number_format(microtime_float() - $this->time_start, 4);
        $time_get_start = number_format(microtime_float() - $time_start, 4);
        if ($time >= 2) {
            // Запись в лог
            $this->logger->info("time", [
                "source" => "ControllerManager",
                "getMethod" => $request->getMethod(),
                "time" => $time,
                "time_start" => $this->time_start,
                "ControllerManagerStart" => $time_start,
                "uri" => escaped_url()
            ]);
        }

        if (!isset($this->data['content'])) {
            $response->withStatus(404);
        } else {
            $response->withStatus(200);
        }

        if ($this->config['settings']["install"]["status"] != null) {
            return $response->write($this->view->render($hook->render(), $hook->view()));
        } else {
            return $response->write($this->view->render($this->render, $this->data));
        }

        // $getScheme            = $request->getUri()->getScheme(); // Работает
        // $getQuery            = $request->getUri()->getQuery(); // Работает
        // $getHost                = $request->getUri()->getHost(); // Работает
        // $getPath                = $request->getUri()->getPath(); // Работает
        // $getParams            = $request->getQueryParams(); // Работает
        // $getMethod            = $request->getMethod();
        // $getParsedBody        = $request->getParsedBody();
    }
 
    public function post(Request $request, Response $response, array $args = [])
    {
        $time_start = microtime_float();

        $callbackStatus = 400;
        $callbackTitle = 'Соообщение системы';
        $callbackText = '';
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');

        $csrf = new PluginCsrf($this->core);
        if ($csrf->check($request, $response, $args) === true) {

            $mods = explode(',', str_replace([" ", "'"], "", $this->config['routers']['site'][$this->route]['blocks']));
            foreach($mods as $key => $block)
            {
                $modules = new $this->config['vendor']['modules']['manager']($this->app, $this->route, $block);
                $callback = $modules->post($request);
            }
        } else {
            $callbackTitle = "Ошибка";
            $callbackText = 'Перегрузите страницу';
            $response->withStatus(403);
            $callback = ['status' => $callbackStatus, 'title' => $callbackTitle, 'text' => $callbackText];
        }

        $time = number_format(microtime_float() - $this->time_start, 4);
        $time_get_start = number_format(microtime_float() - $time_start, 4);
        if ($time >= 10) {
            // Запись в лог
            $this->logger->info("time", [
                "source" => "ControllerManager",
                "getMethod" => $request->getMethod(),
                "time" => $time,
                "time_start" => $this->time_start,
                "ControllerManagerStart" => $time_start,
                "uri" => escaped_url()
            ]);
        }

        return $response->write(json_encode($callback));
        
    }

    public function getTest(Request $request, Response $response, array $args)
    {
        $view = '';
        $host = $request->getUri()->getHost();
        $path = '';
        if($request->getUri()->getPath() != '/') {
            $path = $request->getUri()->getPath();
        }
        $params = '';
        // getQuery
        $params_query = str_replace('q=/', '', $request->getUri()->getQuery());
        if ($params_query) {
            $params = '/'.$params_query;
        }

        $getParams = $request->getQueryParams();
        // $getQuery = $request->getUri()->getQuery();

        // $getParsedBody = $request->getParsedBody();
        // $getParams = $request->getQueryParams();
        // $getScheme = $request->getUri()->getScheme();
        // $getHost = $request->getUri()->getHost();
        // $getPath = $request->getUri()->getPath();
        // $getMethod = $request->getMethod();

        $data = [];

        $h2 = $request->getAttribute('route') ?? '«Hello, world!»';

        // Models Directory /vendor/app/Models/
        // AutoRequire\Autoloader - Automatically registers a namespace \App in /vendor/app/

        $lang = 'en';
        // $language = new \App\Models\ModelLanguage();
        // $lang = $language->get();

        // $route = ucfirst($request->getAttribute('route')) ?? 'Error';
        // $model = new \App\Models\Model'.$route($this->config, $this->package, $this->logger);
        // or
        // $model = new \App\Models\ModelStart($this->config, $this->package, $this->logger);

        // $data = $model->get($request, $response, $args);

        if ($this->cache->run($host.'/'.$path.'/'.$params.'/'.$lang) === null) {
 
            $data = [
                "h1" => "Slim 4 Skeleton",
                "h2" => "Slim + {$h2}",
                "title" => "Slim 4 Skeleton",
                "description" => "a microframework for PHP",
                "robots" => "index, follow",
                "render" => "index.phtml",
                "caching" => $this->config['cache']['driver'],
                "caching_state" => $this->config['cache']['state'],
                "cache_lifetime" => $this->config['cache']['cache_lifetime']
            ];
            $data['h3'] = $request->getAttribute('resource') ?? null;
            $data['id'] = $request->getAttribute('id') ?? null;
            
            if ((int)$this->cache->state() == 1) {
                $this->cache->set($data);
            }
        } else {
            $data = $this->cache->get();
        }

        // Render view
        $render = $data['render'] ?? 'index.phtml';
        
        $view = $this->view->render($render, $data);

        return $view;

    }

    public function runApiTest(Request $request, Response $response, array $args)
    {
        $callback = [];
        $function = strtolower($request->getMethod());
        // Models Directory /vendor/app/Models/
        // AutoRequire\Autoloader - Automatically registers a namespace \App in /vendor/app/
        $model = new \Core\Models\ModelApi($this->app);
        $callback = $model->$function($request, $response, $args);
        // return json_encode($callback, JSON_PRETTY_PRINT);
        return $callback;
    }

    public function postTest(Request $request, Response $response, array $args)
    {
        $callbackStatus = 400;
        $callbackTitle = 'Соообщение системы';
        $callbackText = '';
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');

        $csrf = new PluginCsrf($this->core);
        if ($csrf->check($request, $response, $args) === true) {
            $id = intval(sanitize($post['id']));
            if ($id) {
                $this->session->test_id = $id;
            }
            $callbackStatus = 200;
        } else {
            $callbackTitle = "Ошибка";
            $callbackText = 'Действие заблокировано';
            $response->withStatus(403);
        }
        $callback = ['status' => $callbackStatus, 'title' => $callbackTitle, 'text' => $callbackText];
        return $response->write(json_encode($callback));
    }

}
 