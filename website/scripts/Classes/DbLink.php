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

    public function simpleSelectWhere(array $columns, string $table, array $simpleConditions, array $args){
        foreach ($columns as $key=>$column) {
            $columns[$key] = $this->sanitizeSqlQueryWord($column);
        }
        $table = $this->sanitizeSqlQueryWord($table);

        $fullCondition = "";
        $nbArg = 0;

        foreach ($simpleConditions as $key=>$condition){
            switch (gettype($condition)){
                case "string":
                    switch ($condition){
                        case "||":
                        case "or":
                        case "OR":
                            $fullCondition .= " OR";
                            break;

                        case "&&":
                        case "and":
                        case "AND":
                            $fullCondition .= " AND";
                            break;

                        case "BETWEEN":
                        case "between":
                            $fullCondition .= " BETWEEN";
                            break;

                        default:
                            break;
                    }
                    break;
                case "array":
                    if (count($condition)==3){
                        $fullCondition .= "(";

                        $fullCondition .= $this->sanitizeSqlQueryWord($condition[0]);

                        switch ($condition[1]){
                            case "=":
                            case "==":
                            case "equal":
                            case "eq":
                                $fullCondition .= " =";
                                break;

                            case ">":
                            case "g":
                                $fullCondition .= " >";
                                break;

                            case "<":
                            case "l":
                                $fullCondition .= " <";
                                break;

                            case ">=":
                            case "ge":
                                $fullCondition .= " >=";
                                break;

                            case "<=":
                            case "le":
                                $fullCondition .= " <=";
                                break;

                            default:
                                break;
                        }

                        if (gettype($condition[2]) == "integer") $fullCondition .= $condition[2];
                        else {
                            if ($condition[2] === "?") {
                                $nbArg++;
                                $fullCondition .= " :".$nbArg;
                            }else{
                                $arg2 = $this->sanitizeStringQuotes($condition[2]);
                                $fullCondition .= " '".$arg2."'";
                            }
                        }

                        $fullCondition .= ")";
                    }

                    break;

                default:
                    break;
            }
        }

        $query = "SELECT ";
        for ($i = 0; $i < count($columns); $i++){
            if ($i == (count($columns)-1)) $query .= " ".$columns[$i];
            else $query .= " ".$columns[$i].",";
        }
        $query .= " FROM ".$table." WHERE".$fullCondition;

        $req = $this->pdo->prepare($query);
        $req->execute($args);

        return $req->fetch();

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



