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
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_show_movies(){
        
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_add_movies(){
        
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_update_movies(){
        
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function it_should_be_able_to_delete_movies(){
        
    }
}
?>