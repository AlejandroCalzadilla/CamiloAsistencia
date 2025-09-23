<?php


interface View {


  
  public function actualizar();
  
  public function render($datos);


  public function showMessage($message);

  public function showErrorMessage($message);




}