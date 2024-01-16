<?php
// Conectar ao banco de dados (certifique-se de ter o arquivo de conexão incluído)
include('conn.php');

// Consulta para obter a lista de usuários pendentes
$query = "SELECT customers_tb.cid, customers_tb.fname, customers_tb.lname, approval_tb.status
          FROM customers_tb
          JOIN approval_tb ON customers_tb.cid = approval_tb.cid
          WHERE approval_tb.status = 'pending'";
$result = $conn->query($query);

// Início do HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Usuários Pendentes</title>
</head>
<body>

<?php
// Verificar se há resultados
if ($result->num_rows > 0) {
    echo "<h2>Usuários Pendentes de Aprovação</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nome</th><th>Status</th><th>Aprovar</th></tr>";

// ... (seu código existente) ...


while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['fname']} {$row['lname']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "<td>
            <form method='post' action='approve_employee.php'>
                <input type='hidden' name='cid' value='{$row['cid']}'>
                <button type='submit' name='approve' style='height: 20px; display: block; margin-bottom: 5px;'>Approve</button>
            </form>
          </td>";
    echo "<td>
            <form method='post' action='reject_user.php'>
                <input type='hidden' name='cid' value='{$row['cid']}'>
                <button type='submit' name='reject' style='height: 20px;'>Reject</button>
            </form>
          </td>";
    echo "</tr>";
}


// ... (seu código existente) ...

    echo "</table>";
} else {
    echo "<p>Nenhum usuário pendente de aprovação.</p>";
}

// Fechar a conexão com o banco de dados
$conn->close();
?>

</body>
</html>
