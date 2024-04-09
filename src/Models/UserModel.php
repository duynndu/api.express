<?php

namespace src\Models;

use src\Commons\Model;

class UserModel extends Model
{
    protected ?string $table = 'users';

    function getUser($email,$password){
        $sql = "SELECT * FROM $this->table WHERE email = :email AND password = :password";

        return $this->query($sql,[
            ':email'=>$email,
            ':password'=>$password
        ])->fetch();
    }
}