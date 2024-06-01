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
    for (var i = 0; i < data.length - 1; i += 8) { // Exclui o último elemento que é a temperatura do termistor
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

// Variáveis para armazenar os dados de temperatura ao longo do tempo
var timeData = [];
var temperatureData = [];

// Função para atualizar o gráfico de variação de temperatura com os dados recebidos
function updateTemperatureChart(thermistor_temp) {
    // Remova esta linha: var thermistor_temp = ''; // Não é necessário definir aqui

    // Adicionar o novo valor de temperatura e o tempo correspondente
    var currentTime = new Date();
    timeData.push(currentTime);
    temperatureData.push(thermistor_temp);

    // Manter apenas os últimos 10 minutos de dados
    var maxTime = new Date(currentTime - 10 * 60000);
    while (timeData.length > 0 && timeData[0] < maxTime) {
        timeData.shift();
        temperatureData.shift();
    }


    // Dados para o gráfico de variação de temperatura
    var plotData = [{
        x: timeData,
        y: temperatureData,
        type: 'scatter',
        mode: 'lines',
        name: 'Temperatura do Termistor'
    }];

    // Definir o layout do gráfico de variação de temperatura
    var layout = {
        title: 'Variação de Temperatura do Termistor',
        xaxis: { title: 'Tempo' },
        yaxis: { title: 'Temperatura (°C)' },
        margin: { t: 60, l: 60, r: 60, b: 80 },
        font: { family: 'Arial, sans-serif', size: 12, color: '#333' }
    };

    // Plotar o gráfico de variação de temperatura
    Plotly.newPlot('temperatureChart', plotData, layout);
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
                updateTemperatureChart(thermistor_temp); // Atualizar o gráfico de variação de temperatura
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

// Iniciar a requisição de dados quando a página carregar
setInterval(requestDataFromServer, 5000); // 5000 milissegundos = 5 segundos

// Atualizar os gráficos periodicamente