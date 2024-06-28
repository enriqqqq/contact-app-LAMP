<?php
function downloadCSV($conn) {
    $sql = "SELECT * FROM contacts";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $filename = "contacts.csv";
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ["ID", "Name", "Email", "Phone", "Created At"]);
        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, [$row["id"], $row["name"], $row["email"], $row["phone"], $row["created_at"]]);
        }
        fclose($fp);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
    }
}

?>