<?php /**
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
use Pllano\Interfaces\ControllerInterface;
use Pllano\Core\Controller;
use Pllano\Core\Plugins\PluginCsrf;
use Pllano\Core\Models\ModelArticlecategory;

class ArticlecategoryController extends Controller implements ControllerInterface
{
    
    public function index(Request $request, Response $response, array $args = [])
    {
        $articlecategory = new ModelArticlecategory($this->app);
        $id = (int)$request->getAttribute('id') ?? null;
        $alias = $request->getAttribute('id') ?? '';
        if ($alias) {
            $id = $articlecategory->getIdByAlias($alias);
            if (!$id) {
                $response->withStatus(404);
                $this->data->referer = $request->getReferrer();
                $this->data->article = [];
                $this->render = $this->site->seo_404.'.phtml';
                $this->error->update404();
            }
        }
        if ($alias && $id) {

            $articlecategory_folder = $this->site->articlecategory_folder;
            $articlecategory_index = $this->site->articlecategory_index;
            $pagination_control = $this->site->pagination_control;
            
            $cacheId = 'articlecategory_folder_'.$this->site->site_id.'_'.$this->site->alias.'_'.$id;
            if (!$this->cache->test($cacheId)) {
                $articlecategory->getOne($id);
                $this->cache->save($articlecategory, $cacheId);
            } else {
                $articlecategory = $this->cache->load($cacheId);
            }
            $this->data->articlecategory = $articlecategory;
            
            $articlecategory->getArticles($id);
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            //Zend_View_Helper_PaginationControl::setDefaultViewPartial('controls.phtml');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial($pagination_control.'.phtml');
            if ($articlecategory->paginator) {
                $articlecategory->paginator->setCurrentPageNumber($this->_getParam('page', 1));
                $articlecategory->paginator->setItemCountPerPage(10);
            }
            $this->data->paginator = $articlecategory->paginator;

            $this->render = $articlecategory_folder.'/'.$articlecategory_index.'.phtml';
        }

        return $response->write($this->viev->render($this->render, $this->data->article));
    }
}
 