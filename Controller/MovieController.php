<?php

namespace Controller;

use Model\Movie;

require_once __DIR__ . '/../Config/configuration.php';

class MovieController
{
    private $movieModel;

    // O construtor agora aceita a dependência Movie
    public function __construct(Movie $movieModel)
    {
        $this -> movieModel = $movieModel;
    }

    /**
     * Retorna a lista de filmes.
     * @return array|null Retorna a lista de filmes ou null em caso de falha.
     */
    public function getMovies()
    {
        // Usa a dependência injetada para chamar o método do Model
        $movies = $this->movieModel->getMovies();

        if ($movies) {
            // Retorna o resultado para ser testado
            return [
                'status' => 200,
                'content' => $movies
            ];
        } else {
            return [
                'status' => 404,
                'content' => ["message" => "Filmes nao encontrados"]
            ];
        }
    }

    /**
     * Cria um ou mais filmes.
     * @param mixed $data Dados do filme (objeto único ou array de objetos).
     * @return array Resultado da operação.
     */
    public function createMovie($data)
    {
        // $data é o corpo da requisição já decodificado (mockado no teste)
        // A lógica de file_get_contents("php://input") foi removida para testabilidade.

        if (is_array($data)) {
            $createdMovies = 0;
            $errors = [];

            foreach ($data as $index => $movieData) {
               
                
                if (isset($movieData->title) && isset($movieData->descript) && isset($movieData->rate)) {
                    if ($movieData->rate < 0.0 || $movieData->rate > 9.9) {
                        $errors[] = "Filme " . ($index + 1) . ": Avaliacao deve estar entre 0.0 e 9.9";
                        continue;
                    }

                    $this->movieModel->title = $movieData->title;
                    $this->movieModel->descript = $movieData->descript;
                    $this->movieModel->rate = $movieData->rate;

                    if ($this->movieModel->createMovie()) {
                        $createdMovies++;
                    } else {
                        $errors[] = "Filme " . ($index + 1) . ": Falha ao criar filme";
                    }
                } else {
                    $errors[] = "Filme " . ($index + 1) . ": Informação inválida";
                }
            }

            if ($createdMovies > 0 && empty($errors)) {
                return [
                    'status' => 201,
                    'content' => ["message" => "$createdMovies filmes criados com sucesso"]
                ];
            } elseif ($createdMovies > 0 && !empty($errors)) {
                return [
                    'status' => 207, // Multi-Status
                    'content' => [
                        "message" => "$createdMovies filmes criados com sucesso",
                        "errors" => $errors
                    ]
                ];
            } else {
                return [
                    'status' => 400,
                    'content' => [
                        "message" => "Nenhum filme foi criado",
                        "errors" => $errors
                    ]
                ];
            }
        } else {
            if (isset($data->title) && isset($data->descript) && isset($data->rate)) {
                if ($data->rate < 0.0 || $data->rate > 9.9) {
                    return [
                        'status' => 400,
                        'content' => ["message" => "Avaliacao deve estar entre 0.0 e 9.9"]
                    ];
                }

                $this->movieModel->title = $data->title;
                $this->movieModel->descript = $data->descript;
                $this->movieModel->rate = $data->rate;

                if ($this->movieModel->createMovie()) {
                    return [
                        'status' => 201,
                        'content' => ["message" => "Filme criado com sucesso"]
                    ];
                } else {
                    return [
                        'status' => 500,
                        'content' => ["message" => "Falha ao criar filme"]
                    ];
                }
            } else {
                return [
                    'status' => 400,
                    'content' => ["message" => "Informação inválida"]
                ];
            }
        }
    }

    /**
     * Atualiza um filme.
     * @param object $data Dados do filme a ser atualizado.
     * @return array Resultado da operação.
     */
    public function updateMovie($data)
    {
        // A lógica de file_get_contents("php://input") foi removida para testabilidade.

        if (isset($data->id) && isset($data->title) && isset($data->descript) && isset($data->rate)) {
            // Atribui os dados ao Model injetado
            $this->movieModel->id = $data->id;
            $this->movieModel->title = $data->title;
            $this->movieModel->descript = $data->descript;
            $this->movieModel->rate = $data->rate;

            if ($this->movieModel->updateMovie()) {
                return [
                    'status' => 200,
                    'content' => ["message" => "Filme atualizado com sucesso"]
                ];
            } else {
                return [
                    'status' => 500,
                    'content' => ["message" => "Falha ao atualizar filme"]
                ];
            }
        } else {
            return [
                'status' => 400,
                'content' => ["message" => "Informação invalida"]
            ];
        }
    }

    /**
     * Exclui um filme.
     * @param int|null $id ID do filme a ser excluído.
     * @return array Resultado da operação.
     */
    public function deleteMovie($id)
    {
        // A lógica de $_GET['id'] foi removida para testabilidade.

        if ($id) {
            // Atribui o ID ao Model injetado
            $this->movieModel->id = $id;

            if ($this->movieModel->deleteMovie()) {
                return [
                    'status' => 200,
                    'content' => ["message" => "Filme excluído com sucesso"]
                ];
            } else {
                return [
                    'status' => 500,
                    'content' => ["message" => "Falha ao excluir filme"]
                ];
            }
        } else {
            return [
                'status' => 400,
                'content' => ["message" => "ID invalido"]
            ];
        }
    }
}

// Funções auxiliares para simular o ambiente de produção
// No ambiente de produção, o index.php ou router chamaria o controller.
// Para manter a compatibilidade com o código original, vamos adicionar uma função
// que simula o comportamento de envio de headers e echo, mas apenas se não estiver
// em ambiente de teste (para não interferir no PHPUnit).

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    function sendResponse($result) {
        if (isset($result['status']) && isset($result['content'])) {
            header('Content-Type: application/json', true, $result['status']);
            echo json_encode($result['content']);
        }
    }
}

?>

