<?php
error_reporting(E_ERROR | E_PARSE);

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

$indexUrl = 'https://ellelouisedesigns.com/collections/shop-all';

$config = [
    'categoryContainer' => ['tag' => 'li', 'class' => 'filter'],
    'categoryParent' => ['tag' => 'li'],
    'categoryNode' => ['tag'=>'a'],
    'categoryUrlAttr' => 'href',
    'productCatContainer' => ['tag' => 'div', 'class'=>'product-index'],
    'productInfoNode' => ['tag'=>'div', 'class'=>'product-info-inner'],
    'productCostNode' => ['tag'=>'div', 'class'=>'prod-price', 'parent' => ['tag' => 'div', 'class'=>'price']],
    'productNameNode' => ['tag'=>'h3','parent'=> ['tag'=>'a']],
    'productUrlNode' => ['tag'=>'a', 'attribute' => 'href']
];

$crawler = new Crawler($indexUrl, $config);
$crawler->execute();

?>