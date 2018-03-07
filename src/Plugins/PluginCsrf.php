<?php /**
	* Pllano Core (https://pllano.com)
	*
	* @link https://github.com/pllano/core
	* @version 1.0.1
	* @copyright Copyright (c) 2017-2018 PLLANO
	* @license http://opensource.org/licenses/MIT (MIT License)
*/
namespace Pllano\Core\Plugins;

use Psr\Http\Message\{ServerRequestInterface as Request, ResponseInterface as Response};
use Psr\Container\ContainerInterface as Container;
use Pllano\Core\Interfaces\PluginInterface;
use Pllano\Core\Models\ModelSecurity;

class PluginCsrf implements PluginInterface
{
    private $config;
	private $session;
	private $core;

    function __construct($core)
    {
        $this->core = $core;
		$this->config = $this->core->get('config');
        $this->session = $this->core->get('session');
	}

    public function check(Request $request, Response $response, array $args = []): bool
    {
        $post = $request->getParsedBody();
		$secure = new ModelSecurity($this->core);
		$token_key = $this->config['key']['token'];
		$crypt = $this->config['vendor']['crypto']['crypt'];
		$check = false; $token = 0; $csrf = 1;
		try {
			// Получаем токен из сессии
			$token = $crypt::decrypt($this->session->token, $token_key);
		} catch (\Exception $ex) {
			$secure->token($request);
			// Сообщение об Атаке или подборе токена
		}
		try {
			// Получаем токен из POST
			$csrf = $crypt::decrypt(sanitize($post['csrf']), $token_key);
		} catch (\Exception $ex) {
		    $secure->csrf($request);
			// Сообщение об Атаке или подборе csrf
		}
		if($token == $csrf) {$check = true;}
		return $check;
	}

}
 
