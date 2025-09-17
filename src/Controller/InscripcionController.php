<?php


class InscripcionController{

   public $model;

   public $view;


    public function  __construct(InscripcionModel $model ,InscripcionView $view){
        $this->$view =$view;
    }


    public function handleRequest()
    {


    }



}