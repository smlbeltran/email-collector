<?php

namespace EmailCollector\Services\User;

class UserDatabaseInterface
{

    /**
     * @var $pdo \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createUser($payload)
    {
        /** @var EmailCollector\Bootstrap\ServiceDatabase $pdo */
        $pdo = $this->pdo->prepare("INSERT INTO users (first_name, last_name, email, password) 
                    VALUES (:firstName, :lastName, :email, :password)");

        $password = password_hash($payload->data->password, PASSWORD_BCRYPT);

        $pdo->bindParam(":firstName", $payload->data->first_name);
        $pdo->bindParam(":lastName", $payload->data->last_name);
        $pdo->bindParam(":email", $payload->data->email);
        $pdo->bindParam(":password", $password);

        try {
            $pdo->execute();
        } catch (\PDOException $e) {
            throw new \PDOException('can\'t create user' . $e->getMessage());
        }
    }

    public function getOne($payload)
    {
        $pdo =  $this->pdo->prepare("SELECT * FROM users WHERE email = :email");

        $pdo->bindParam(":email", $payload->data->email);

        try {
            $pdo->execute();
            $row = $pdo->fetch(\PDO::FETCH_OBJ);

            if (password_verify($payload->data->password, $row->password)) {
                return $row;
            }
        } catch (\PDOException $e) {
            throw new \PDOException('user can\'t be found  ' . $e->getMessage());
        }
    }
}
