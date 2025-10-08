<?php

use PHPUnit\Framework\TestCase;
use Controller\MovieController;
use Model\Movie;

class MovieTest extends TestCase{

    private $movieController;
    private $mockMovieModel;

    protected function setUp(): void{
        $this -> mockMovieModel = $this -> createMock(Movie::class);

        $this -> movieController = new MovieController($this -> mockMovieModel);
        
        unset($_GET);
        if (function_exists('xdebug_get_headers')) {

        }
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_movies_list_when_movies_are_found(){
        $expectedMovies = [
            ['id' => 1, 'title' => 'Filme 1', 'descript' => 'Desc 1', 'rate' => 8.5],
            ['id' => 2, 'title' => 'Filme 2', 'descript' => 'Desc 2', 'rate' => 9.0]
        ];
        $expectedResult = [
            'status' => 200,
            'content' => $expectedMovies
        ];

        $this->mockMovieModel->method('getMovies')->willReturn($expectedMovies);

        $result = $this->movieController->getMovies();

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_message_when_no_movies_are_found(){
        $expectedResponse = ["message" => "Filmes nao encontrados"];
        $expectedResult = [
            'status' => 404,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('getMovies')->willReturn(false);

        $result = $this->movieController->getMovies();

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_a_single_movie_is_created_successfully(){
        $inputData = (object)['title' => 'Novo Filme', 'descript' => 'Nova Desc', 'rate' => 7.5];
        $expectedResponse = ["message" => "Filme criado com sucesso"];
        $expectedResult = [
            'status' => 201,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('createMovie')->willReturn(true);

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_a_single_movie_has_invalid_rate(){
        $inputData = (object)['title' => 'Filme Ruim', 'descript' => 'Desc Ruim', 'rate' => 10.0];
        $expectedResponse = ["message" => "Avaliacao deve estar entre 0.0 e 9.9"];
        $expectedResult = [
            'status' => 400,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_a_single_movie_is_missing_fields(){
        $inputData = (object)['title' => 'Filme Incompleto', 'rate' => 7.0];
        $expectedResponse = ["message" => "Informação inválida"];
        $expectedResult = [
            'status' => 400,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_a_single_movie_creation_fails_in_model(){
        $inputData = (object)['title' => 'Filme Falho', 'descript' => 'Desc Falha', 'rate' => 6.0];
        $expectedResponse = ["message" => "Falha ao criar filme"];
        $expectedResult = [
            'status' => 500,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('createMovie')->willReturn(false);

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }


    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_all_movies_in_array_are_created_successfully(){
        $inputData = [
            (object)['title' => 'Filme A', 'descript' => 'Desc A', 'rate' => 8.0],
            (object)['title' => 'Filme B', 'descript' => 'Desc B', 'rate' => 9.5]
        ];
        $expectedResponse = ["message" => "2 filmes criados com sucesso"];
        $expectedResult = [
            'status' => 201,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('createMovie')->willReturn(true);

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_some_movies_fail_and_others_succeed_in_array_creation(){
        $inputData = [
            (object)['title' => 'Filme OK', 'descript' => 'Desc OK', 'rate' => 8.0],
            (object)['title' => 'Filme Fail', 'descript' => 'Desc Fail', 'rate' => 7.0],
            (object)['title' => 'Filme OK 2', 'descript' => 'Desc OK 2', 'rate' => 9.0]
        ];
        
        $this->mockMovieModel->expects($this->exactly(3))
             ->method('createMovie')
             ->willReturnOnConsecutiveCalls(true, false, true);

        $expectedResponse = [
            "message" => "2 filmes criados com sucesso",
            "errors" => [
                "Filme 2: Falha ao criar filme"
            ]
        ];
        $expectedResult = [
            'status' => 207,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_all_movies_fail_in_array_creation(){
        $inputData = [
            (object)['title' => 'Filme Fail 1', 'descript' => 'Desc Fail 1', 'rate' => 8.0],
            (object)['title' => 'Filme Fail 2', 'descript' => 'Desc Fail 2', 'rate' => 9.0]
        ];
        
        $this->mockMovieModel->method('createMovie')->willReturn(false);

        $expectedResponse = [
            "message" => "Nenhum filme foi criado",
            "errors" => [
                "Filme 1: Falha ao criar filme",
                "Filme 2: Falha ao criar filme"
            ]
        ];
        $expectedResult = [
            'status' => 400,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_able_to_return_when_array_contains_invalid_rate_and_no_movies_are_created(){
        $inputData = [
            (object)['title' => 'Filme Inválido 1', 'descript' => 'Desc Inválida 1', 'rate' => 10.1],
            (object)['title' => 'Filme Inválido 2', 'descript' => 'Desc Inválida 2', 'rate' => -0.5]
        ];
        
        $expectedResponse = [
            "message" => "Nenhum filme foi criado",
            "errors" => [
                "Filme 1: Avaliacao deve estar entre 0.0 e 9.9",
                "Filme 2: Avaliacao deve estar entre 0.0 e 9.9"
            ]
        ];
        $expectedResult = [
            'status' => 400,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_array_contains_invalid_rate_and_some_movies_are_created(){
        $inputData = [
            (object)['title' => 'Filme OK', 'descript' => 'Desc OK', 'rate' => 8.0],
            (object)['title' => 'Filme Inválido', 'descript' => 'Desc Inválida', 'rate' => 10.1],
            (object)['title' => 'Filme OK 2', 'descript' => 'Desc OK 2', 'rate' => 9.0]
        ];
        
        $this->mockMovieModel->expects($this->exactly(2))
             ->method('createMovie')
             ->willReturn(true);

        $expectedResponse = [
            "message" => "2 filmes criados com sucesso",
            "errors" => [
                "Filme 2: Avaliacao deve estar entre 0.0 e 9.9"
            ]
        ];
        $expectedResult = [
            'status' => 207,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->createMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }


    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_update_movie_successfully(){
        $inputData = (object)['id' => 1, 'title' => 'Filme Atualizado', 'descript' => 'Desc Atualizada', 'rate' => 8.8];
        $expectedResponse = ["message" => "Filme atualizado com sucesso"];
        $expectedResult = [
            'status' => 200,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('updateMovie')->willReturn(true);

        $result = $this->movieController->updateMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_update_movie_is_missing_fields(){
        $inputData = (object)['id' => 1, 'title' => 'Filme Incompleto', 'rate' => 7.0];
        $expectedResponse = ["message" => "Informação invalida"];
        $expectedResult = [
            'status' => 400,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->updateMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_update_movie_fails_in_model(){
        $inputData = (object)['id' => 1, 'title' => 'Filme Falho', 'descript' => 'Desc Falha', 'rate' => 6.0];
        $expectedResponse = ["message" => "Falha ao atualizar filme"];
        $expectedResult = [
            'status' => 500,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('updateMovie')->willReturn(false);

        $result = $this->movieController->updateMovie($inputData);

        $this->assertEquals($expectedResult, $result);
    }


    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_delete_movie_successfully(){
        $id = 1;
        $expectedResponse = ["message" => "Filme excluído com sucesso"];
        $expectedResult = [
            'status' => 200,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('deleteMovie')->willReturn(true);

        $result = $this->movieController->deleteMovie($id);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_delete_movie_is_missing_id(){
        $id = null;
        $expectedResponse = ["message" => "ID invalido"];
        $expectedResult = [
            'status' => 400,
            'content' => $expectedResponse
        ];

        $result = $this->movieController->deleteMovie($id);

        $this->assertEquals($expectedResult, $result);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_return_when_delete_movie_fails_in_model(){
        $id = 99;
        $expectedResponse = ["message" => "Falha ao excluir filme"];
        $expectedResult = [
            'status' => 500,
            'content' => $expectedResponse
        ];

        $this->mockMovieModel->method('deleteMovie')->willReturn(false);

        $result = $this->movieController->deleteMovie($id);

        $this->assertEquals($expectedResult, $result);
    }
}
?>
