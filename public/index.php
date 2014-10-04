<?php
// loader
$loader = require('../vendor/autoload.php');
// application
$app = new \Slim\Slim();
// service
$app->container->singleton('mongo', function () {
    // connect to Compose assuming your MONGOHQ_URL environment
    // variable contains the connection string
		$connection_url = getenv("MONGOHQ_URL");
     // create the mongo connection object
    $m = new MongoClient($connection_url);
    // extract the DB name from the connection path
    $url = parse_url($connection_url);
    $db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
    // use the database we connected to
    $db = $m->selectDB($db_name);

    return $db;
});
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
