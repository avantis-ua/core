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

class ModelCartInterface
{

    static public function bottomButtonsBlock($cartMode)
    {

    }

    static public function sectionTitle($cartMode)
    {

    }

    static public function headerRow($submode)
    {

    }

    static public function finalInfoRow($productData)
    {

    }
    
    static public function finalTableRow($productData)
    {

    }
    
    static public function tableRow($productData)
    {

    }

    static public function totalRow($totalSum)
    {

    }
    
    static public function finalTotalRow($totalSum, $productData)
    {

    }
    
    static public function finalButtonsRow()
    {

    }

    static public function finalBigTableBlock($theadBlock, $tbodyBlock)
    {

    }

    static public function numBlockRow($productData)
    {

    }

    static public function statusSelectorBlock($productData)
    {

    }

    static public function newstatusSelectorBlock($productData)
    {

    }

    static public function bigTableBlock($contentInsideData, $sum, $submode, $cartMode)
    {

    }
    
    static public function removeButtonBlock($productData)
    {

    }

    static public function noItemsBlock($virtualMode)
    {

    }

    static public function orderHeaderBlock($orderData)
    {

    }
    
    static public function tableRowOrder($productData)
    {

    }

    static public function totalRowOrderHeader($orderData)
    {

    }
    
    static public function totalRowOrder($totalSum, $orderData)
    {

    }
    
    static public function totalRowOrderCrm($totalSum, $orderData)
    {

    }

}
 