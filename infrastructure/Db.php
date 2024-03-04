<?php

class Db
{

    private static $dsn = 'mysql:host=localhost;dbname=naz;charset=utf8'; //change hostname and db name according to your database
    private static $username = 'bappi'; //change username according to your database
    private static $password = '1234'; //change password according to your database
    private static $options = [];

    private $pdo;
    private static $db;

    public static function getInstance()
    {
        if (!isset(self::$db)) {
            self::$db = new Db();
        }
        return self::$db;
    }

    private function __construct()
    {
        try {


            $this->pdo = new PDO(self::$dsn, self::$username, self::$password, self::$options);
            // Set common attributes
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function execute($sqlPath, $payload)
    {
        // Assuming $pdo is your PDO connection object

        // Define your payload
        // $payload = [
        //     'search_query' => 'search term', // Example search term
        //     'limit' => 10,
        //     'offset' => 0,
        // ];

        // Load the SQL query from the file
        $sql = file_get_contents(__DIR__ . '/../sql/' . $sqlPath . '.sql');

        // Prepare the SQL statement
        $stmt = $this->pdo->prepare($sql);

        // Automatically bind parameters from the payload
        foreach ($payload as $placeholder => $value) {
            // Determine the parameter type
            $paramType = PDO::PARAM_STR; // Default to string
            if (is_int($value)) {
                $paramType = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $paramType = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $paramType = PDO::PARAM_NULL;
            }

            // Bind the parameter
            $stmt->bindValue(":$placeholder", $value, $paramType);
        }

        // Execute the query
        $stmt->execute();

        // Fetch the results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Use the results
        // echo '<pre>', print_r($results, true), '</pre>';

        return $results;
    }
}
