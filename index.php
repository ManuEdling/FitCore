<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCore - Login e Cadastro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <div class="tabs">
        <div class="tab active" onclick="switchTab('login')">Entrar</div>
        <div class="tab" onclick="switchTab('cadastro')">Cadastrar</div>
    </div>

    <!-- MENSAGENS DE ERRO/SUCESSO -->
    <?php if(isset($_GET['erro'])): ?>
        <div class="msg"><?php echo htmlspecialchars($_GET['erro']); ?></div>
    <?php endif; ?>

    <!-- FORMULÁRIO DE LOGIN -->
    <div id="login-form" class="form-container active">
        <h2>Login FitCore</h2>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label>E-mail</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>

    <!-- FORMULÁRIO DE CADASTRO -->
    <div id="cadastro-form" class="form-container">
        <h2>Criar Conta</h2>
        <form action="cadastro.php" method="POST">
            <div class="input-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" required>
            </div>
            <div class="input-group">
                <label>E-mail</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            <div class="input-group">
                <label>Tipo de Conta</label>
                <select name="tipo" required>
                    <option value="aluno">Aluno</option>
                    <option value="personal">Personal Trainer</option>
                </select>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
    </div>
</div>

<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.form-container').forEach(f => f.classList.remove('active'));
        
        if(tabName === 'login') {
            document.querySelectorAll('.tab')[0].classList.add('active');
            document.getElementById('login-form').classList.add('active');
        } else {
            document.querySelectorAll('.tab')[1].classList.add('active');
            document.getElementById('cadastro-form').classList.add('active');
        }
    }
</script>

</body>
</html>