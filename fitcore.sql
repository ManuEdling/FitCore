DROP DATABASE IF EXISTS fitcore;
CREATE DATABASE fitcore;
USE fitcore;

-- 1. Usuarios (Personal ou Aluno)
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('personal', 'aluno') NOT NULL
);

-- 2. Vinculo de quem e aluno de qual personal
CREATE TABLE vinculo_personal_aluno (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_personal INT UNSIGNED NOT NULL,
    id_aluno INT UNSIGNED NOT NULL,
    FOREIGN KEY (id_personal) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_aluno) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. Lista de Exercicios (se o personal criar um novo, vincula o ID dele em criado_por)
CREATE TABLE exercicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    grupo_muscular VARCHAR(50) NOT NULL, -- Ex: Pernas, Peito, Posterior
    criado_por INT UNSIGNED NULL, -- NULL = exercicio padrao do sistema
    foto VARCHAR(255) NULL, -- Caminho da imagem, GIF ou vídeo do exercício
    FOREIGN KEY (criado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- 4. Treinos montados pelo Personal para o Aluno
CREATE TABLE treinos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_aluno INT UNSIGNED NOT NULL,
    id_personal INT UNSIGNED NOT NULL,
    titulo VARCHAR(100) NOT NULL, -- Ex: Perna, Posterior, Treino A
    observacoes TEXT NULL,
    FOREIGN KEY (id_aluno) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_personal) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 5. Exercicios colocados dentro de cada Treino
CREATE TABLE treino_exercicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_treino INT UNSIGNED NOT NULL,
    id_exercicio INT UNSIGNED NOT NULL,
    series INT NOT NULL DEFAULT 3,
    repeticoes VARCHAR(30) NOT NULL, -- Ex: 10-12, Ate a falha
    descanso_segundos INT DEFAULT 60,
    ordem INT DEFAULT 1,
    FOREIGN KEY (id_treino) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_exercicio) REFERENCES exercicios(id) ON DELETE CASCADE
);

-- 6. Carga anotada pelo Aluno no exercicio
CREATE TABLE registro_cargas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_aluno INT UNSIGNED NOT NULL,
    id_treino_exercicio INT UNSIGNED NOT NULL,
    carga_kg DECIMAL(5,2) NOT NULL,
    data_registro DATE NOT NULL,
    FOREIGN KEY (id_aluno) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_treino_exercicio) REFERENCES treino_exercicios(id) ON DELETE CASCADE
);