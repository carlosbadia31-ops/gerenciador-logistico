# Gerenciador Logístico

Sistema web feito para a APO de Fundamentos de Programação para Internet (UNIPAR EAD).

Aluno: Carlos Henrique Badia Lazarin - RA 09066221

## O que o sistema faz
- Cadastro de produtos, endereços (origem e destino) e transportadores, com editar e excluir.
- Geração de fretes ligando um transportador, uma origem, um destino e vários produtos, com cálculo do peso e do valor total.
- Relatório dos fretes de um dia, com download em CSV.

## Tecnologias
PHP, HTML, CSS, Bootstrap 5 e JavaScript. Os dados ficam em arquivos JSON na pasta `dados`.

## Como rodar
1. Instale o XAMPP e inicie o Apache.
2. Coloque a pasta do projeto em `C:\xampp\htdocs\logistica`.
3. Acesse http://localhost/logistica