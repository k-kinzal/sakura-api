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
$app->get('/:name', function($name) use($app) {
	$app->render('json.php', array('name' => $name));
});
$app->post('/:name', function($name) use($app) {
});
// run application
$app->run();
