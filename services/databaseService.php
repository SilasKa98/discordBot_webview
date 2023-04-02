<?php


class DatabaseService{

    function __construct(){
    
      
     
        $this->connection = new mysqli("localhost", "root");
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        if (!$this->connection->select_db("bergfestbot")) {
          print "DB existiert nicht.";
          $this->connection->close();
          exit();
      }
      
       
/*
       $this->connection = new mysqli("db5012083111.hosting-data.io:3306", "dbu4749837", "dsa23iD+1dFs13Kdd!");
       if (mysqli_connect_errno()) {
           printf("Connect failed: %s\n", mysqli_connect_error());
           exit();
       }
        if (!$this->connection->select_db("dbs10168586")) {
            print "DB existiert nicht.";
            $this->connection->close();
            exit();
        }
    */    
        
    }

    function selectData($table, $condition = "", $params = array()) {
        // Build query
        $query = "SELECT * FROM $table";
        if ($condition) {
          $query .= " WHERE $condition";
        }
        
        // Prepare statement
        $stmt = $this->connection->prepare($query);
        
        // Bind parameters
        if ($params) {
          $types = str_repeat("s", count($params)); // Assuming all values are strings
          $stmt->bind_param($types, ...$params);
        }
        
        // Execute statement
        $stmt->execute();
        
        // Get results
        $result = $stmt->get_result();
        
        // Fetch data
        $data = array();
        while ($row = $result->fetch_assoc()) {
          $data[] = $row;
        }
        
        // Close statement
        $stmt->close();
        
        return $data;
    }
      

    function insertData($table, $data, $types) {
        // Get keys and values of data array
        $keys = array_keys($data);
        $values = array_values($data);
        
        // Build prepared statement
        $query = "INSERT INTO $table (".implode(",",$keys).") VALUES (".implode(",",array_fill(0,count($values),"?")).")";
        
        // Prepare statement
        $stmt = $this->connection->prepare($query);
        
        // Bind parameters
        #$types = str_repeat("s", count($values)); // Assuming all values are strings
        $stmt->bind_param($types, ...$values);
        
        // Execute statement
        $stmt->execute();
        
        // Check for errors
        if ($stmt->errno) {
          echo "Error: ".$stmt->error;
        }
        
        // Close statement
        $stmt->close();
    }

    function updateData($table, $data, $condition, $params = array(), $types) {
        // Get keys and values of data array
        $keys = array_keys($data);
        $values = array_values($data);
        
        // Build prepared statement
        $setValues = "";
        foreach ($keys as $key) {
          $setValues .= "$key=?,";
        }
        $setValues = rtrim($setValues, ",");
        
        $query = "UPDATE $table SET $setValues WHERE $condition";
        
        // Prepare statement
        $stmt = $this->connection->prepare($query);
        
        // Bind parameters
        #$types = str_repeat("s", count($values)); // Assuming all values are strings
        $stmt->bind_param($types, ...$values, ...$params);
        
        // Execute statement
        $stmt->execute();
        
        // Check for errors
        if ($stmt->errno) {
          echo "Error: ".$stmt->error;
        }
        
        // Close statement
        $stmt->close();
      }
      
   

    function deleteData($table, $id_to_delete){
      // Build prepared statement
      $sql = "DELETE FROM ".$table." WHERE id = ?";
      
      // Prepare statement
      $stmt = $this->connection->prepare($sql);

      // Bind parameters
      $stmt->bind_param("i", $id_to_delete);

      // Execute statement
      $stmt->execute();

      // Close statement
      $stmt->close();

    }
      
}


?>