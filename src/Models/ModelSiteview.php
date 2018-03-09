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

class ModelSiteview extends Model implements ModelInterface
{
    
    private $site = [];
    
    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->site = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'site_view';
        $this->_idField = 'view_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }
    
    
    public function getInfo()
    {
        // Анализируем. Если это страница товара - записываем в таблицу
        // Если в предыдущих есть такой же - игнорируем
        $_url = $this->session->productCurrentUrl;
        $_defis = strrpos($_url, '-');
        // это не страница товара - уходим
        if($_defis === false) return;
        $_dot = strpos($_url, '.');
        $product_id = substr($_url, $_defis + 1, $_dot - 1 - $_defis);
        $_cookie = $this->session->code ?? null;
        // Это паук - уходим.
        if ($_cookie === null OR strlen($_cookie) < 13) return;

        $_info = [
        'ip' => $_SERVER['REMOTE_ADDR'],
        'cookie' => $_cookie,
        'site_id' => $this->siteId,
        'product_id' => $product_id
        ];
        
        // Берем самые свежие
        $writeFlag = true;
        $query = "SELECT site_id, product_id FROM site_view ORDER BY created DESC LIMIT 20";
        $rows = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        // Если среди них есть такой товар - не пишем
        foreach($rows AS $row)
        {
            if($row['site_id'] == $_info['site_id'] AND $row['product_id'] == $_info['product_id']) {$writeFlag = false; break;}
        }
        if ($writeFlag === true) {
            $this->fromArray($_info);
            if($this->product_id > 0) $this->save();
        }
    }
}
 