<?php
/**
 * Created by PhpStorm.
 * User: sankaran.m
 * Date: 03-03-2019
 * Time: 21:34
 */

class User {
    public function __construct() {
    }

// Add User / Register
    public function register($data) {
        global $DB;
        // Prepare Query
        $DB->query('INSERT INTO users (name, email,password) 
      VALUES (:name, :email, :password)');

        // Bind Values
        $DB->bind(':name', $data['name']);
        $DB->bind(':email', $data['email']);
        $DB->bind(':password', $data['password']);

        //Execute
        if ($DB->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Find USer BY Email
    public function findUserByEmail($email) {
        global $DB;
        $DB->query("SELECT * FROM users WHERE email = :email");
        $DB->bind(':email', $email);

        $row = $DB->single();

        //Check Rows
        if ($DB->rowCount() > 0) {
            $DB->close();
            return true;
        } else {
            $DB->close();
            return false;
        }
    }

    // Login / Authenticate User
    public function login($email, $password) {
        global $DB;
        $DB->query("SELECT * FROM users WHERE email = :email");
        $DB->bind(':email', $email);

        $row = $DB->single();
        $DB->close();

        $hashed_password = $row->password;
        if (password_verify($password, $hashed_password)) {
            return $row;
        } else {
            return false;
        }
    }

    // Find User By ID
    public function getUserById($id) {
        global $DB;
        $DB->query("SELECT * FROM users WHERE id = :id");
        $DB->bind(':id', $id);

        $row = $DB->single();

        return $row;
    }
}