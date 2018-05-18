<?php 
class DB
{

    private $database;

    public function __construct( $user,
                                  $password,
                                  $dbName,
                                  $dbServer)
    {
        $str = 'pgsql:host=' . $dbServer . ';dbname=' . $dbName;
		//echo "<pre>";
		//print_r(get_declared_classes());
		//die;
        try {
            $this->database = new PDO($str, $user, $password);
            //Exibe todos os erros de SQL
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print "Ocorreu um erro de conexão: " . $e->getMessage();
        }
    }

    public function begin()
    {
        return $this->database->beginTransaction();
    }

    public function rollback()
    {
        return $this->database->rollBack();
    }

    public function commit()
    {
        return $this->database->commit();
    }

    public function queryAsArray($sql)
    {
        $return = array();
        $data = array();

        foreach ($this->database->query($sql) as $return) {
            array_push($data, $return);
        }

        return $data;
    }

    /**
     * Realizar consultas
     *
     * @param type $sql
     * @return PDOStatement
     * @throws SQLException
     */
    public function query($sql)
    {
        try {
            return $this->database->query($sql);
        } catch (PDOException $exception) {
          echo '<pre>' . $exception->getMessage() . '</pre>';
          file_put_contents("db.log", $exception->getTraceAsString());
        }
    }


    /**
     *
     * @param String $sql
     * @param Array $data
     * @return bool <b>TRUE</b> no caso de sucesso ou <b>FALSE</b> caso ocora alguma falha.
     * @throws SQLException
     */
    public function execute($sql, $data)
    {
        try {
            $stmt = $this->database->prepare($sql);
            return $stmt->execute(array_values($data));
        } catch (PDOException $exception) {
            echo '<pre>' . $exception->getMessage() . '</pre>';
            print_r($data);
        }
    }

    public function exec($sql)
    {
        try {
            return $this->database->exec($sql);
        } catch (PDOException $exception) {
            echo '<pre>' . $exception->getMessage() . '</pre>';
        }
    }

    public function lastInsertId($name){
        return $this->database->lastInsertId($name);
    }

    public function __destruct()
    {
        $this->database = null;
    }

}
?>