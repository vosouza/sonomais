let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("carousel-slide");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slides[slideIndex-1].style.display = "block";
}

// Inicializar Simple Lightbox
document.addEventListener('DOMContentLoaded', () => {
    const lightbox = new SimpleLightbox('.carousel-slide a');
});

// Adicionar funcionalidade ao botão WhatsApp (substitua o link pelo seu número)
const whatsappButton = document.querySelector('.whatsapp-button');
if (whatsappButton) {
    whatsappButton.addEventListener('click', () => {
        window.open('https://wa.me/SEUNUMERODOTELEFONE?text=Ol%C3%A1,%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20produto.', '_blank');
    });
}