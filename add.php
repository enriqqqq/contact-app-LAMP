<?php
function addContact($conn) {
    // get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    // regex patterns
    $phoneRegex = '/^(?:\+62|62|0)8\d{7,10}$/';
    $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    // response template
    $response = [
        'status' => 400,
        'errors' => [],
        'previous' => $_POST,
        'message' => 'Failed to add contact'
    ];

    // validate name
    if (empty($name)) {
        $response['errors']['name'] = "Name is required";
    }
    else if (strlen($name) < 3) {
        $response['errors']['name'] = "Name must be at least 3 characters long";
    }
    else if (strlen($name) > 45) {
        $response['errors']['name'] = "Name must be at most 45 characters long";
    }

    // validate phone
    if (empty($phone)) {
        $response['errors']['phone'] = "Phone number is required, e.g. 081234567890, +6281234567890 or 6281234567890 and must be 10-13 digits long";
    }
    else if (!preg_match($phoneRegex, $phone)) {
        $response['errors']['phone'] = "Invalid phone number, e.g. 081234567890, +6281234567890 or 6281234567890 and must be 10-13 digits long";
    }

    // validate email
    if (empty($email)) {
        $response['errors']['email'] = "Email is required";
    }
    else if (!preg_match($emailRegex, $email)) {
        $response['errors']['email'] = "Invalid email address";
    }
    else if (strlen($email) > 45) {
        $response['errors']['email'] = "Email must be at most 45 characters long";
    }

    // return errors if any
    if (!empty($response['errors'])) {
        return $response;
    }
    
    $sql = "INSERT INTO contacts (name, email, phone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $phone);
        if ($stmt->execute()) {
            $response['status'] = 200;
            $response['message'] = "New contact added successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Error: " . $conn->error;
        }
    } else {
        $response['status'] = 500;
        $response['message'] = "Error: " . $conn->error;
    }

    return $response;
}
?>
