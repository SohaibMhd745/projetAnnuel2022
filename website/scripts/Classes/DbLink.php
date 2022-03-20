<?php

class DbLink{
    private $pdo;

    function __construct(string $host, string $charset, string $dbname, string $user, string $pass){
        $host = $this->sanitizeStringQuotes($host);
        $charset = $this->sanitizeStringQuotes($charset);
        $dbname = $this->sanitizeStringQuotes($dbname);
        $user = $this->sanitizeStringQuotes($user);
        $pass = $this->sanitizeStringQuotes($pass);

        try{
            $pdo = new PDO('mysql:host='.$host.';charset='.$charset.';dbname='.$dbname,
                $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }catch(Exception $e){
            die('Fatal PDO creation error : ' . $e->getMessage());
        }
    }

    /**
     *
     * Public Functions
     *
     */

    /**
     * @param string $query : Query to execute
     * @param array $args : Arguments for the query
     * @return mixed : -1 if sql exception, false if empty, array of selected if select, true if insert/update
     */
    public function query(string $query, array $args){
        try {
            $req = $this->pdo->prepare($query);
            $req->execute($args);
            return $req->fetch();
        }catch (mysqli_sql_exception $err){
            return -1;
        }
    }

    /**
     *
     * Utility functions
     *
     */

    /**
     * Removes quotes and double quotes from a string
     * @param string $inputString : String to be sanitized
     * @return string : Sanitized String
     */
    private function sanitizeStringQuotes(string $inputString) : string{
        $inputString = htmlspecialchars($inputString);

        str_replace("'", "\'", $inputString);
        str_replace('"', '\"', $inputString);

        return $inputString;
    }

    /**
     * Removes special characters from any sql query argument
     * @param string $inputString : String to be sanitized
     * @return string : Sanitized String
     */
    private function sanitizeSqlQueryWord(string $inputString) : string{
        return preg_replace("/[A-Za-z0-9]",'',$inputString);
    }

}



