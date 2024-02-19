<?php
error_reporting(0);
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

//Create new customer.
function error422 ($message) {
    $data = [
        'status' => '422',
        'message' => $message,
    ];
    header('HTTP/1.0 422 Unprocessable Entity');
    echo json_encode($data);
    exit();
}

function storeCustomer($customerInput) {
    global $conn;
    $name = mysqli_real_escape_string($conn, $customerInput['name']);
    $email = mysqli_real_escape_string($conn, $customerInput['email']);
    $phone = mysqli_real_escape_string($conn, $customerInput['phone']);

    if(empty(trim($name))) {
        return error422('Enter your name');
    }else if(empty(trim($email))){
        return error422('Enter your mail address');
    }else if(empty(trim($phone))){
        return error422('Enter your phone number');
    }else{
        $query = "INSERT INTO customers (name, email, phone) VALUES ('$name', '$email', '$phone')";
        $result = mysqli_query($conn, $query);
        if($result){
            $data = [
                'status' => 201,
                'message' => 'Customer created successfully'
            ];
            http_response_code(201); 
            return json_encode($data);
        }else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            http_response_code(500); 
            return json_encode($data);
        }
    }
}

//Get one customer.
function getCustomer($customerParams) {
    global $conn;
    if ($customerParams['id'] == null) {
        return error422('No customer id provided');
    }

    $customerId = mysqli_real_escape_string($conn, $customerParams['id']);
    $query = "SELECT * FROM customers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $customerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => 'Customer fetched successfully',
                'data' => $res,
            ];
            http_response_code(200);
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No customers were found'
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

//Update customer information.
function updateCustomer($customerInput, $customerParams) {
    global $conn;

    if (empty($customerParams['id'])) {
        return error422('No customer id provided');
    }

    $customerId = mysqli_real_escape_string($conn, $customerParams['id']);

    $name = mysqli_real_escape_string($conn, $customerInput['name']);
    $email = mysqli_real_escape_string($conn, $customerInput['email']);
    $phone = mysqli_real_escape_string($conn, $customerInput['phone']);

    if (empty(trim($name)) || empty(trim($email)) || empty(trim($phone))) {
        return error422('Please enter name, email, and phone number');
    }

    // Prepare and bind the update statement
    $query = "UPDATE customers SET name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $phone, $customerId);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $data = [
            'status' => 200,
            'message' => 'Customer updated successfully'
        ];
        http_response_code(200);
        return json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error'
        ];
        http_response_code(500);
        return json_encode($data);
    }
}

//Delete a customer
function deleteCustomer($customerParams) {
    global $conn;

    if (empty($customerParams['id'])) {
        return error422('No customer id provided');
    }

    $customerId = mysqli_real_escape_string($conn, $customerParams['id']);

    // Prepare and bind the delete statement
    $query = "DELETE FROM customers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $customerId);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $data = [
            'status' => 200,
            'message' => 'Customer deleted successfully'
        ];
        http_response_code(200);
        return json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error'
        ];
        http_response_code(500);
        return json_encode($data);
    }
}

