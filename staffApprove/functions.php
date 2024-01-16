<?php
// Include the connection file
include('conn.php');

// Function to list pending approval employees
function listPendingEmployees() {
    global $conn; // Access to the global connection variable
    $sql = "SELECT * FROM approval_tb WHERE status = 'pending'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $pendingEmployees = array();
        while ($row = $result->fetch_assoc()) {
            $pendingEmployees[] = $row;
        }
        return $pendingEmployees;
    } else {
        return array(); // Return an empty array if there are no pending employees
    }
}

// Function to approve an employee
function approveEmployee($userId) {
    global $conn; // Access to the global connection variable
    $approvalSql = "UPDATE approval_tb SET status = 'approved' WHERE user_id = ?";
    $approvalStmt = $conn->prepare($approvalSql);
    $approvalStmt->bind_param("i", $userId);
    $approvalStmt->execute();

    // Add logic here to update privileges in the customers_tb table if necessary
    // This may involve updating a "privileges" field in the customers_tb table

    $approvalStmt->close();
}
?>
