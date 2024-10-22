class Carousel {
  constructor(carouselElement) {
    // DOM Elements
    this.carousel = carouselElement;
    this.carouselInner = this.carousel.querySelector(".carousel-inner");
    this.carouselItems = this.carousel.querySelectorAll(".carousel-item");
    this.prevButton = this.carousel.querySelector(".prev");
    this.nextButton = this.carousel.querySelector(".next");
    this.indicatorsContainer = this.carousel.querySelector(
      ".carousel-indicators"
    );

    // State
    this.currentIndex = 0;

    // Bind methods to maintain 'this' context
    this.updateCarousel = this.updateCarousel.bind(this);
    this.updateIndicators = this.updateIndicators.bind(this);
    this.createIndicators = this.createIndicators.bind(this);
    this.goToPrev = this.goToPrev.bind(this);
    this.goToNext = this.goToNext.bind(this);

    // Initialize
    this.init();
  }

  init() {
    // Set up event listeners
    this.prevButton.addEventListener("click", this.goToPrev);
    this.nextButton.addEventListener("click", this.goToNext);

    // Create and update initial state
    this.createIndicators();
    this.updateCarousel();
  }

  updateCarousel() {
    this.carouselInner.style.transform = `translateX(-${
      this.currentIndex * 100
    }%)`;
    this.updateIndicators();
  }

  updateIndicators() {
    const indicators = this.indicatorsContainer.querySelectorAll(
      ".carousel-indicator"
    );
    indicators.forEach((indicator, index) => {
      indicator.classList.toggle("active", index === this.currentIndex);
    });
  }

  createIndicators() {
    for (let i = 0; i < this.carouselItems.length; i++) {
      const indicator = document.createElement("div");
      indicator.classList.add("carousel-indicator");

      // Use arrow function to maintain 'this' context
      indicator.addEventListener("click", () => {
        this.goToSlide(i);
      });

      this.indicatorsContainer.appendChild(indicator);
    }
  }

  goToPrev() {
    this.currentIndex =
      (this.currentIndex - 1 + this.carouselItems.length) %
      this.carouselItems.length;
    this.updateCarousel();
  }

  goToNext() {
    this.currentIndex = (this.currentIndex + 1) % this.carouselItems.length;
    this.updateCarousel();
  }

  goToSlide(index) {
    this.currentIndex = index;
    this.updateCarousel();
  }
}

// Usage
document.addEventListener("DOMContentLoaded", () => {
  // Initialize all carousels on the page
  const carousels = document.querySelectorAll(".carousel");
  carousels.forEach((carouselElement) => {
    new Carousel(carouselElement);
  });
});
