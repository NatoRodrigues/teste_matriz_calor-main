#include <WiFi.h>
#include <WiFiClient.h>
#include <Adafruit_AMG88xx.h>

const char* ssid = "jrservice.net(sandra)9806-4567";  // Nome da rede Wi-Fi
const char* password = "15022000";
const char* server = "10.0.0.106"; // Endereço IP do servidor
const char* path = "/teste_temperatura_php/add.php";
const int port = 80; // Porta do servidor

// Inicializa o sensor AMG Adafruit
Adafruit_AMG88xx amg;

void setup() {
  Serial.begin(115200);
  delay(10);

  // Conectar-se à rede Wi-Fi
  Serial.println();
  Serial.println();
  Serial.print("Conectando a ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("Conectado à rede Wi-Fi");
  Serial.println("Endereço IP: ");
  Serial.println(WiFi.localIP());

  // Inicializar o sensor AMG Adafruit
  if (!amg.begin()) {
    Serial.println("Falha ao iniciar o sensor AMG");
    while (1);
  }
  Serial.println("Sensor AMG iniciado com sucesso!");
}

void loop() {
  // Ler os pixels do sensor AMG
  float pixels[AMG88xx_PIXEL_ARRAY_SIZE];
  amg.readPixels(pixels);

  // Calcular a temperatura média de todos os pixels
  float sum = 0;
  for (int i = 0; i < AMG88xx_PIXEL_ARRAY_SIZE; i++) {
    sum += pixels[i];
  }
  float temperature = sum / AMG88xx_PIXEL_ARRAY_SIZE;

  // Iniciar uma conexão cliente
  WiFiClient client;
  if (!client.connect(server, port)) {
    Serial.println("Falha ao conectar ao servidor");
    return;
  }

  // Construir a URL com os parâmetros (por exemplo: http://192.168.1.100/index.php?temperature=25.5)
  String url = "GET " + String(path) + "?temperature=" + String(temperature) + " HTTP/1.1\r\n" +  
               "Host: " + server + "\r\n" +
               "Connection: close\r\n\r\n";

  Serial.print("Requisição GET: ");
  Serial.println(url);

  // Enviar a requisição HTTP GET ao servidor
  client.print(url);

  // Esperar a resposta do servidor
  while (!client.available()) {
    delay(1000);
  }

  // Ler a resposta do servidor e imprimir no Monitor Serial
  while (client.available()) {
    String line = client.readStringUntil('\r');
    Serial.print(line);
  }

  Serial.println();
  Serial.println("Requisição completa");

  // Aguardar um intervalo antes de enviar a próxima requisição
  delay(10000);
}
