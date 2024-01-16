<?php
// Conectar ao banco de dados (certifique-se de ter o arquivo de conexão incluído)
include('conn.php');

if (isset($_POST['reject'])) {
    $cid = $_POST['cid'];

    // Remover o registro da tabela approval_tb
    $deleteSql = "DELETE FROM approval_tb WHERE cid = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $cid);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Atualizar o tipo na tabela customers_tb para 'user'
    $updateUserSql = "UPDATE customers_tb SET type = 'user' WHERE cid = ?";
    $updateUserStmt = $conn->prepare($updateUserSql);
    $updateUserStmt->bind_param("i", $cid);
    $updateUserStmt->execute();
    $updateUserStmt->close();

    // Redirecionar para a página de lista de usuários pendentes (ou outra página)
    header("Location: list_pending_employees_content.php");
    exit();
}
?>
