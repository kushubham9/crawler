<?php

/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 13/05/17
 * Time: 12:35 PM
 */
class Product
{
    private $name;
    private $cost;
    private $url;
    private $category;


    function __construct($name, $cost, $url, array $addRecord = [])
    {
        $this->name = $name;
        $this->cost = $cost;
        $this->url = $url;

        foreach ($addRecord as $recordName => $recordValue){
            $this->recordName = $recordValue;
        }
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }


}