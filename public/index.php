<?php
// loader
$loader = require('../vendor/autoload.php');
// application
$app = new \Slim\Slim();
// config
$app->config(array(
	'debug' => true,
  'templates.path' => '../templates'
));
// routing
$app->get('/', function() use($app) {
	$app->render('json.php', array());
});
$app->get('/:name', function($name) use($app) {
	$app->render('json.php', array('name' => $name));
});
// run application
$app->run();
