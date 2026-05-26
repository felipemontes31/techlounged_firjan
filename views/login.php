<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login de Usuário</title>
  <link rel="stylesheet" href="../style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      
      <h2>Login</h2>
      <p>Insira suas credenciais para acessar o sistema.</p>
      
      <div id="errorMsg" style="color: #d32f2f; margin-bottom: 15px; font-size: 14px; font-weight: bold; text-align: center;"></div>

      <form action="../services/auth/login.php" method="POST" id="loginForm" class="login-form">
        
        <div class="form-group mb-3">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Email" required autocomplete="email">
        </div>

        <div class="form-group mb-3">
          <label for="senha">Senha</label>
          <input type="password" id="senha" name="senha" class="form-control" placeholder="Senha" required autocomplete="current-password">
        </div>

        <div class="d-grid gap-2 mb-3">
          <button type="submit" class="btn btn-primary w-100">
              Entrar
          </button>
        </div>

        <hr> <div class="d-flex justify-content-between mt-3">
          <a href="techlounged_firjan/views/registros.php" class="btn btn-success">
              Cadastrar Usuário
          </a>

          <a href="techlounged_firjan/index.php" class="btn btn-secondary">
              ← Voltar
          </a>
        </div> 

      </form>
    </div>
  </div>

  <script>
    const loginForm = document.getElementById('loginForm');
    const errorMsg = document.getElementById('errorMsg');

    loginForm.addEventListener('submit', function(event) {
      const email = document.getElementById('email').value.trim();
      const senha = document.getElementById('senha').value.trim();

      // Validação simples no Front-end antes de enviar ao PHP
      if (email === "" || senha === "") {
        event.preventDefault(); // Impede o envio se houver campo vazio
        errorMsg.textContent = "Por favor, preencha todos os campos!";
        return;
      }
    });
  </script>
</body>
</html>