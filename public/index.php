<?php
// loader
$loader = require('../vendor/autoload.php');
// application
$app = new \Slim\Slim();
// config
$app->config(array(
	'debug' => false,
  'templates.path' => '../templates'
));
// service
//-- create mongodb service
$app->container->singleton('mongo', function () {
	// initialize
	$connection_url = getenv("MONGOHQ_URL");
  $url = parse_url($connection_url);
  $db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
  // create mongo
  $mongo = new MongoClient($connection_url);
  // create and return for database instance
  return $mongo->selectDB($db_name);
});
//-- create queue service
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

  		try {
    		$value = $collection->findAndModify(null, null, null, [
    			'remove' => true,
    		]);
    	} catch (\Exception $e) {
    		$value = false;
    	}

  		return $value;
  	}

  	public function dequeueAll($name, $limit = INT_MAX) {
  		$values = [];
  		while ($limit-- > 0 && $value = $this->dequeue($name)) {
  			$values[] = $value;
  		}

  		return $values;
  	}
  }

  return new Queue($app->mongo);
});
// routing
//-- get application status and enable application by newrelic
$app->get('/', function() use ($app) {
});
//-- get json
$app->get('/:name', function($name) use ($app) {
	$value = $app->queue->dequeue($name);
	if (empty($value)) {
		$app->response()->status(204);
		return;
	}

	$app->view->clear(); // remove scrap data
	$app->render('json.php', $value);
});
//-- post json
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
