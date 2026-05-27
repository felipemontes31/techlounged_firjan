<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login de Usuário</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="../style.css">
  
  <style>
    /* CSS exclusivo desta tela para garantir o posicionamento do Card de Login */
    .login-container {
      min-height: calc(100vh - 40px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .login-card {
      background-color: var(--bg-card);
      padding: 40px;
      border-radius: var(--radius);
      border: 1px solid var(--gray-slate);
      box-shadow: var(--shadow);
      max-width: 420px;
      width: 100%;
    }
    .login-card h2 {
      color: var(--blue-institutional);
      margin-bottom: 8px;
      text-align: center;
      font-weight: bold;
    }
    .login-card p {
      color: var(--text-muted);
      font-size: 14px;
      margin-bottom: 24px;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      
      <h2>Login</h2>
      <p>Insira suas credenciais para acessar o sistema.</p>
      
      <div id="errorMsg" style="color: var(--error-color); margin-bottom: 15px; font-size: 14px; font-weight: bold; text-align: center;"></div>

      <form action="../services/auth/login.php" method="POST" id="loginForm" class="form-container">
        
        <div class="form-group mb-3">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="Seu e-mail corporativo" required autocomplete="email">
        </div>

        <div class="form-group mb-3">
          <label for="senha">Senha</label>
          <input type="password" id="senha" name="senha" placeholder="Sua senha secreta" required autocomplete="current-password">
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn-submit w-100">Entrar</button>
        </div>

        <hr> 
        
        <div class="d-flex justify-content-between gap-2 mt-3">
          <a href="cadastro.php" class="btn-back" style="border-color: var(--blue-dynamic); color: var(--blue-dynamic); text-align: center; flex: 1;">
              Cadastrar
          </a>
          <a href="../index.php" class="btn-back" style="text-align: center; flex: 1;">
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

      if (email === "" || senha === "") {
        event.preventDefault(); 
        errorMsg.textContent = "Por favor, preencha todos os campos!";
        return;
      }
    });
  </script>
</body>
</html>