<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eventos - Midiateca - Firjan - Sapucai</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #6d9eba;
      color: white;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
    }

    .login-btn {
      background-color: white;
      color: #6d9eba;
      padding: 10px 15px;
      border-radius: 4px;
      text-decoration: none;
      font-weight: bold;
    }

    .login-btn:hover {
      background-color: #e1bee7;
    }

    .search-bar {
      background-color: #fff;
      padding: 15px;
      display: flex;
      gap: 10px;
      justify-content: center;
      flex-wrap: wrap;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .search-bar input, .search-bar select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    .search-bar button {
      padding: 10px 15px;
      background-color: #6d9eba;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .search-bar button:hover {
      background-color: #6a1b9a;
    }

    /* Carrossel */
    .carousel {
      position: relative;
      max-width: 1000px;
      margin: 40px auto;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .carousel-track {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }

    .card {
      min-width: 100%;
      box-sizing: border-box;
      background-color: white;
      border-radius: 10px;
      overflow: hidden;
    }

    .card img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }

    .card-content {
      padding: 20px;
    }

    .card-content h2 {
      font-size: 24px;
      margin: 0 0 10px;
      color: #333;
    }

    .card-content p {
      font-size: 16px;
      color: #555;
      margin: 5px 0;
    }

    .card-content .date {
      font-weight: bold;
      color: #4a148c;
    }

    .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 12px 20px;
      background-color: #4a148c;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-size: 16px;
    }

    .btn:hover {
      background-color: #6a1b9a;
    }

    .carousel-button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(74,20,140,0.7);
      border: none;
      color: white;
      font-size: 30px;
      padding: 10px;
      cursor: pointer;
      border-radius: 50%;
    }

    .carousel-button.left {
      left: 15px;
    }

    .carousel-button.right {
      right: 15px;
    }

    .carousel-button:hover {
      background-color: rgba(106,27,154,0.9);
    }
  </style>
</head>
<body>
  <header>
    <h1>Eventos em Destaque</h1>
    <a href="login.html" class="login-btn">Login</a>
  </header>

  <div class="search-bar">
    <input type="text" placeholder="Buscar eventos...">
    <select>
      <option value="">Categoria</option>
      <option value="musica">Música</option>
      <option value="teatro">Teatro</option>
      <option value="tecnologia">Tecnologia</option>
      <option value="gastronomia">Gastronomia</option>
    </select>
    <select>
      <option value="">Localização</option>
      <option value="miateca">Midiateca</option>
      <option value="auditorio">Auditório</option>
      <option value="online">Online</option>
    </select>
    <button>Filtrar</button>
  </div>

  <!-- Carrossel -->
  <div class="carousel">
    <div class="carousel-track">
      <div class="card">
        <img src="imagens/muraldigital.jpg" alt="Mural Digital">
        <div class="card-content">
          <h2>Mural Digital</h2>
          <p class="date">12 de Junho, 2026</p>
          <p>Mural virtual abordando presença feminina na tecnologia.</p>
          <a href="#" class="btn">Ver Detalhes</a>
        </div>
      </div>

      <div class="card">
        <img src="imagens/biblioteca.jpeg" alt="De Frente com a Biblio">
        <div class="card-content">
          <h2>De Frente com a Biblio</h2>
          <p class="date">20 de Julho, 2026</p>
          <p>Apresentação sobre o uso do espaço da biblioteca e suas potencialidades.</p>
          <a href="#" class="btn">Ver Detalhes</a>
        </div>
      </div>

      <div class="card">
        <img src="imagens/projetointegrador.jpg" alt="Projeto Integrador">
        <div class="card-content">
          <h2>Projeto Integrador</h2>
          <p class="date">5 de Agosto, 2026</p>
          <p>Apresentar os objetivos e regras de P.I aos alunos de aprendizagem.</p>
          <a href="#" class="btn">Ver Detalhes</a>
        </div>
      </div>

      <div class="card">
        <img src="imagens/planoanual.jpg" alt="Plano Atividades Anual">
        <div class="card-content">
          <h2>Plano de Atividades Anual</h2>
          <p class="date">15 de Setembro, 2026</p>
          <p>Atividades a serem desenvolvidas no espaço da Midiateca ao longo do ano.</p>
          <a href="#" class="btn">Ver Detalhes</a>
        </div>
      </div>
    </div>

    <button class="carousel-button left">&#10094;</button>
    <button class="carousel-button right">&#10095;</button>
  </div>

  <script>
    const track = document.querySelector('.carousel-track');
    const cards = Array.from(track.children);
    const nextButton = document.querySelector('.carousel-button.right');
    const prevButton = document.querySelector('.carousel-button.left');
    let currentIndex = 0;

    function updateCarousel() {
      track.style.transform = 'translateX(-' + currentIndex * 100 + '%)';
    }

    nextButton.addEventListener('click', () => {
      currentIndex = (currentIndex + 1) % cards.length;
      updateCarousel();
    });

    prevButton.addEventListener('click', () => {
      currentIndex = (currentIndex - 1 + cards.length) % cards.length;
      updateCarousel();
    });

    // Passagem automática a cada 6 segundos
    setInterval(() => {
      currentIndex = (currentIndex + 1) % cards.length;
      updateCarousel();
    }, 6000);
  </script>
</body>
</html>
