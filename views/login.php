<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login de Usuário</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      
      <h2>Login</h2>
      <p>Insira suas credenciais para acessar o sistema.</p>
      
      <div id="errorMsg" style="color: #d32f2f; margin-bottom: 15px; font-size: 14px; font-weight: bold; text-align: center;"></div>

      <form action="../services/auth/login.php" method="POST" id="loginForm" class="login-form">
        
        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="Email" required autocomplete="email">
        </div>

        <div class="form-group">
          <label for="senha">Senha</label>
          <input type="password" id="senha" name="senha" placeholder="Senha" required autocomplete="current-password">
        </div>

    <button type="submit">
        Entrar
    </button>

 <div class="d-flex justify-content-between mt-4">
   
    <button type="submit" class="btn btn-success" >
        Cadastrar Usuário
    </button>

     <a href="techlounged_firjan/index.php" class="btn btn-secondary">
        ← Voltar
    </a>
</div> 


</form>
  </div>

  <script>
    const loginForm = document.getElementById('loginForm');
    const errorMsg = document.getElementById('errorMsg');

    loginForm.addEventListener('submit', function(event) {
      // Pegando os valores corretos pelos IDs ajustados
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