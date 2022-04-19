<?php
  session_start();

  require_once('tools/Router.php');

  $router = new Router();

  $router->routeReq();
?>