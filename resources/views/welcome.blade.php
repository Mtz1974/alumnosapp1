<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AlumnosApp</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

  <!-- Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-300 dark:bg-gray-900 text-gray-800 dark:text-white">
  <div class="min-h-screen flex flex-col items-center px-4 py-8">

    <!-- Logo UTN -->
    <div class="mb-6">
      <img src="{{ asset('/images/UTN-Resistencia.png') }}" alt="Logo UTN" class="w-40 mx-auto drop-shadow-lg">
    </div>

    <!-- Título -->
    <h1 class="text-4xl font-bold mb-4 text-center">📚 Bienvenido a AlumnosApp</h1>
    <p class="text-lg text-center max-w-2xl mb-8">
      Una herramienta diseñada para <strong>registrar y listar alumnos</strong> con sus perfiles y enlaces de contacto.
    </p>

    <!-- Botones -->
    <div class="flex gap-4 mb-10">
      @if (Route::has('login'))
        <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow transition">Iniciar Sesión</a>
        @if (Route::has('register'))
          <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg shadow transition">Registrarse</a>
        @endif
      @endif
    </div>

    <!-- Carrusel -->
    <div class="w-full max-w-5xl mx-auto px-4">
      <div id="carousel-root" class="relative rounded-xl overflow-hidden shadow-lg select-none">

        <!-- TRACK -->
        <div id="carousel-track" class="flex transition-transform duration-700 ease-in-out" style="transform: translateX(0%)">
          <!-- SLIDE 1 -->
          <div class="shrink-0 basis-full h-80 md:h-96 flex items-center justify-center p-2">
            <img src="{{ asset('/images/utn-1.jpeg') }}" alt="Instalaciones UTN" class="block w-full h-full object-contain">
          </div>
          <!-- SLIDE 2 -->
          <div class="shrink-0 basis-full h-80 md:h-96 flex items-center justify-center p-2">
            <img src="{{ asset('/images/utn-2.jpg') }}" alt="Laboratorio UTN" class="block w-full h-full object-contain">
          </div>
          <!-- SLIDE 3 -->
          <div class="shrink-0 basis-full h-80 md:h-96 flex items-center justify-center p-2">
            <img src="{{ asset('/images/utn-3.jpg') }}" alt="Aulas UTN" class="block w-full h-full object-contain">
          </div>
        </div>

        <!-- Flechas -->
        <button id="carousel-prev"
                class="z-10 absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-black/40 text-white px-3 py-2 backdrop-blur pointer-events-auto"
                aria-label="Anterior" type="button">‹</button>
        <button id="carousel-next"
                class="z-10 absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-black/40 text-white px-3 py-2 backdrop-blur pointer-events-auto"
                aria-label="Siguiente" type="button">›</button>

        <!-- Dots -->
        <div id="carousel-dots" class="z-10 absolute bottom-3 left-1/2 -translate-x-1/2 flex space-x-2"></div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-sm text-gray-500 mt-8">
      &copy; {{ date('Y') }} AlumnosApp - Universidad Tecnológica Nacional (FR Resistencia/Formosa)
    </footer>
  </div>

  <!-- Script carrusel (aislado y seguro) -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const root  = document.getElementById('carousel-root');
      const track = document.getElementById('carousel-track');
      const btnPrev = document.getElementById('carousel-prev');
      const btnNext = document.getElementById('carousel-next');
      const dotsContainer = document.getElementById('carousel-dots');

      if (!root || !track || !btnPrev || !btnNext || !dotsContainer) return;

      const slides = track.children.length;
      const intervalMs = 5000;
      let index = 0;
      let timer = null;

      // Crear dots
      for (let i = 0; i < slides; i++) {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', `Ir al slide ${i + 1}`);
        dot.className = "w-3 h-3 rounded-full bg-white/50 hover:bg-white transition outline-none ring-0";
        dot.addEventListener('click', () => { goTo(i); start(); });
        dotsContainer.appendChild(dot);
      }
      const dots = dotsContainer.children;

      function updateDots() {
        for (let i = 0; i < dots.length; i++) {
          dots[i].className = "w-3 h-3 rounded-full transition " + (i === index ? "bg-white" : "bg-white/50 hover:bg-white");
        }
      }

      function goTo(i) {
        index = (i + slides) % slides;
        track.style.transform = `translateX(-${index * 100}%)`;
        updateDots();
      }

      function start() {
        stop();
        timer = setInterval(() => goTo(index + 1), intervalMs);
      }

      function stop() {
        if (timer) clearInterval(timer);
        timer = null;
      }

      // Controles
      btnPrev.addEventListener('click', () => { goTo(index - 1); start(); });
      btnNext.addEventListener('click', () => { goTo(index + 1); start(); });

      // Pausa al pasar el mouse y cuando se oculta la pestaña
      root.addEventListener('mouseenter', stop);
      root.addEventListener('mouseleave', start);
      document.addEventListener('visibilitychange', () => { document.hidden ? stop() : start(); });

      // Iniciar
      goTo(0);
      start();
    });
  </script>
</body>
</html>
