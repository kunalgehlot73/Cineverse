const heroSection = document.querySelector('.hero-section');
const slider = document.querySelector('.slider');
const slides = document.querySelectorAll('.slide');
const previous = document.querySelector('#prev-slide');
const next = document.querySelector('#next-slide');
let currentIndex = 0;

function showSlide() {
    if (currentIndex >= slides.length) currentIndex = 0;
    if (currentIndex < 0) currentIndex = slides.length - 1;
    slides.forEach(slide => slide.classList.remove('active'));
    slides[currentIndex].classList.add('active');
    slider.style.transform = `translateX(${50 - (currentIndex * 100)}%)`;
}
function nextSlide(){
    currentIndex++;
    showSlide();
}
function prevSlide(){
    currentIndex--;
    showSlide();
}

let slideInterval = setInterval(nextSlide, 3000);

heroSection.addEventListener('mouseenter', () => clearInterval(slideInterval));
heroSection.addEventListener('mouseleave', () => {
    slideInterval = setInterval(nextSlide, 3000);
});
previous.addEventListener('click', ()=>{
    clearInterval(slideInterval);
    prevSlide();
});
next.addEventListener('click', ()=>{
    clearInterval(slideInterval);
    nextSlide();
});

showSlide();
