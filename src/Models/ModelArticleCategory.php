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
use Pllano\Core\{Model, Data};

class ModelArticleCategory extends Model implements ModelInterface
{

    private $article_category = [];

    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->article_category = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'site_article_category';
        $this->_idField = 'article_category_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }

    public function getIdByAlias($alias)
    {
        // $db = (new RouterDb($this->config, 'Pdo'))->run('mysql');
        $query = [
            "alias" => $alias,
            "site_id" => $this->siteId
        ];
        $id = null;
        $data = $this->db->get($this->table, $query, $id, $this->field_id);
        return $data['id'] ?? 0;

    }

    public function getArticles($article_category_id, $paginatorOn = 1)
    {

        return $this->data;
    }

}
 