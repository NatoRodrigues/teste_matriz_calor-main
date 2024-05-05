<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Calor Infravermelho</title>
    <!-- Inclua a biblioteca Plotly.js -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <!-- Inclua a biblioteca Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Inclua o Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <!-- Adicione o arquivo JavaScript -->
    <script src="./JS/fade-in.js"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <!-- Adicione o favicon -->
    <link rel="icon" href="assets/images/favicon/noun-infrared-852813.png" type="image/x-icon">
    <style>
        .chart-container {
            width: 100%;
            max-width: 600px; /* Defina o tamanho máximo para o gráfico */
            height: 400px; /* Ajuste a altura conforme necessário */
            margin: 0 auto; /* Centralize o contêiner */
        }
    </style>
    <style>
        /* Estilos CSS para o banner */
        .banner {
    display: none; /* Inicialmente, o banner está oculto */
}

.banner.fade-in {
    display: block; /* Quando a classe fade-in é adicionada, o banner é exibido */
    animation: fadeIn 0.5s ease-in-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    </style>
</head>

<body style="background-color: #f3f4f6; font-family: Arial, sans-serif;">
    <!-- Navbar -->
    
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="assets/images/infrared.png" alt="Logo" class="h-8 mr-2">
                <a href="#" class="text-white font-bold text-xl font-sans">Mapa de Calor</a>
            </div>
            <button id="menuButton" class="text-white md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
            <ul id="menu" class="hidden md:flex space-x-4">
                <li><a href="index.php" class="text-white font-sans">Home</a></li>
                <li><a href="sobre.html" class="text-white font-sans">Sobre</a></li>
                <li><a href="contato.html" class="text-white font-sans">Contato</a></li>
            </ul>
        </div>
    </nav>

    <!-- Container para os gráficos -->
    <div class="container mx-auto mt-8">
        <!-- Banner -->
        <div id="banner" class="hidden bg-yellow-500 p-4 shadow-md rounded-md mb-8">
            <!-- Conteúdo do banner -->
            <h2 class="text-lg font-bold mb-2">🚨 Monitoramento em tempo real 🚨</h2>
            <p class="text-gray-700">Aproxime alguma parte do seu corpo ao sensor e verique o mapa abaixo.</p>
        </div>

        <!-- Gráfico de Mapa de Calor -->
        <div id="heatmapChart" class="chart-container"></div>
        <!-- Gráfico de Variação de Temperatura -->
        <div id="temperatureChart" class="chart-container"></div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 Mapa de Calor Infravermelho. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Script para os gráficos -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var button = document.querySelector('button');
            var menu = document.querySelector('ul');

            button.addEventListener('click', function () {
                menu.classList.toggle('hidden');
            });
        });

        // Função para atualizar o heatmap com os dados recebidos
        function updateHeatmap(data) {
            // Converter os dados recebidos em uma matriz bidimensional de 8x8
            var matrixData = [];
            for (var i = 0; i < data.length; i += 8) {
                matrixData.push(data.slice(i, i + 8));
            }

            // Definir o layout
            var layout = {
                title: 'Mapa de Calor Infravermelho',
                xaxis: {
                    title: 'Linhas', // Alterar o título do eixo x para 'Linhas'
                    side: 'bottom' // Posicionar o título do eixo x na parte inferior
                },
                yaxis: {
                    title: 'Colunas', // Alterar o título do eixo y para 'Colunas'
                    autorange: 'reversed' // Inverter a direção do eixo y
                },
                margin: {
                    t: 60, // Adicionar espaço superior para o título
                    l: 60, // Adicionar espaço esquerdo
                    r: 60, // Adicionar espaço direito
                    b: 80 // Adicionar espaço inferior para o título do eixo x
                },
                font: { // Configurações de fonte
                    family: 'Arial, sans-serif', // Usar uma fonte sans-serif
                    size: 12, // Tamanho da fonte
                    color: '#333' // Cor da fonte
                }
            };

            // Plotar o heatmap com a matriz bidimensional
            Plotly.newPlot('heatmapChart', [{
                z: matrixData, // Usando a matriz bidimensional convertida
                type: 'heatmap',
                colorscale: 'Jet', // Esquema de cores semelhante ao infravermelho
                zsmooth: 'best', // Suavizar a visualização do heatmap
                zhoverformat: '.2f', // Formatar o valor ao passar o mouse sobre o heatmap
                colorbar: { // Configurações da barra de cores
                    title: 'Temperatura (°C)', // Título da barra de cores
                    titleside: 'right', // Posição do título da barra de cores
                    ticksuffix: '°C' // Sufixo para os valores da barra de cores
                }
            }], layout);
        }

        // Função para atualizar o gráfico de variação de temperatura com os dados recebidos
        function updateTemperatureChart(data) {
            // Dados de exemplo para o gráfico de variação de temperatura
            var temperatureData = [
                { x: [1, 2, 3, 4, 5], y: [20, 21, 22, 23, 24], type: 'scatter', mode: 'lines', name: 'Temperatura' }
            ];

            // Definir o layout do gráfico de variação de temperatura
            var layout = {
                title: 'Variação de Temperatura',
                xaxis: { title: 'Tempo (minutos)' },
                yaxis: { title: 'Temperatura (°C)' },
                margin: { t: 60, l: 60, r: 60, b: 80 },
                font: { family: 'Arial, sans-serif', size: 12, color: '#333' }
            };

            // Plotar o gráfico de variação de temperatura
            Plotly.newPlot('temperatureChart', temperatureData, layout);
        }

        // Função para fazer a requisição AJAX ao servidor PHP usando Axios
        var heatmapData = null;

        // Função para fazer a requisição AJAX ao servidor PHP usando Axios
        function requestDataFromServer() {
            // Fazer a requisição GET para o backend
            axios.get('../backend/backend.php')
                .then(response => {
                    if (response.data.error) {
                        console.error('Erro:', response.data.error);
                    } else {
                        console.log('Dados recebidos:', response.data); // Verifique os dados recebidos no console
                        heatmapData = response.data;
                        updateHeatmap(heatmapData); // Atualizar o heatmap com os novos dados
                        updateTemperatureChart(heatmapData); // Atualizar o gráfico de variação de temperatura
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        }

        // Iniciar a requisição de dados quando a página carregar
        requestDataFromServer();

        // Atualizar os gráficos periodicamente
        setInterval(requestDataFromServer, 10000); // A cada 10 segundos (10000 milissegundos)
    </script>
</body>

</html>
