<?php


namespace src\commons;


use Cocur\Slugify\Slugify;

class Controller
{
    protected $slugify;
    public function __construct()
    {
        $this->slugify = new Slugify();
    }
}