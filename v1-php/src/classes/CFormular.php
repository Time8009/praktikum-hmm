<?php
class CFormular {

    const db_name = "db_projekte";
    const Table_name = "tbl_formular";

    const host = "mysql";
    const user = "formular_manage";
    const pass = "php!?*";

    const allcolumns = "id, first_name, last_name, birthdate, email, address, zip_code, city, subject, message";
    const widcolumns = "first_name, last_name, birthdate, email, address, zip_code, city, subject, message";

    private static ?mysqli $conn = null;

    public int $id = 0;
    public string $first_name = '';
    public string $last_name = '';
    public ?DateTime $birthdate = null;
    public ?string $birthdateVal = null;
    public string $email = '';
    public string $address = '';
    public string $plz = '';
    public string $city = '';
    public string $subject = '';
    public string $message = '';

    public function __construct(int|array $data = []) {
        if (self::$conn === null) {
            self::$conn = new mysqli(self::host, self::user, self::pass, self::db_name);
            self::$conn->set_charset("utf8mb4");
        }

        if (is_int($data)) {
            $stmt = self::$conn->prepare("SELECT " . self::allcolumns . " FROM " . self::Table_name . " WHERE id = ?");
            $stmt->bind_param("i", $data);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if ($row) {
                $this->assignData($row);
            }
        } elseif (is_array($data) && !empty($data)) {
            $this->assignData($data);
        }
    }

    private function assignData(array $data): void {
        $this->id = (int)($data['id'] ?? 0);
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';

        if (!empty($data['birthdate'])) {
            $this->birthdate = new DateTime($data['birthdate']);
            $this->birthdateVal = $this->birthdate->format('Y-m-d');
        } else {
            $this->birthdate = null;
            $this->birthdateVal = null;
        }

        $this->email = $data['email'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->plz = $data['zip_code'] ?? $data['plz'] ?? '';
        $this->city = $data['city'] ?? '';
        $this->subject = $data['subject'] ?? '';
        $this->message = $data['message'] ?? '';
    }

    public static function find_all(): array {
        if (self::$conn === null) {
            new self();
        }
        $formularObjects = [];
        $query = "SELECT * FROM " . self::Table_name;

        $result = self::$conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $formularObjects[] = new self($row);
            }
            $result->free();
        }

        return $formularObjects;
    }

    public static function find_person(): array {
        if (self::$conn === null) {
            new self();
        }
        $formularObjects = [];
        $query = "SELECT id, first_name, last_name, birthdate FROM " . self::Table_name;

        $result = self::$conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $date = !empty($row['birthdate']) ? new DateTime($row['birthdate']) : null;

                $formularObjects[] = [
                    'id'         => (int)$row['id'],
                    'first_name' => (string)$row['first_name'],
                    'last_name'  => (string)$row['last_name'],
                    'birthdate'  => $date ? $date->format('Y-m-d') : null
                ];
            }
            $result->free();
        }
        return $formularObjects;
    }

    public function save(): void {
        if ($this->id > 0) {
            $this->update();
            return;
        }

        if (self::$conn === null) {
            new self();
        }

        $query = 'INSERT INTO ' . self::Table_name . ' (' . self::widcolumns . ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);';
        $stmt = self::$conn->prepare($query);

        if (!$stmt) {
            exit('Abfragefehler: ' . self::$conn->error);
        }

        $stmt->bind_param(
            "sssssssss",
            $this->first_name,
            $this->last_name,
            $this->birthdateVal,
            $this->email,
            $this->address,
            $this->plz,
            $this->city,
            $this->subject,
            $this->message
        );

        $stmt->execute();
        $stmt->close();
    }

    public function update(): void {
        if (self::$conn === null) {
            new self();
        }

        $query = "UPDATE " . self::Table_name . " 
                  SET first_name = ?, 
                      last_name = ?, 
                      birthdate = ?, 
                      email = ?, 
                      address = ?, 
                      zip_code = ?, 
                      city = ?, 
                      subject = ?, 
                      message = ? 
                  WHERE id = ?;";
        $stmt = self::$conn->prepare($query);

        if (!$stmt) {
            exit('Abfragefehler: ' . self::$conn->error);
        }
        
        $stmt->bind_param(
            'sssssssssi',
            $this->first_name,
            $this->last_name,
            $this->birthdateVal,
            $this->email,
            $this->address,
            $this->plz,
            $this->city,
            $this->subject,
            $this->message,
            $this->id
        );

        $stmt->execute();
        $stmt->close();
    }

    public function delete(): bool {
        if ($this->id <= 0) {
            return false;
        }

        if (self::$conn === null) {
            new self();
        }

        $stmt = self::$conn->prepare("DELETE FROM " . self::Table_name . " WHERE id = ?;");

        if (!$stmt) {
            exit('Abfragefehler: ' . self::$conn->error);
        }

        $stmt->bind_param("i", $this->id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
}