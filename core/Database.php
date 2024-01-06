<?php

use function PHPSTORM_META\type;

class Database {
    protected $conn;
    function __construct() {
        $credentials = json_decode(file_get_contents(BASE_PATH . "/credentials.json"), true);
        $this->conn = new mysqli();
        try {
            $this->conn->connect(
                $credentials["db_host"],
                $credentials["db_user"],
                $credentials["db_pass"],
                $credentials["db_name"]
            );
        } catch(Exception $e) {
            http_response_code(500);
        }
    }

    // Function to execute query
    protected function execute(string $sanitize_query) {
        try {
            if($result = $this->conn->query($sanitize_query)) {
                return [
                    "result" => $result,
                    "error" => null
                ];
            }
        } catch(Exception $e) {
            return [
                "result" => null,
                "error" => $e
            ];
        }
    }
    public function sanitize(string $str) {
        return $this->conn->escape_string($str);
    }

    public function insert(string $table_name, array $att_values) {
        $attributes = "";
        $values = "";
        foreach($att_values as $attribute => $value) {
            if($attributes !== "") {
                $attributes .= ",";
                $values .= ",";
            }
            $attributes .= "`" . $attribute . "`";
            if(!is_numeric($value)) $values .= "'" . $value . "'";
            else $values .= $value;
        }
        $query_string = "INSERT INTO $table_name($attributes) VALUE($values)";
        // echo $query_string;
        $result = $this->execute($query_string);
        return $result;
    }

    public function select(
        string $table_name,
        array $join_array=NULL, 
        array $attribute_array=NULL, 
        array $where_array=NULL,
        string $order=NULL,
        int $limit=NULL
    ) {
        // JOIN condition
        $join = "";
        if($join_array != null) {
            foreach($join_array as $k=>$v) {
                $join .= "$k ON $v ";
            }
        }
        // Attribute Implode
        if($attribute_array) {
            $attribute = implode(",", $attribute_array);
        } else {
            $attribute = "*";
        }
        // WHERE condition
        $where = "";
        if($where_array) {
            foreach ($where_array as $v) {
                if($where == "") $where .= "WHERE ";
                else $where .= " and ";
                $where .= $v;
            }
        }
        $query_string = "SELECT $attribute FROM $table_name $join $where";
        if($order) {
            $query_string .= "ORDER BY $order";
        }
        // Limit condition
        if($limit) {
            $query_string .= " LIMIT $limit";   
        }
        // echo $query_string . "\n";
        $result = $this->execute($query_string);
        return $result;
    }
}
?>