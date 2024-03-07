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


    private function getStmt($sql, $payload)
    {


        if (isset($payload['limit'])) {
            if ($payload['limit'] == -1) {
                $payload['limit'] = 9999999999;
            }
        }


        // Prepare the SQL statement
        $stmt = $this->pdo->prepare($sql);

        // $inputString = "This is a :test string :with :db_19 multiple :placeholders :something.";

        // Define the regular expression
        // $pattern = '/:([^:\s]+)/';
        // $pattern = '/:(\S+)/';
        $pattern = '/:([a-zA-Z0-9_]+)/';

        // Perform the match
        preg_match_all($pattern, $sql, $matches);

        // Output the entire matches including the colon
        // print_r($matches[1]);


        $binded = array();


        foreach ($matches[1] as $placeholder) {

            if ($binded[$placeholder] ?? false) continue;

            $value = $payload[$placeholder] ?? null;

            // print_r(' - ');
            // print_r($placeholder);
            // print_r(' - ');
            // print_r(var_dump($value));

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

            $binded[$placeholder] = true;
        }





        // // Automatically bind parameters from the payload
        // foreach ($payload as $placeholder => $value) {
        //     // Determine the parameter type
        //     $paramType = PDO::PARAM_STR; // Default to string
        //     if (is_int($value)) {
        //         $paramType = PDO::PARAM_INT;
        //     } elseif (is_bool($value)) {
        //         $paramType = PDO::PARAM_BOOL;
        //     } elseif (is_null($value)) {
        //         $paramType = PDO::PARAM_NULL;
        //     }

        //     // Bind the parameter
        //     $stmt->bindValue(":$placeholder", $value, $paramType);
        // }


        return $stmt;
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


        $stmt = $this->getStmt($sql, $payload);

        $results = array();


        try {


            // $stmt->debugDumpParams();
            // Execute the query
            $stmt->execute();

            // Fetch the results
            $results['ret_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Use the resultss
            // echo '<pre>', print_r($results, true), '</pre>';

            // print_r($resultss);
        } catch (PDOException $e) {

            $results['error'] = [
                "info" => $e->errorInfo,
                "message" => "Error while executing '$sqlPath' .  ###ERR: " . $e->getMessage(),
                "trace" => $e->getTrace(),
                // "sql" => $stmt->debugDumpParams(),
            ];
        }

        // // Execute the query
        // $stmt->execute();
        // // Fetch the results
        // $results['ret_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $results;
    }
}
