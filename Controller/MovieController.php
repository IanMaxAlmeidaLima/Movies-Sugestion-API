<?php
namespace Controller;

use Model\Movie;

require_once __DIR__ . '/../Config/configuration.php';

class MovieController
{
    public function getMovies()
    {
        $movie = new Movie();
        $movies = $movie->getMovies();

        if ($movies) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($movies);
        } else {
            header('Content-Type: application/json', true, 404);
            echo json_encode(["message" => "Filmes nao encontrados"]);
        }
    }

    public function createMovie()
    {
$data = json_decode(file_get_contents("php://input"));

        if (is_array($data)) {
            $createdMovies = 0;
            $errors = [];

            foreach ($data as $index => $movieData) {
                if (isset($movieData->title) && isset($movieData->descript) && isset($movieData->rate)) {
                    if ($movieData->rate < 0.0 || $movieData->rate > 9.9) {
                        $errors[] = "Filme " . ($index + 1) . ": Avaliacao deve estar entre 0.0 e 9.9";
                        continue;
                    }

                    $movie = new Movie();
                    $movie->title = $movieData->title;
                    $movie->descript = $movieData->descript;
                    $movie->rate = $movieData->rate;

                    if ($movie->createMovie()) {
                        $createdMovies++;
                    } else {
                        $errors[] = "Filme " . ($index + 1) . ": Falha ao criar filme";
                    }
                } else {
                    $errors[] = "Filme " . ($index + 1) . ": Informação inválida";
                }
            }

            if ($createdMovies > 0 && empty($errors)) {
                header('Content-Type: application/json', true, 201);
                echo json_encode(["message" => "$createdMovies filmes criados com sucesso"]);
            } elseif ($createdMovies > 0 && !empty($errors)) {
                header('Content-Type: application/json', true, 207); // Multi-Status
                echo json_encode([
                    "message" => "$createdMovies filmes criados com sucesso",
                    "errors" => $errors
                ]);
            } else {
                header('Content-Type: application/json', true, 400);
                echo json_encode([
                    "message" => "Nenhum filme foi criado",
                    "errors" => $errors
                ]);
            }
        } else {
            if (isset($data->title) && isset($data->descript) && isset($data->rate)) {
                if ($data->rate < 0.0 || $data->rate > 9.9) {
                    header('Content-Type: application/json', true, 400);
                    echo json_encode(["message" => "Avaliacao deve estar entre 0.0 e 9.9"]);
                    return;
                }

                $movie = new Movie();
                $movie->title = $data->title;
                $movie->descript = $data->descript;
                $movie->rate = $data->rate;

                if ($movie->createMovie()) {
                    header('Content-Type: application/json', true, 201);
                    echo json_encode(["message" => "Filme criado com sucesso"]);
                } else {
                    header('Content-Type: application/json', true, 500);
                    echo json_encode(["message" => "Falha ao criar filme"]);
                }
            } else {
                header('Content-Type: application/json', true, 400);
                echo json_encode(["message" => "Informação inválida"]);
            }
        }
    }

    public function updateMovie()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id) && isset($data->title) && isset($data->descript) && isset($data->rate)) {
            $movie = new Movie();
            $movie->id = $data->id;
            $movie->title = $data->title;
            $movie->descript = $data->descript;
            $movie->rate = $data->rate;

            if ($movie->updateMovie()) {
                header('Content-Type: application/json', true, 200);
                echo json_encode(["message" => "Filme atualizado com sucesso"]);
            } else {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["message" => "Falha ao atualizar filme"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["message" => "Informação invalida"]);
        }
    }

    // Função para excluir um filme
    public function deleteMovie()
    {
        // Obtém os dados da requisição
        $id = $_GET['id'] ?? null; // Verifica se o ID foi passado na URL

        if ($id) {
            $movie = new Movie();
            $movie->id = $id;

            if ($movie->deleteMovie()) {
                header('Content-Type: application/json', true, 200);
                echo json_encode(["message" => "Filme excluído com sucesso"]);
            } else {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["message" => "Falha ao excluir filme"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["message" => "ID invalido"]);
        }

    }
}

?>