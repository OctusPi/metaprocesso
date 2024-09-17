# Projeto Metaprocesso Compras Gov

Este é um projeto que utiliza Laravel como backend e Vue.js como frontend. Este README fornece as instruções para configurar o ambiente de desenvolvimento, construir o projeto e rodar o servidor.

## Pré-requisitos

Certifique-se de ter as seguintes ferramentas instaladas em sua máquina:

- [PHP](https://www.php.net/downloads) >= 8.3
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) >= 20.x
- [NPM](https://www.npmjs.com/) ou [Yarn](https://yarnpkg.com/)
- [MySQL](https://www.mysql.com/)


Clone o repositório do projeto:
   ```bash
   git clone
   cd repositorio
   ```

## Configuração do Backend (Laravel)

1. composer install:
   ```bash
    cd server
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
    php artisan make:bigboss
   ```

2. Inicie o servidor de desenvolvimento do Laravel:
   ```bash
    php artisan serve
   ```

## Configuração do Frontend Vue

1. npm install:
   ```bash
    cd client
    npm install
   ```

2. Inicie o servidor de desenvolvimento Vue:
   ```bash
    npm run dev
   ```

## Estrutura do Projeto
```plaintext
project-root/
├── client/                # Diretório principal do Vue.js
│   ├── public/
│   ├── src/
│   ├── package.json       # Arquivo de configuração do Node.js
│   ├── ...                # Outros arquivos e diretórios do projeto Vue.js
├── server/                # Diretório principal do Laravel
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   │   └── index.php      # Ponto de entrada para o Laravel
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── tests/
│   ├── .env               # Arquivo de configuração de ambiente
│   ├── artisan            # CLI do Laravel
│   ├── composer.json      # Arquivo de configuração do Composer
│   ├── vite.config.js     # Arquivo de configuração do Vite para Laravel
│   ├── ...                # Outros arquivos e diretórios do projeto Laravel
├── .gitignore
├── README.md