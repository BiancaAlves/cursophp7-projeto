<?php 
session_start();

//Require do composer
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

//Instanciando a classe Slim
$app = new Slim();

//Slim: Configurando para que sejam mostrados todas as mensagens de erro
$app->config('debug', true);

//Slim: Criando conteúdo para o caminho "/"
$app->get('/', function() {

	$page = new Page();

	$page->setTpl("index");	

});

//Slim: Criando conteúdo para o caminho "/admin"
$app->get('/admin/', function() {
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});


$app->get('/admin/login', function(){
	$page = new PageAdmin(array(
		"header"=>false,
		"footer"=>false
		));
	$page->setTpl("login");
});

$app->post('/admin/login', function(){

	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");
	exit;
});

$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;
});

// Página que lista todos os usuários - Consultar
$app->get("/admin/users", function(){
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();
	
	$page->setTpl("users", array(
		"users" => $users
		));

});

// Página onde se adiciona um novo usuário - Criar
$app->get("/admin/users/create", function(){
	User::verifyLogin();

	$page = new PageAdmin();
	
	$page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

// Página onde o usuário faz alterações - Alterar
//O caminho dessa página contém a própria id do usuário que a está acessando
$app->get("/admin/users/:iduser", function($iduser){
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();
	
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
		));

});

//Agora, as rotas que receberão as informações
$app->post("/admin/users/create", function(){

	User::verifyLogin();

	$user = new User();

	// A linha abaixo transmite o resultado do checkbox para um valor booleano
	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;
});

$app->post("/admin/users/:iduser", function($iduser){

	User::verifyLogin();
	
	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1:0;

	$user->get((int)$iduser);
		
	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
});

$app->get("/admin/forgot", function(){
	$page = new PageAdmin(array(
		"header"=>false,
		"footer"=>false
	));

	$page->setTpl("forgot");
});

$app->post("/admin/forgot", function(){
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin(array(
		"header"=>false,
		"footer"=>false
	));

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin(array(
		"header"=>false,
		"footer"=>false
	));

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User;

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
			"cost"=>12
		]);

	$user->setPassword($password);

	$page = new PageAdmin(array(
		"header"=>false,
		"footer"=>false
	));

	$page->setTpl("forgot-reset-success");

});

//Rodando o Slim
$app->run();

?>