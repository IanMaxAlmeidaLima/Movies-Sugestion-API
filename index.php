<?php
require_once __DIR__ . '/vendor/autoload.php';

use Controller\MovieController;
$movieController = new MovieController();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $movieController->getMovies();
        break;
    case 'POST':
        $movieController->createMovie();
        break;
    case 'PUT':
        $movieController->updateMovie();
        break;
    case 'DELETE':
        $movieController->deleteMovie();
        break;
    default:
        echo json_encode(["message" => "Metodo não permitido"]);
        break;
}
?>

