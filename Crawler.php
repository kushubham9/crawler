<?php

use Category as ProductCategory;

class Crawler {

    private $baseUrl;
    private $indexUrl;
    //Product Properties
    private $productCatContainer;

    // Product Information DOM Properties
    private $productInfoNode;

    // Product Cost DOM Properties
    private $productCostNode;

    //Product URL DOM Properties
    private $productUrlNode;

    //Product Name DOM Properties
    private $productNameNode;

    //Fetching Category Properties
    private $categoryContainer;
    private $categoryParent;
    private $categoryNode;
    private $categoryUrlAttr;

    // Category Pro
    /**
     * Crawler constructor.
     * @param array $config
     */
    public function __construct($indexUrl, array $config = [])
    {
        $urlComponenets = parse_url($indexUrl);
        $this->indexUrl = $indexUrl;

        $this->baseUrl = $urlComponenets['scheme'].'://'.$urlComponenets['host'];
        foreach ($config as $recordName => $recordValue){
            if (property_exists('Crawler', $recordName))
                $this->{$recordName} = $recordValue;
        }
    }

    private function _fetchProducts($url, $category){
        $objProduct = [];
        $DOM = $this->_getContent($url);
        $containerNodes = $this->_getContainerNodes($DOM, $this->productCatContainer);
        $productInfoNodes = $this->_getParentNodes($containerNodes, $this->productInfoNode);

        foreach ($productInfoNodes as $productInfoNode){
            $var_name = $this->_getProductName($productInfoNode);
            $var_cost = $this->_getProductCost($productInfoNode);
            $var_url = $this->_getProductUrl($productInfoNode);
            $product = new Product($var_name, $var_cost, $var_url);

            $product->setCategory($category);
            $objProduct[] = $product;
        }
        return $objProduct;
    }

    private function _getProductName($productInfoNode){
        $parentNode = $this->_getParentNodes([$productInfoNode],$this->productNameNode['parent']);
        $element = $this->_getElementNodes($parentNode, $this->productNameNode);
        return $element[0]->nodeValue;
    }

    private function _getProductCost($productInfoNode){
        $priceNode = $this->_getParentNodes([$productInfoNode], $this->productCostNode);
        return $priceNode[0]->nodeValue;
    }

    private function _getProductUrl($productInfoNode){
        $UrlNode = $this->_getElementNodes([$productInfoNode], $this->productUrlNode);
        return $UrlNode[0]->getAttribute($this->productUrlNode['attribute']);
    }


    /**
     * @param $DOM DOMDocument
     * @param $container
     * @param $parent
     * @param $node
     */
    private function _getNodes($DOM, $container, $parent, $node){
        $containerNodes = $this->_getContainerNodes($DOM, $container);
        $parentNodes = $this->_getParentNodes($containerNodes, $parent);
        $elementNodes = $this->_getElementNodes($parentNodes, $node);

        return $elementNodes;
    }

    private function _getElementNodes($parentNodes, $element){
        $elementNodes = [];
        foreach ($parentNodes as $parentNode){
            foreach ($parentNode->childNodes as $childNode){
                if ($childNode->nodeName == $element['tag']){
                    $elementNodes[] = $childNode;
                }
            }
        }
        return $elementNodes;
    }

    private function _getParentNodes($containerNodes, $parent){
        $parentNodes = [];
        $nodeStack = [];
        foreach ($containerNodes as $node){
            array_push($nodeStack, $node);
        }
        $node = array_pop($nodeStack);

        while ($node != null){
            foreach ($node->childNodes as $childNode){
                switch ($childNode->nodeType){
                    case XML_ELEMENT_NODE:
                        if (
                            $childNode->nodeName == $parent['tag']
                            &&
                            $childNode->hasChildNodes()
                        ){
                            if ((isset($parent['class']) && $this->_hasClass($childNode, $parent['class'])) || !isset($parent['class']))
                                $parentNodes [] = $childNode;
                            else
                                array_push($nodeStack, $childNode);
                        }

                        else{
                            array_push($nodeStack, $childNode);
                        }
                }
            }
            $node = array_pop($nodeStack);
        }
        return $parentNodes;
    }

    /**
     * @param $DOM DOMDocument
     * @param $container array
     */
    private function _getContainerNodes($DOM, $container){

        //Getting the container nodes
        $containerNodes = [];

        if (isset($container['id']) && !empty($container['id'])){
            $containerNodes[] = $DOM->getElementById($container['id']);
        }

        else if (isset($container['tag']) && !empty($container['tag'])){
            $temp_containerNode = $DOM->getElementsByTagName($container['tag']);

            if (isset($container['class']) && !empty($container['class'])){
                foreach ($temp_containerNode as $item){
                    if ($this->_hasClass($item, $container['class']))
                        $containerNodes[] = $item;
                }
            }
        }

        return $containerNodes;
    }

    /**
     * @param $node DOMNode
     * @param $className string
     * @return bool
     */
    private function _hasClass($node, $className){
        $attr_Class = $node->hasAttribute('class') ? $node->getAttribute('class') : '';
        if ($attr_Class == '')
            return false;

        else if (in_array($className,explode(' ',$attr_Class)))
            return true;
    }

    /**
     * @param $url
     * @return DOMDocument
     * @throws Exception
     */
    private function _getContent($url)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36'
        ));

        $response = curl_exec($curl);
        if (!$response){
            throw new Exception("Unable to fetch page. Please check the URL.");
        }

        curl_close($curl);

        $dom->loadHTML($response);
        return $dom;
    }

    private function _getCategories(){
        if (empty($this->categoryNode)){
            throw new Exception("Node type for the category must be defined.");
        }

        $DOM = $this->_getContent($this->indexUrl);
        $nodes = $this->_getNodes($DOM, $this->categoryContainer, $this->categoryParent, $this->categoryNode);

        $categories = [];
        foreach ($nodes as $node){
            if ($node->nodeValue != null){
                $categories [] = new Category($node->nodeValue, $node->getAttribute($this->categoryUrlAttr));
            }
        }
        return $categories;
    }

    /**
     * @param $products Product []
     */
    private function _printResult($products){
        echo '
             <html>
                <body>
                    <table border="1" cellpadding="5" width="100%">
                        <thead>
                            <td> Category </td>
                            <td> Category URL </td>
                            <td> Product Name </td>
                            <td> Product Url </td>
                            <td> Product Cost </td>
                        </thead>
                        <tbody>';
                            foreach ($products as $product){
                                echo '<tr>';
                                echo '<td>'.$product->getCategory()->getCategoryName().'</td>';
                                echo '<td>'.$product->getCategory()->getCategoryUrl().'</td>';
                                echo '<td>'.$product->getName().'</td>';
                                echo '<td>'.$product->getUrl().'</td>';
                                echo '<td>'.$product->getCost().'</td>';
                                echo '</tr>';
                            }
                echo '  </tbody>
                    </table>
                </body>
            </html>';
    }

    public function execute(){
        $products = [];
        $categories = $this->_getCategories();

        foreach ($categories as $categoryObj){
            $catalogueUrl = $this->baseUrl . $categoryObj->getCategoryUrl();
            $products = array_merge($products, $this->_fetchProducts($catalogueUrl, $categoryObj));
//            break;
        }

        $this->_printResult($products);
    }
}

//$p1 = new ProductCategory(['categoryName' => 'test']);
//die ($p1->categoryName);

?>