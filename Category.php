<?php

    /**
     * Created by PhpStorm.
     * User: Shu
     * Date: 12/05/17
     * Time: 11:39 PM
     */

    class Category
    {
        private $categoryName;
        private $categoryUrl;
        private $products = [];

        /**
         * Category constructor.
         * @param $p_name
         * @param $p_url
         * @param array $p_categoryRecord
         */
        function __construct($p_name, $p_url, array $p_categoryRecord = [])
        {
            $this->categoryName = $p_name;
            $this->categoryUrl = $p_url;

            foreach ($p_categoryRecord as $recordName => $recordValue){
                $this->recordName = $recordValue;
            }
        }

        /**
         * @return mixed
         */
        public function getCategoryName()
        {
            return $this->categoryName;
        }

        /**
         * @param mixed $categoryName
         */
        public function setCategoryName($categoryName)
        {
            $this->categoryName = $categoryName;
        }

        /**
         * @return mixed
         */
        public function getCategoryUrl()
        {
            return $this->categoryUrl;
        }

        /**
         * @param mixed $categoryUrl
         */
        public function setCategoryUrl($categoryUrl)
        {
            $this->categoryUrl = $categoryUrl;
        }

        public function addProduct($product){
            if (is_array($product)){
                foreach ($product as $val){
                    array_push($val);
                }
            }
            else{
                array_push($this->products, $product);
            }
        }

        public function getProducts(){
            return $this->products;
        }
    }

?>