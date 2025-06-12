document.addEventListener('DOMContentLoaded', function() {
    const slidesContainer = document.querySelector('.slides');
    const slides = document.querySelectorAll('.slide');
    let currentIndex = 0;
    const totalSlides = slides.length;

    function goToSlide(index) {
        slidesContainer.style.transition = 'transform 1s ease-in-out';
        slidesContainer.style.transform = `translateX(-${index * 100}%)`;
        currentIndex = index;
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        goToSlide(currentIndex);
    }

    // Inicia o slider
    setInterval(nextSlide, 3000); // Muda a cada 3 segundos

    // Pré-carrega as imagens para evitar flashes
    slides.forEach(slide => {
        const img = new Image();
        img.src = slide.querySelector('img').src;
    });
});