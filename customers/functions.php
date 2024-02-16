<?php

require_once("../inc/dbcon.php");

function getCustomerList() {
    global $conn;
    $query = "SELECT * FROM customers";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            $data = [
                'status' => 200,
                'message' => 'Customers fetched successfully.',
                'data' => $res,
            ];
            header("200"); 
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No customers found'
            ];
            http_response_code(404);  
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error'
        ];
        http_response_code(500); 
        return json_encode($data);
    }
}
