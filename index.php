<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Cidades</title>
    <link rel="stylesheet" href="style.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
</head>
<style>
    .titulo{
    color: #535353;
    margin-bottom: 25px;
    text-align: center;
    font-weight: bold;
    font-size: 50px;

    animation: fadeIn 2s;

}
.search-bar{
    animation:fadeIn 2s;
}

</style>


<?php
    session_start();
    $_SESSION['InserirCidade'] = null;


?>
<body>
    <div class="container-all">
    <nav class="titulo">Bem-vindo ao Vitra Air</nav>
        <div class="search-bar">
        <form method="post" action="result.php">
                <input 
                    type="text" 
                    name="InserirCidade" 
                    placeholder="Pesquisar cidade..." 
                    required
                >
            </form>
        </div>
    </div>
</body>
</html>