<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new \Slim\Slim();

$app->config('debug', true);

// Cria um arquivo de ROTAS para as telas do SITE
// SITE.PHP no admin
require_once("site.php");

// Cria um arquivo de ROTAS para as rotinas de administração do site
require_once("admin.php");

// Cria um arquivo de ROTAS para as rotinas de administração de usuarios do site
require_once("admin-users.php");

// Cria um arquivo de ROTAS para as rotinas de administração de categforias do site
require_once("admin-categories.php");

// Cria um arquivo de ROTAS para as rotinas de administração de categforias do site
require_once("admin-products.php");

$app->run();
      
 ?>