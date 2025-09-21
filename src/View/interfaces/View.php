<?php


interface View {

  public function render();


  public function showMessage($message);

  public function showErrorMessage($message);



}