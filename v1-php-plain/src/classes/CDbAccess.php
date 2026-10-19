<?php

class dbaccess
{
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset;
    private $conn;

    public function __construct()
    {
        $this->host    = 'mysql';
        $this->db      = 'db_projekte';
        $this->user    = 'formular_manage';
        $this->pass    = 'php!?*';
        $this->charset = 'utf8mb4';
        $this->conn = new mysqli($this->host,$this->user, $this->pass,$this->db);
        $this->conn->set_charset($this->charset);
    }

    public function passwordcheck($password)
    {
        if ($this->pass !=$password) {
            return false;
        } else {
            return true;
        }
    }

    public function select()
    {

        $query = "SELECT id, first_name, last_name, birthdate, email, address, zip_code, city, subject, message FROM tbl_formular;";
        $result =$this->conn->query($query);$data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $this->conn->close();
        return $data;
    }

    public function filter($column,$direction = 'ASC')
    {
        $allowedColumns = [
            'id', 'first_name', 'last_name', 'birthdate',
            'email', 'address', 'zip_code', 'city', 'subject', 'message'
        ];

        if (!in_array($column, $allowedColumns, true)) {$column = 'id';
        }

        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        $query = "SELECT id, first_name, last_name, birthdate, email, address, zip_code, city, subject, message 
                  FROM tbl_formular 
                  ORDER BY `{$column}` {$direction};";

        $result =$this->conn->query($query);$data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $this->conn->close();

        return $data;
    }

    public function search($column,$value)
    {
        $allowedColumns = [
            'id', 'first_name', 'last_name', 'birthdate',
            'email', 'address', 'zip_code', 'city', 'subject', 'message'
        ];

        if (!in_array($column, $allowedColumns, true)) {$column = 'first_name';
        }

        $escapedValue = $this->conn->real_escape_string($value);

        $query = "SELECT id, first_name, last_name, birthdate, email, address, zip_code, city, subject, message 
                  FROM tbl_formular 
                  WHERE `{$column}` LIKE '%{$escapedValue}%';";

        $result =$this->conn->query($query);$data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $this->conn->close();

        return $data;
    }

    public function insert($firstName, $lastName,$email, $birthdateVal,$addressVal, $zipCodeVal,$cityVal, $subject,$message)
    {
        $query = "INSERT INTO tbl_formular (first_name, last_name, email, birthdate, address, zip_code, city, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            exit('Abfragefehler: ' . $this->conn->error);
        }

        $stmt->bind_param(
            "sssssssss",
            $firstName,
            $lastName,
            $email,
            $birthdateVal,
            $addressVal,
            $zipCodeVal,
            $cityVal,
            $subject,
            $message
        );

        $success =$stmt->execute();
        $stmt->close();$this->conn->close();
        return $success;
    }

    public function update($firstName,$lastName, $email,$birthdateVal, $addressVal,$zipCodeVal, $cityVal,$subject, $message,$id)
    {
        $query = "UPDATE tbl_formular 
                  SET first_name = ?, 
                      last_name = ?, 
                      email = ?, 
                      birthdate = ?, 
                      address = ?, 
                      zip_code = ?, 
                      city = ?, 
                      subject = ?, 
                      message = ? 
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            exit('Abfragefehler: ' . $this->conn->error);
        }

        $stmt->bind_param(
            "sssssssssi",
            $firstName,$lastName,
            $email,$birthdateVal,
            $addressVal,$zipCodeVal,
            $cityVal,$subject,
            $message,$id
        );

        $success =$stmt->execute();
        $stmt->close();$this->conn->close();
        return $success;
    }

    public function delete($id)
    {
        $query = "DELETE FROM `tbl_formular` WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            exit('Error preparing statement: ' . $this->conn->error);
        }

        $stmt->bind_param("i", $id);
        $success =$stmt->execute();
        $stmt->close();$this->conn->close();
        return $success;
    }

    public function truncate()
    {
        $query = "TRUNCATE TABLE tbl_formular;";
        $result = $this->conn->query($query);

        $this->conn->close();
        return $result;
    }
}