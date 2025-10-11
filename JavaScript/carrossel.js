document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.querySelector('.promo-carousel');
  if (!carousel) return;

  const track = carousel.querySelector('.promo-track');
  const slides = Array.from(track.children);
  const dotsContainer = carousel.querySelector('.promo-dots');
  const intervalTime = 5000; // tempo entre slides em ms
  let currentIndex = 0;
  let interval;

  // Criar bolinhas (dots)
  slides.forEach((_, i) => {
    const dot = document.createElement('button');
    if (i === 0) dot.classList.add('active');
    dot.addEventListener('click', () => {
      currentIndex = i;
      updateCarousel();
      resetInterval();
    });
    dotsContainer.appendChild(dot);
  });

  // Atualiza slide e bolinhas
  function updateCarousel() {
    track.style.transform = `translateX(-${currentIndex * 100}%)`;
    dotsContainer.querySelectorAll('button').forEach((dot, idx) => {
      dot.classList.toggle('active', idx === currentIndex);
    });
  }

  // Próximo slide
  function nextSlide() {
    currentIndex = (currentIndex + 1) % slides.length;
    updateCarousel();
  }

  // Reseta intervalo automático (ex: após clique na bolinha)
  function resetInterval() {
    clearInterval(interval);
    interval = setInterval(nextSlide, intervalTime);
  }

  // Inicia o intervalo automático
  interval = setInterval(nextSlide, intervalTime);

  // Pausa ao passar o mouse e retoma ao sair
  carousel.addEventListener('mouseenter', () => clearInterval(interval));
  carousel.addEventListener('mouseleave', resetInterval);
});
