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

class ModelReviewcategory extends Model implements ModelInterface
{
    public $paginator = null;
    private $currency = [];
    
    public function __construct(Container $app)
    {
        parent::__construct($app);
        $this->currency = new Data([]);
        $this->connectContainer();
        $this->connectDatabases();
        $this->_table = 'site_reviews_category';
        $this->_idField = 'reviews_category_id';
        $this->_adapter = 'Pdo';
        $this->db->setAdapter($this->_adapter);
        $this->connectDatabases();
    }
    
    public function getIdByAlias($alias)
    {

        if($this->data) {
            return $this->data['id'];
        } else {
            return 0;
        }
    }
    
    public function getReviews($reviews_category_id, $paginatorOn = 1)
    {
        $cacheId = 'reviewcategoryGetReview_'.$this->siteId.'_'.$reviews_category_id.'_'.$paginatorOn;
        if ($this->cache->run($cacheId) === null) {

            $this->data = [];

            if ((int)$this->cache->state() == 1) {
                 $this->cache->set($this->data);
            }
        } else {
            $this->data = $this->cache->get();
        }
        return $this->data ?? null;
    }
    
    public function getReviewsAll($reviews_category_id, $paginatorOn = 1)
    {
        $cacheId = 'reviewsCategoryGetReviews_'.$this->siteId.'_'.$reviews_category_id.'_'.$paginatorOn;
        if ($this->cache->run($cacheId) === null) {

            $this->data = [];

            if ((int)$this->cache->state() == 1) {
                 $this->cache->set($this->data);
            }
        } else {
            $this->data = $this->cache->get();
        }
        return $this->data ?? null;
    }
}