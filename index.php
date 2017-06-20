<?php 
//Require do composer
require_once("vendor/autoload.php");

//Instanciando a classe Slim
$app = new \Slim\Slim();

//Slim: Configurando para que sejam mostrados todas as mensagens de erro
$app->config('debug', true);

//Slim: Criando conteúdo para o caminho "/"
$app->get('/', function() {

	$sql = new Hcode\DB\Sql();

	$results = $sql->select("SELECT * FROM tb_users");

	echo json_encode($results);

});

//Rodando o Slim
$app->run();

 ?>