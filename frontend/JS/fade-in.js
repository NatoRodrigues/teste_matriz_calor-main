// script.js

document.addEventListener('DOMContentLoaded', function () {
    document.body.style.opacity = '0'; // Configurar opacidade do corpo para 0

    // Exibir o corpo com transição suave
    setTimeout(function () {
        document.body.style.transition = 'opacity 0.5s ease-in-out'; // Adicionar transição suave
        document.body.style.opacity = '1'; // Configurar opacidade do corpo para 1
    }, 100); // Tempo em milissegundos

    // Exibir o banner após um atraso de 1 segundo
    setTimeout(function () {
        var banner = document.getElementById('banner');
        banner.style.display = 'block';
        banner.classList.add('fade-in');
    }, 1000); // Tempo em milissegundos
});


// Adicione um atraso maior para o card "Sobre o Sensor"
const sensorCard = document.getElementById('sensorCard');
setTimeout(() => {
    sensorCard.classList.remove('hidden');
    sensorCard.classList.add('fade-in');
}, 2000); // Atraso de 2000 milissegundos (2 segundos)
