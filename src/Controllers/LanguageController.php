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
use Pllano\Interfaces\ControllerInterface;
use Pllano\Core\Controller;

class LanguageController extends Controller implements ControllerInterface
{

    public function get(Request $request, Response $response, array $args = [])
    {
        $language = $this->languages->get($request);
        $langs = new $this->config['vendor']['detector']['language']();

        if (isset($this->session->language)) {
            $lang = $this->session->language;
        } elseif ($langs->getLanguage()) {
            $lang = $langs->getLanguage();
        } else {
            $lang = $this->config['settings']['language'];
        }

        foreach($language as $key => $value)
        {
            $arr["id"] = $key;
            $arr["name"] = $value;
            $langArr[] = $arr;
        }
        // callback - Даем ответ в виде json о результате
        $callback = [
            'language' => $lang,
            'languages' => $langArr,
            'status' => 200
        ];
        // Выводим заголовки
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');
 
        // Выводим json
        return $response->write(json_encode($callback));
 
    }
    
    public function post(Request $request, Response $response, array $args = [])
    {
        $language = $this->languages->get($request);
        $langs = new $this->config['vendor']['detector']['language']();

        if (isset($this->session->language)) {
            $lang = $this->session->language;
        } elseif ($langs->getLanguage()) {
            $lang = $langs->getLanguage();
        } else {
            $lang = $this->config['settings']['language'];
        }
        // Получаем данные отправленные нам через POST
        $post = $request->getParsedBody();
        $lg = filter_var($post['id'], FILTER_SANITIZE_STRING);
        if ($lg) {
            // Записываем в сессию язык выбранный пользователем
            if ($lg == 1) {$this->session->language = "ru";}
            if ($lg == 2) {$this->session->language = "ua";}
            if ($lg == 3) {$this->session->language = "en";}
            if ($lg == 4) {$this->session->language = "de";}
        }

        foreach($language as $key => $value)
        {
            $arr["id"] = $key;
            $arr["name"] = $value;
            $langArr[] = $arr;
        }
        // callback - Даем ответ в виде json о результате
        $callback = [
            'language' => $this->session->language,
            'languages' => $langArr,
            'status' => 200
        ];
        // Выводим заголовки
        $response->withStatus(200);
        $response->withHeader('Content-type', 'application/json');
 
        // Выводим json
        return $response->write(json_encode($callback));
 
    }
 
}
 