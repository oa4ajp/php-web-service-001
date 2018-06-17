<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

// Routes

//Cors support
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

/*
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/
// Define app routes
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("{ \"message\": \"" . $args['name']. "\"}");
});

// get all todos
$app->get('/tasks', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT * FROM tasks ORDER BY task");
    $sth->execute();
    $todos = $sth->fetchAll();
    //return $response->write("{ \"message\": \"" . 'OK' . "\"}");
    return $this->response->withJson($todos);
});

// Add a new todo
$app->post('/task', function ($request, $response) {
    $input = $request->getParsedBody();
    $sql = "INSERT INTO tasks (task) VALUES (:task)";
    $sth = $this->db->prepare($sql);
    $sth->bindParam("task", $input['task']);
    $sth->execute();
    $input['id'] = $this->db->lastInsertId();
    return $this->response->withJson($input);
});

// Add a new file
$app->post('/file', function ($request, $response) {
    $directory = $this->get('upload_directory');    
    $uploadedFiles = $request->getUploadedFiles();

    //var_dump($uploadedFiles);
    //$this->logger->info("");

    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['file'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = moveUploadedFile($directory, $uploadedFile);
         $input['file'] =  $filename;
        return $this->response->withJson($input);
    }

});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory directory to which the file is moved
 * @param UploadedFile $uploaded file uploaded file to move
 * @return string filename of moved file
 */
function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $filename = $uploadedFile->getClientFilename();
    
    $path = $directory . DIRECTORY_SEPARATOR . $filename;
    //var_dump($path);
    $uploadedFile->moveTo($path);

    return $filename;
}