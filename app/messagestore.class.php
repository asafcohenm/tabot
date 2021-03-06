<?php

class messagestore extends message {

    private $log;
    private $connection;

    function __construct($message, $log) {
        parent::__construct($message);

        $this->log = $log;

        //Define DB
        $userName = $_ENV['DB_USERNAME']?$_ENV['DB_USERNAME']:config::$db['userName'];
        $password = $_ENV['DB_PASSWORD']?$_ENV['DB_PASSWORD']:config::$db['password'];
        $server = $_ENV['DB_SERVER']?$_ENV['DB_SERVER']:config::$db['server'];
        $name = $_ENV['DB_NAME']?$_ENV['DB_NAME']:config::$db['name'];

        $this->connection = new PDO('mysql:host='.$server.';dbname='.$name, $userName, $password);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);  // allows us to catch errors
    }

    public function save() {

        try {
            $query = $this->connection->prepare('INSERT INTO '.$name.'.messages (message, userId, created) VALUES(:message, :userId, NOW())');
            if(!$query) {
                $this->log->error('PDO error', $this->connection->errorInfo());
                die();
            } else {
                $res = $query->execute(array(
                    'message' => $this->getMessage(),
                    'userId' => $this->getUser()->getUserId()
                ));
            }
        } catch (PDOException $e) {
            $this->log->error('PDO error', $e->getMessage());
            die();
        }

    }

    function __destruct() {
        $this->connection = null;
    }

}

?>
