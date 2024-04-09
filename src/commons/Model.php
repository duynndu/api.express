<?php


namespace src\commons;


use PDO;
use PDOException;

class Model
{
    public PDO|null $conn;
    protected ?string $table;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $post = $_ENV['DB_PORT'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->conn = new PDO("mysql:host=$host;port=$post;dbname=$dbname", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $PDOException) {
            echo "Kết nối thất bại: " . $PDOException->getMessage();
            die;
        }
        $this->query("SET GLOBAL 
        sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
        ");
    }

    public function getAll($limit = '')
    {
        if($limit || is_numeric($limit) || $limit == 0){
            $limit = "LIMIT ".intval($limit);
        }
        $data = $this->query("SELECT * FROM $this->table ORDER BY id DESC $limit");
        return $data->fetchAll();
    }

    public function getById($id): array|null
    {
        $data = $this->query("SELECT * FROM $this->table WHERE id = :id ORDER BY id DESC", [':id' => $id]);
        return $data->fetch();
    }

    // thêm
    public function insert($data)
    {
        $cols = implode(",", array_keys($data));
        $keyNew = array_map(fn($value) => ":$value", array_keys($data));
        $values = implode(',', $keyNew);
        $sql = "INSERT INTO 
                    $this->table ($cols) 
                    VALUES ($values)";
        return [
            'rowCount'=>$this->query($sql, array_combine($keyNew, $data))->rowCount(),
            'lastInsertId'=>$this->conn->lastInsertId()
        ];
    }


    //sửa
    public function update($data, $id)
    {
        $keyNew = array_map(fn($key) => ":$key", array_keys($data));
        $setValue = implode(',', array_map(fn($key, $value) => "$key = $value", array_keys($data), $keyNew));
        $sql = "UPDATE 
                    $this->table
                SET 
                    $setValue 
                WHERE
                    id=:id";
        $data = array_combine($keyNew, $data);
        $data[':id'] = $id;
        return $this->query($sql, $data)->rowCount();
    }


     function delete($id)
    {
        $sql = "DELETE FROM $this->table WHERE id=:id";
        return $this->query($sql, [':id' => $id])->rowCount();
    }

    protected function query($sql, $params = [])
    {
        try {
            foreach ($params as $key=>$param) {
                if(empty($param)){
                    throw new PDOException("params[$key] is Empty");
                }
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }catch (PDOException $e){
            return null;
        }

    }

    public function __destruct()
    {
        $this->conn = null;
    }
}