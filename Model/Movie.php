<?php
namespace Model;

use PDO;
use Model\Connection;

class Movie
{
    private $conn;

    public $id;
    public $title;
    public $descript;
    public $rate;

    public function __construct()
    {
        $this->conn = Connection::getConnection();
    }

    // Método para obter todos os usuários
    public function getMovies()
    {
        $sql = "SELECT * FROM movies";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para criar um novo usuário
    public function createMovie()
    {
        $sql = "INSERT INTO movies (title, descript, rate) VALUES (:title, :descript, :rate)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindParam(":descript", $this->descript, PDO::PARAM_STR);
        $stmt->bindParam(":rate", $this->rate, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para editar um usuário
    public function updateMovie()
    {
        $sql = "UPDATE movies SET title = :title, descript = :descript, rate = :rate WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindParam(":descript", $this->descript, PDO::PARAM_STR);
        $stmt->bindParam(":rate", $this->rate, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para excluir um usuário
    public function deleteMovie()
    {
        $sql = "DELETE FROM movies WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

?>