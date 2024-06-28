<?php

function getlist($conn) {
    return $conn->query("SELECT * FROM contacts");
}

?>