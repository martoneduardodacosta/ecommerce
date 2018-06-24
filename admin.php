<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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

      
 ?>