<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo"></a></p>

# 📞 Case Técnico Betabit: Agenda Telefônica - Backend (Laravel)

## 🎥 Apresentação e Demonstração do Projeto

O vídeo de explicação detalhada aborda a estrutura do Back-end, as decisões de design de banco de dados, a lógica de autenticação e a implementação da API RESTful:

**[ASSISTA AQUI: VÍDEO DE EXPLICAÇÃO NO YOUTUBE (Tempo: 05:00 - 10:00)] (https://www.youtube.com/watch?v=JSnh9ZSuglc&t=1s)**

---

## ✨ Visão Geral e Stack Tecnológica

Este repositório contém a **API RESTful (Backend)** da "Agenda Telefônica", desenvolvida em Laravel/PHP, que serve dados e gerencia a autenticação para o Frontend (Vue.js).

### 🔗 Repositório do Frontend (Vue 3)

Para rodar a aplicação Full Stack completa, acesse o repositório do Front-end:
> **[Betabit-Agenda-Frontend](https://github.com/feliipenevesnow/Betabit-Agenda-Frontend)**

### ⚙️ Tecnologias Utilizadas no Backend

* **Framework:** Laravel (Mencione a versão do Laravel se souber, ex: 10.x)
* **Linguagem:** PHP (Mencione a versão do PHP se souber, ex: 8.2+)
* **Banco de Dados:** SQL (Especifique o SGBD: MySQL/PostgreSQL/SQLite)
* **Autenticação:** Laravel Sanctum (Utilizado para autenticação de SPA/API Tokens)

---

## ✅ Requisitos Atendidos (Backend)

O Back-end foi implementado para dar suporte a todos os requisitos do case:

* **Autenticação Robusta:**
    * Implementação de rotas de `login` e `logout`.
    * Utilização do **Laravel Sanctum** para proteger as rotas da API.
* **API RESTful Completa (CRUD):**
    * Rotas para **Cadastro (C)**, **Leitura (R)**, **Atualização (U)** e **Exclusão (D)** de contatos.
    * Estrutura de banco de dados (`Schema Migration`) para `users` e `contacts` (campos: nome, telefone, e-mail, imagem).
* **Boas Práticas:**
    * Uso de *Resource Controllers* e *Models* do Laravel.
    * Separação de responsabilidades (Controllers e Models).
    * Validação de dados (Requests) para garantir a integridade.

---
