<?php

use App\model\Contact;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

const PAGE_SIZE = 100;

$app = AppFactory::create();

// Create Twig
$twig = Twig::create(__DIR__ . '/../app/templates');

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));

// Routes

// Redirect home to all contacts
$app->redirect('/', '/contacts', 301);

// Show all contacts
$app->get('/contacts', function (Request $request, Response $response, $args) {
    Contact::loadDB();
    $searchTerm = '';
    $queryTerms = $request->getQueryParams();

    if (array_key_exists('q', $queryTerms)) {
        $searchTerm = $queryTerms['q'];
        $contactsSet = Contact::search($searchTerm);
    } else {
        $contactsSet = Contact::all();
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'contacts.html.twig', [
        'contacts' => $contactsSet,
        'searchTerm' => $searchTerm
    ]);
});

// Show a Contact
$app->get('/contacts/{id:[0-9]+}', function (Request $request, Response $response, $args) {
    Contact::loadDB();
    $contact = Contact::find($args['id']);

    $view = Twig::fromRequest($request);
    return $view->render($response, 'show.html.twig', [
        'contact' => $contact
    ]);
});

// Show new form
$app->get('/contacts/new', function (Request $request, Response $response, $args) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'new.html.twig', []
    );
});

// Add a contact
$app->post('/contacts/new', function (Request $request, Response $response, $args) {
    Contact::loadDB();
    $parsedBody = $request->getParsedBody();

    $c = new Contact(null, $parsedBody['first_name'], $parsedBody['last_name'], $parsedBody['phone'], $parsedBody['email']);

    if ($c->save()) {
        // flash("Created New Contact!")
        return $response
            ->withHeader('Location', '/contacts')
            ->withStatus(302);
    } else {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'new.html.twig', [
                                          'contact' => $c
                                      ]
        );
    }
});

// Form to edit a contact
$app->get('/contacts/{id:[0-9]+}/edit', function (Request $request, Response $response, $args) {
    Contact::loadDB();
    $contact = Contact::find($args['id']);

    $view = Twig::fromRequest($request);
    return $view->render($response, 'edit.html.twig', [
        'contact' => $contact
    ]);
});

// Edit a Contact
$app->post('/contacts/{id:[0-9]+}/edit', function (Request $request, Response $response, $args) {
    Contact::loadDB();
    $c = Contact::find($args['id']);

    $parsedBody = $request->getParsedBody();
    $c->update($parsedBody['first_name'], $parsedBody['last_name'], $parsedBody['phone'], $parsedBody['email']);

    $view = Twig::fromRequest($request);
    if ($c->save()) {
        //flash("Updated Contact!")
        return $response
            ->withHeader('Location', '/contacts/' . $c->id() )
            ->withStatus(302);
    } else {
        return $view->render($response, 'edit.html.twig', [
            'contact' => $c
        ]);
    }
});

// Delete a contact
$app->post('/contacts/{id:[0-9]+}/delete', function (Request $request, Response $response, $args) {
    Contact::loadDB();
    $c = Contact::find($args['id']);

    $c->delete();
    //flash("Deleted Contact!")

    return $response
        ->withHeader('Location', '/contacts')
        ->withStatus(302);
});

$app->run();
