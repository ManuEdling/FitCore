# FitCore — Plataforma de Treinos e Evolução

## Sobre o projeto
O **FitCore** é uma aplicação web completa desenvolvida para gerenciar academias e estúdios de treinamento, otimizando a criação e execução de fichas de treino, o acompanhamento de cargas e a comunicação integrada entre alunos e personal trainers.

---

## 🛠️ Tecnologias Utilizadas
* **Backend:** PHP (com PDO para banco de dados seguro e gerenciamento de sessões)
* **Banco de Dados:** MySQL (estruturado com chaves estrangeiras e integridade referencial)
* **Frontend:** HTML5, CSS3 personalizado (tema escuro e detalhes em dourado) e JavaScript (para pré-visualização de mídias)
* **Ambiente de Desenvolvimento:** XAMPP (Apache/MySQL) e Visual Studio Code

---

## 🚀 Principais Funcionalidades Implementadas

### 1. Sistema de Autenticação e Perfis
* **Login e Cadastro Dinâmico:** Separado por tipos de usuário (`personal` e `aluno`), com senhas criptografadas (`password_hash`).
* **Controle de Acesso:** Proteção de rotas por sessão, garantindo que alunos acessem apenas o painel de alunos e personais apenas o painel administrativo.

### 2. Área do Personal Trainer
* **Vínculo de Alunos:** Sistema para conectar alunos cadastrados à carteira do personal.
* **Gerenciamento de Exercícios:** Cadastro de novos exercícios informando nome, grupo muscular e suporte a **upload de arquivos de mídia (imagens, GIFs e vídeos)** diretamente da galeria, com pré-visualização em tempo real e opção de exclusão.
* **Montagem de Fichas de Treino:** Criação de rotinas personalizadas para os alunos vinculados, especificando séries, repetições, tempo de descanso e ordenação.
* **Histórico de Cargas:** Visualização detalhada da evolução e progressão de carga registrada pelos alunos ao longo do tempo.
* **Gestão de Treinos:** Listagem organizada por aluno (com sistema de expansão/recolhimento) e opção de exclusão de fichas completas.

### 3. Área do Aluno
* **Dashboard de Treinos:** Visualização limpa e moderna de todas as fichas de treino liberadas pelo personal trainer, incluindo observações e recomendações.
* **Execução de Treino Interativa:** 
  * Exibição das mídias explicativas (fotos/vídeos) cadastradas para cada exercício.
  * Visualização automática da **última carga registrada** anteriormente para servir de comparativo.
  * Campo rápido para anotação da carga atual em quilogramas (kg) a cada sessão.
* **Finalização de Treino:** Conclusão do ciclo de treino com feedback visual de sucesso.

---

## 📂 Estrutura do Banco de Dados (`fitcore`)
O sistema opera com um banco de dados relacional contendo as seguintes tabelas principais:
1. **`usuarios`** — Armazena os dados de cadastro e o tipo de conta (personal/aluno).
2. **`vinculo_personal_aluno`** — Relaciona quais alunos pertencem a qual personal.
3. **`exercicios`** — Catálogo de exercícios do sistema ou criados pelos personais, com suporte a mídias.
4. **`treinos`** — Cabeçalhos das fichas de treino associadas aos alunos.
5. **`treino_exercicios`** — Associação dos exercícios às fichas, definindo séries, repetições e descanso.
6. **`registro_cargas`** — Histórico de pesos (kg) levantados pelos alunos em cada exercício por data.

---

## ⚙️ Como Executar o Projeto Localmente

1. Certifique-se de ter o **XAMPP** instalado e com os módulos **Apache** e **MySQL** ativados.
2. Clone ou coloque a pasta **`FitCore`** dentro do diretório padrão do servidor local (`C:\xampp\htdocs\`).
3. Crie uma pasta chamada **`uploads`** dentro do diretório `FitCore` para armazenar as mídias dos exercícios.
4. Abra o **MySQL Workbench** ou o **phpMyAdmin**, crie e execute o script do banco de dados `fitcore` (incluído no arquivo `.sql` do projeto).
5. Abra o navegador e acesse:
   `http://localhost/FitCore/`