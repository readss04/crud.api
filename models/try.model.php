<?php

interface IExample{
    public function getAll();
    public function insert_user($data);
}


class Example implements IExample
{
    protected $pdo, $glb;

    protected $table_name = "users";

    public function __construct(\PDO $pdo, GlobalMethods $glb){
        $this->pdo = $pdo;
        $this->glb = $glb;
    }

    public function hello(){
        $data = [
            "sample"=>"Hello"
        ];
        return $data;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM ".$this->table_name;
        try{
            $stmt = $this->pdo->prepare($sql);

            if($stmt->execute()){
                $data = $stmt->fetchAll();
                if($stmt->rowCount() >= 1){
                    return $this->glb->responsePayload($data, "success","Successfully pulled all data", 200);
                }else{
                    return $this->glb->responsePayload(null, "fauled","No data exisiting", 404);
                }
            }

        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }

     // Get user by ID
     public function getUserById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            if ($data) {
                return $this->glb->responsePayload($data, "success", "User found", 200);
            } else {
                return $this->glb->responsePayload(null, "failed", "User not found", 404);
            }
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
    // insert user
    public function insert_user($data){
        $sql = "INSERT INTO ".$this->table_name."(firstname, lastname, is_admin) VALUES(?, ?, ?)";
        try{
            $stmt = $this->pdo->prepare($sql);

            $isAdmin = filter_var($data->is_admin, FILTER_VALIDATE_BOOLEAN);

            if($stmt->execute([$data->firstname, $data->lastname, $isAdmin])){
                return $this->glb->responsePayload(null, "success","Successfully inserted data", 200);
            }else{
                return $this->glb->responsePayload(null, "failed","Failed to insert data", 400);
            }
        }catch(\PDOException $e){
            echo $e->getMessage();
        }
        return "yo";
    }

     // Update user
     public function update_user($id, $data) {
        $sql = "UPDATE " . $this->table_name . " SET firstname = ?, lastname = ?, is_admin = ? WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            
            $isAdmin = filter_var($data->is_admin, FILTER_VALIDATE_BOOLEAN);
    
            if ($stmt->execute([$data->firstname, $data->lastname, $isAdmin, $id])) {
                return $this->glb->responsePayload(null, "success", "User updated successfully", 200);
            } else {
                return $this->glb->responsePayload(null, "failed", "Failed to update user", 400);
            }
        } catch (\PDOException $e) {
            return $this->glb->responsePayload(null, "error", $e->getMessage(), 500);
        }
    }


    public function deleteUser($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                return $this->glb->responsePayload(null, "success", "User deleted", 200);
            } else {
                return $this->glb->responsePayload(null, "failed", "Failed to delete user", 400);
            }
        } catch (\PDOException $e) {
            return $this->glb->responsePayload(null, "error", $e->getMessage(), 500);
        }
    }
    
    
}
