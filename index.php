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

//Rodando o Slim
$app->run();

?>