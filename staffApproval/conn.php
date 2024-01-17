<?php
$servername = "localhost";  // substitua pelo seu endereço do servidor (se não estiver usando localhost)
$username = "root";  // substitua pelo nome de usuário do MySQL/MariaDB
$password = "";    // substitua pela senha do MySQL/MariaDB
$database = "joao_db";       // substitua pelo nome da sua base de dados

// Tenta estabelecer a conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Não feche a conexão aqui, mantenha-a aberta para ser usada em outros lugares do programa
?>
