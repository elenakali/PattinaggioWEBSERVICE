CREATE DATABASE db_pattinaggio;
USE db_pattinaggio;

CREATE TABLE corsi_pattini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    specialita VARCHAR(100) NOT NULL,
    livello VARCHAR(50) NOT NULL,
    orario VARCHAR(100) NOT NULL,
    posti_liberi INT NOT NULL
);

CREATE TABLE iscrizioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_corso INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    data_operazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_corso) REFERENCES corsi_pattini(id)
);

INSERT INTO corsi_pattini (specialita, livello, orario, posti_liberi) VALUES 
('Pattinaggio Artistico', 'Principianti', 'Lunedì 17:00', 15),
('Roller Freestyle', 'Avanzato', 'Mercoledì 19:30', 5),
('Corsa In Linea', 'Intermedio', 'Venerdì 18:00', 0);
