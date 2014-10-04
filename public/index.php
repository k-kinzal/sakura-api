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
$app->container->singleton('queue', function () use ($app) {
    class Queue {
    	protected $mongo;

    	public function __construct($mongo) {
    		$this->mongo = $mongo;
    	}

    	public function enqueue($name, $value) {
    		$collection = $this->mongo->selectCollection($name);
    		$collection->insert($value);
    	}

    	public function dequeue($name) {
    		$collection = $this->mongo->selectCollection($name);
    		$value = $collection->findOne();
    		$collection->remove(['_id' => $value['_id']]);
    		return $value;
    	}
    }

    return new Queue($app->mongo);
});
// config
$app->config(array(
	'debug' => true,
  'templates.path' => '../templates'
));
// routing
$app->get('/:name', function($name) use ($app) {
	$value = $app->queue->dequeue($name);

	$app->render('json.php', $value);
});
$app->post('/:name', function($name) use ($app) {
	$value = json_decode($app->request->getBody(), true);
	if (empty($value)) {
		// TODO: creaet validate exception
		throw new \Exception();
	}
	$app->queue->enqueue($name, $value);
});
// run application
$app->run();
