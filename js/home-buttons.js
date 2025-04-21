// Redirection des boutons de la page d'accueil

document.addEventListener('DOMContentLoaded', () => {
    // Bouton Médical
    const medicButton = document.querySelector('.medic-home-button');
    if (medicButton) {
      medicButton.addEventListener('click', () => {
        window.location.href = 'pages/medic/levels.html';
      });
    }
  
    // Bouton Incendie
    const fireButton = document.querySelector('.fire-home-button');
    if (fireButton) {
      fireButton.addEventListener('click', () => {
        window.location.href = 'pages/fire/levels.html';
      });
    }
  
    // Bouton 1722
    const oneSevenTwoTwoButton = document.querySelector('.one-seven-two-two-home-button');
    if (oneSevenTwoTwoButton) {
      oneSevenTwoTwoButton.addEventListener('click', () => {
        window.location.href = 'pages/one-seven-two-two/levels.html';
      });
    }
  
    // Bouton Prévention
    const preventButton = document.querySelector('.prevent-home-button');
    if (preventButton) {
      preventButton.addEventListener('click', () => {
        window.location.href = 'pages/prevent/levels.html';
      });
    }
  });
  