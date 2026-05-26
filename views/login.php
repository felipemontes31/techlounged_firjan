<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login de Usuário</title>
  <style>
     
    
    /* login */
     body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #4a148c, #6a1b9a);
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      width: 350px;
      text-align: center;
    }

    .login-container h2 {
      margin-bottom: 20px;
      color: #4a148c;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      background-color: #4a148c;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
    }

    .login-container button:hover {
      background-color: #6a1b9a;
    }

    .login-container a {
      display: block;
      margin-top: 15px;
      font-size: 14px;
      color: #4a148c;
      text-decoration: none;
    }

    .login-container a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
   <form action="../services/auth/login.php" method="POST">

    <input type="email" name="email" placeholder="Email">

    <input type="password" name="senha" placeholder="Senha">

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
    // Exemplo simples de validação
    const loginForm = document.getElementById('loginForm');
    const errorMsg = document.getElementById('errorMsg');

    loginForm.addEventListener('submit', function(event) {
      event.preventDefault(); // evita recarregar a página

      const usuario = document.getElementById('usuario').value;
      const senha = document.getElementById('senha').value;

      // Aqui você define os usuários válidos
      const usuarioCorreto = "admin";
      const senhaCorreta = "12345";

      if (usuario === usuarioCorreto && senha === senhaCorreta) {
        // Redireciona para a página de atividades
        window.location.href = "atividades.html";
      } else {
        errorMsg.textContent = "Usuário ou senha incorretos!";
      }
    });
  </script>
</body>
</html>
