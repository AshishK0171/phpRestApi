<?php
class UsersGateway{
    private PDO $conn;
    public function __construct(Database $database){
        $this->conn = $database->getConnection();
    }
    public function getvaliduser(array $data):array|null{
        $sql = "SELECT *
                FROM users
                WHERE name = :name 
                AND password = :password";
                
                $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $data["password"] ?? 0, PDO::PARAM_STR);        
        $stmt->execute();
        
        $data = null;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
    public function get(string $id):array{
        $sql = "SELECT *
                FROM users
                WHERE id = :id";
                
                $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);        $stmt->execute();
        
        $data = null;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
    
    
    public function getAll():array
    {
        $sql = "SELECT *
                FROM users";
                
        $stmt = $this->conn->query($sql);
        
        $data = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }
    public function create(array $data):string
    {
        $sql = "INSERT INTO users (name, password)
                VALUES (:name, :password)";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $data["password"] ?? 0, PDO::PARAM_STR);        
        $stmt->execute();
        
        return $this->conn->lastInsertId();

    }
    public function delete(string $id): int
    {
        $sql = "DELETE FROM users
                WHERE id = :id";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
}
?>