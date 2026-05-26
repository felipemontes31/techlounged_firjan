<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eventos - Midiateca - Firjan - Sapucai</title>
  <link rel = "steelheet" href="techlounged_firjan/style.css">
</head>
<body>
  <header>
    <h1>Eventos em Destaque</h1>
    <a href="views/login.php" class="login-btn">Login</a>
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
      <option value="">Espaço</option>
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
        <img src="imagem/muraldigital.jpeg" alt="Mural Digital">
        <div class="card-content">
          <h2>Mural Digital</h2>
          <p class="date">12 de Junho, 2026</p>
          <p>Mural virtual abordando presença feminina na tecnologia.</p>
          <a href="#" class="btn">Ver Detalhes</a>
        </div>
      </div>

      <div class="card">
        <img src="imagem/biblioteca.jpeg" alt="De Frente com a Biblio">
        <div class="card-content">
          <h2>De Frente com a Biblio</h2>
          <p class="date">20 de Julho, 2026</p>
          <p>Apresentação sobre o uso do espaço da biblioteca e suas potencialidades.</p>
          <a href="#" class="btn">Ver Detalhes</a>
        </div>
      </div>

      <div class="card">
        <img src="imagem/projetointegrador.jpeg" alt="Projeto Integrador">
        <div class="card-content">
          <h2>Projeto Integrador</h2>
          <p class="date">5 de Agosto, 2026</p>
          <p>Apresentar os objetivos e regras de P.I aos alunos de aprendizagem.</p>
          <a href="#" class="btn">Ver Detalhes</a>
        </div>
      </div>

      <div class="card">
        <img src="imagem/planoanual.jpeg" alt="Plano Atividades Anual">
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
