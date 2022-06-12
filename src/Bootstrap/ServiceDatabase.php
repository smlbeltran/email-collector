<?php


namespace App\Bootstrap;

use Psr\Container\ContainerInterface;

class ServiceDatabase
{
    public function load(ContainerInterface $container)
    {
        $container->set('Database.Master', function () use ($container) {
            $config = $container->get('Config');

            $dns = vsprintf('mysql:host=%s;dbname=%s;port=%s;charset=utf8', [
                $config['database.mysql.host'],
                $config['database.mysql.name'],
                $config['database.mysql.port'],
            ]);

            try {

                $pdo = new \PDO($dns, $config['database.mysql.user'], $config['database.mysql.password'], [
                    \PDO::ATTR_PERSISTENT,
                ]);

                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);

            } catch (\PDOException $e) {
                throw new \Exception('connection failed: ' . $e->getMessage());
            }

            return $pdo;
        });
    }
}
