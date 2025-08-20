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
            Una herramienta diseñada para <strong>registrar y listar alumnos</strong> con sus perfiles y enlaces de contacto,
            facilitando a los profesores el acceso rápido a la información de sus estudiantes.
        </p>

        <!-- Botones de acceso -->
        <div class="flex gap-4 mb-10">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow transition">
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow transition">
                        Iniciar Sesión
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg shadow transition">
                            Registrarse
                        </a>
                    @endif
                @endauth
            @endif
        </div>

        <!-- Carrusel de imágenes -->
        <div class="relative w-full max-w-4xl overflow-hidden rounded-xl shadow-lg mb-8">
            <div id="carousel" class="flex transition-transform duration-700">
                <img src="{{ asset('/images/utn-1.jpeg') }}" class="w-full object-cover" alt="Instalaciones UTN">
                <img src="{{ asset('/images/utn-2.jpg') }}" class="w-full object-cover" alt="Laboratorio UTN">
                <img src="{{ asset('/images//utn-3.jpg') }}" class="w-full object-cover" alt="Aulas UTN">
            </div>
            <!-- Controles -->
            <button onclick="prevSlide()" class="absolute left-3 top-1/2 -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">‹</button>
            <button onclick="nextSlide()" class="absolute right-3 top-1/2 -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">›</button>
        </div>

        <!-- Pie de página -->
        <footer class="text-center text-sm text-gray-500 mt-8">
            &copy; {{ date('Y') }} AlumnosApp - Universidad Tecnológica Nacional (FR Resistencia/Formosa)
        </footer>
    </div>

    <!-- Script carrusel -->
<script>
  let currentIndex = 0;
  const carousel = document.getElementById('carousel');
  const slides = carousel.children;
  const totalSlides = slides.length;

  function showSlide(index) {
    carousel.style.transform = `translateX(-${index * 100}%)`;
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides;
    showSlide(currentIndex);
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
    showSlide(currentIndex);
  }

  // Auto-play cada 5 segundos
  setInterval(nextSlide, 5000);

  // Mostrar la primera imagen al cargar
  showSlide(currentIndex);
</script>
</body>
</html>

