<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Create Twig
$twig = Twig::create(__DIR__ . '/../app/templates');

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));

// Routes
$app->redirect('/', '/contacts', 301);

$app->get('/contacts', function (Request $request, Response $response, $args) {

    $contacts = new App\model\Contacts();
    $searchTerm = '';
    $queryTerms = $request->getQueryParams();

    if (array_key_exists('q', $queryTerms)) {
        $searchTerm = $queryTerms['q'];
        $contactsSet = $contacts->search($searchTerm);
    } else {
        $contactsSet = $contacts->all();
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'contacts.html.twig', [
        'contacts' => $contactsSet,
        'searchTerm' => $searchTerm
    ]);
});

$app->run();
