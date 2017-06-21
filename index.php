<?php 
//Require do composer
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;

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
$app->get('/admin', function() {

	$page = new PageAdmin();

	$page->setTpl("index");

});

//Rodando o Slim
$app->run();

?>