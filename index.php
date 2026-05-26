<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eventos - Midiateca - Firjan - Sapucaí</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>Eventos em Destaque</h1>
    <a href="views/login.php" class="login-btn">Login</a>
  </header>

  <main>
    <section class="search-bar">
      <input type="text" placeholder="Buscar eventos...">
      
      <select name="categoria" aria-label="Selecionar Categoria">
        <option value="">Categoria</option>
        <option value="musica">Música</option>
        <option value="teatro">Teatro</option>
        <option value="tecnologia">Tecnologia</option>
        <option value="gastronomia">Gastronomia</option>
      </select> 
      
      <select name="espaco" aria-label="Selecionar Espaço">
        <option value="">Espaço</option>
        <option value="midiateca">Midiateca</option>
        <option value="auditorio">Auditório</option>
        <option value="online">Online</option>
      </select>
      
      <button type="button">Filtrar</button>
    </section>

    <section class="carousel" aria-label="Carrossel de Eventos">
      <div class="carousel-track">
        
        <article class="card">
          <img src="imagem/muraldigital.jpeg" alt="Mural Digital sobre presença feminina na tecnologia">
          <div class="card-content">
            <h2>Mural Digital</h2>
            <p class="date">12 de Junho, 2026</p>
            <p>Mural virtual abordando presença feminina na tecnologia.</p>
            <a href="#" class="btn">Ver Detalhes</a>
          </div>
        </article>

        <article class="card">
          <img src="imagem/biblioteca.jpeg" alt="Espaço da biblioteca da Midiateca">
          <div class="card-content">
            <h2>De Frente com a Biblio</h2>
            <p class="date">20 de Julho, 2026</p>
            <p>Apresentação sobre o uso do espaço da biblioteca e suas potencialidades.</p>
            <a href="#" class="btn">Ver Detalhes</a>
          </div>
        </article>

        <article class="card">
          <img src="imagem/projetointegrador.jpeg" alt="Apresentação do Projeto Integrador">
          <div class="card-content">
            <h2>Projeto Integrador</h2>
            <p class="date">5 de Agosto, 2026</p>
            <p>Apresentar os objetivos e regras de P.I aos alunos de aprendizagem.</p>
            <a href="#" class="btn">Ver Detalhes</a>
          </div>
        </article>

        <article class="card">
          <img src="imagem/planoanual.jpeg" alt="Cronograma do Plano de Atividades Anual">
          <div class="card-content">
            <h2>Plano de Atividades Anual</h2>
            <p class="date">15 de Setembro, 2026</p>
            <p>Atividades a serem desenvolvidas no espaço da Midiateca ao longo do ano.</p>
            <a href="#" class="btn">Ver Detalhes</a>
          </div>
        </article>

      </div>

      <button class="carousel-button left" aria-label="Slide anterior">&#10094;</button>
      <button class="carousel-button right" aria-label="Próximo slide">&#10095;</button>
    </section>
  </main>

  <script>
    const track = document.querySelector('.carousel-track');
    const cards = Array.from(track.children);
    const nextButton = document.querySelector('.carousel-button.right');
    const prevButton = document.querySelector('.carousel-button.left');
    let currentIndex = 0;
    let autoSlideInterval;

    function updateCarousel() {
      track.style.transform = `translateX(-${currentIndex * 100}%)`;
    }

    // Função para avançar o slide
    function nextSlide() {
      currentIndex = (currentIndex + 1) % cards.length;
      updateCarousel();
    }

    // Função para voltar o slide
    function prevSlide() {
      currentIndex = (currentIndex - 1 + cards.length) % cards.length;
      updateCarousel();
    }

    // Gerencia o timer automático (evita bugs quando o usuário clica manualmente)
    function startAutoSlide() {
      stopAutoSlide(); // Garante que não haverá múltiplos timers rodando
      autoSlideInterval = setInterval(nextSlide, 6000);
    }

    function stopAutoSlide() {
      clearInterval(autoSlideInterval);
    }

    // Eventos dos botões com reset de timer para melhor UX
    nextButton.addEventListener('click', () => {
      nextSlide();
      startAutoSlide(); 
    });

    prevButton.addEventListener('click', () => {
      prevSlide();
      startAutoSlide();
    });

    // Inicia o carrossel automático ao carregar a página
    startAutoSlide();
  </script>
</body>
</html>