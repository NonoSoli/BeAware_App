document.addEventListener('DOMContentLoaded', () => {
    // Bouton back to home
    const backToHomeButton = document.querySelector('.back-to-home');
    if (backToHomeButton) {
        backToHomeButton.addEventListener('click', () => {
        window.location.href = 'https://funlab.be/BeAware/home.html';
      });
    }

    // Bouton back to levels
    const backToLevelsButton = document.querySelector('.back-to-levels');
    if (backToLevelsButton) {
        backToLevelsButton.addEventListener('click', () => {
        window.location.href = 'levels.html';
      });
    }
});