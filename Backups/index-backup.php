<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app = new \Slim\Slim();

$app->config('debug', true);

// Cria um arquivo de ROTAS para as telas do SITE
// SITE.PHP no admin
require_once("site.php");

$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});


$app->get('/admin', function() {
    
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->get('/admin/login', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false,
	]);

	$page->setTpl("login");

});


// Rota para Login no sistema
$app->post('/admin/login', function() {
    
	    User::login($_POST["login"], $_POST["password"]);

	    header("Location: /admin");
	    exit;


});

//ROta para Logout do Sistema
$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;

});

//Rota para cadastro, alteração e exclusão de usuários
$app->get("/admin/users", function() {

	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array("users"=>$users));
});

$app->get("/admin/users/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");
});


$app->post("/admin/users/create", function(){

	User::verifyLogin();

	$user = new User();

	// Se o inadmin for clicado, passa 1 ou caso contrario passa 0
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	// Passa os dados da tela para serem gravados
	$user->setData($_POST);	

	// Chamada de procedimento para salver o registro no Banco de Dados
	$user->save();

	header("Location: /admin/users");
	exit;


});

$app->get("/admin/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

$app->get("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(

		"user"=>$user->getValues()
	));

});


// Rota para alterar usuários
$app->post("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
});

// Rota para alteração de senha por esquecimento
$app->get("/admin/forgot", function() {

	 $page = new PageAdmin([
	 	"header"=>false,
	 	"footer"=>false
	  ]);

	 $page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function (){

	$page = new PageAdmin([
	 	"header"=>false,
	 	"footer"=>false
	  ]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

       //Funcao para recuperar o usuario que solicita a recuperação de senha.
	   $user = User::validForgotDecrypt($_GET["code"]);

       $page = new PageAdmin([
	 	"header"=>false,
	 	"footer"=>false
	  ]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));	

});


$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"],PASSWORD_DEFAULT);

	$user->setPassword($password);

    $page = new PageAdmin([
	 	"header"=>false,
	 	"footer"=>false
	  ]);

	$page->setTpl("forgot-reset-success");	

});

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", 
	['categories'=>$categories
    ]);		

});

$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");		

});

$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;

});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

    User::verifyLogin(); 

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header('Location: /admin/categories');
	exit;

});

$app->get("/admin/categories/:idcategory", function($idcategory)
{

    User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		'category'=>$category->getValues()]);

});

$app->post("/admin/categories/:idcategory", function($idcategory)
{

    User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();	

	header('Location: /admin/categories');
	exit;

});

$app->run();
      
 ?>