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
use Pllano\Core\Model;

class ModelSessionUser extends Model implements ModelInterface
{

    /*  
    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->connectContainer();
        $this->connectDatabases();
    }
    */

    // Получаем данные из session
    public function get()
    {

        $this->connectContainer();

        // Определяем язык интерфейса пользователя
        $langs = new $this->config['vendor']['detector']['language']();
        // Получаем массив данных из таблицы language на языке из $this->session->language
        $lang = $this->session->language ?? $langs->getLanguage() ?? $this->config["settings"]["language"] ?? null;
        $this->session->language = $lang;

        if(!isset($this->session->post_id)) {
            $this->session->post_id = random_alias_id();
        }

        if (isset($this->session->authorize)) {
            if ($this->session->authorize == 1) {
                $this->_data['language'] = $lang;
                $this->_data['authorize'] = $this->session->authorize;
                try {
                    $this->_data['role_id'] = $this->session->role_id ?? null;
                    $this->_data['user_id'] = $this->session->user_id ?? null;
                    // Читаем ключи
                    $session_key = $this->config['key']['session'];
                    $crypt = $this->config['vendor']['crypto']['crypt'];
                    if (isset($this->session->iname)) {$this->_data['iname'] = $crypt::decrypt($this->session->iname, $session_key);}
                    if (isset($this->session->fname)) {$this->_data['fname'] = $crypt::decrypt($this->session->fname, $session_key);}
                    if (isset($this->session->phone)) {$this->_data['phone'] = $crypt::decrypt($this->session->phone, $session_key);}
                    if (isset($this->session->email)) {$this->_data['email'] = $crypt::decrypt($this->session->email, $session_key);}
                } catch (\Exception $ex) {
                    // Если не можем расшифровать, чистим сессию
                    $this->session->clear();
                }
                // Возвращаем массив с данными сессии пользователя
                return $this->_data;
            } else {
                $this->_data['language'] = $lang;
                $this->_data['authorize'] = $this->session->authorize;
                return $resp;
            }
        } else {
                $this->_data['language'] = $lang;
                unset($this->_data['authorize']);
                return $this->_data;
        }
    }

}
 