CREATE DATABASE amg8833;
USE amg8833;

CREATE TABLE dados_amg8833 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature_pixels FLOAT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE dados_amg8833
MODIFY COLUMN temperature_pixels JSON;

