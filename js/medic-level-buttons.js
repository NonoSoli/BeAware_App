$(document).ready(function () {
    // Clique sur bouton niveau 1
    $('.button-level-one').on('click', function () {
        $('.button-level-one').next('.rectangular-box-levels').show();
        $('.button-level-two').next('.rectangular-box-levels').hide();
        $('.button-level-three').next('.rectangular-box-levels').hide();
    });

    // Clique sur bouton niveau 2
    $('.button-level-two').on('click', function () {
        $('.button-level-two').next('.rectangular-box-levels').show();
        $('.button-level-one').next('.rectangular-box-levels').hide();
        $('.button-level-three').next('.rectangular-box-levels').hide();
    });

    // Clique sur bouton niveau 3
    $('.button-level-three').on('click', function () {
        $('.button-level-three').next('.rectangular-box-levels').show();
        $('.button-level-one').next('.rectangular-box-levels').hide();
        $('.button-level-two').next('.rectangular-box-levels').hide();
    });

    $('.player-level-one').on('click', function () {
        window.location.href = 'exercice.html';
    });

    $('.player-level-two').on('click', function () {
        window.location.href = '../../home.html';
    });

    $('.player-level-three').on('click', function () {
        window.location.href = '../../home.html';
    });
});
