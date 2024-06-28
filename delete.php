<?php
function deleteContact($conn) {
    $id = $_POST["id"];
    $response = [
        'status' => 400,
        'errors' => [],
        'previous' => $_POST,
        'message' => ''
    ];
    $sql = "DELETE FROM contacts WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $response['status'] = 200;
        $response['message'] = "Contact deleted successfully";
    } else {
        $response['errors']['connection'] = $conn->error;
        $response['message'] = "Error: " . $sql . "<br>" . $conn->error;
    }
    return $response;
}

?>