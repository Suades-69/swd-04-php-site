# Prime Cleaners Ltd

## Project description

A fictional cleaning business website built as a learning project for PHP 8. It demonstrates a reusable PHP page layout, data-driven service and team pages, and a server-processed contact form that saves submissions locally.

## Features

- Shared configuration, navigation, header, and footer
- Home, About, Services, individual service, and Contact pages
- Service cards and team profiles rendered from PHP arrays
- Contact form with server-side validation, sticky values, CSRF protection, and Post/Redirect/Get
- Contact submissions saved as JSON Lines in `storage/messages.jsonl`
- Password-protected local messages page with newest-first sorting and a submission count
- Responsive styling and escaped output for dynamic content

## Tech stack

- PHP 8 (tested locally with PHP 8.2)
- HTML5
- CSS3
- Apache through XAMPP
- JSON Lines for local contact submission storage

## Run with XAMPP

1. Install and start **Apache** in the XAMPP Control Panel.
2. Keep the project in this folder:

   ```text
   C:\xampp\htdocs\swd\04-php-site
   ```

3. Open the site at:

   [http://localhost/swd/04-php-site/](http://localhost/swd/04-php-site/)

4. The contact submissions page is at:

   [http://localhost/swd/04-php-site/messages.php](http://localhost/swd/04-php-site/messages.php)

   Configure a local password before using the messages page. One option is to create `includes/local-config.php` with your own password:

   ```php
   <?php

   return [
       'messagesPagePassword' => 'choose-a-private-local-password',
   ];
   ```

   This file is excluded by `.gitignore` and must not be committed. Alternatively, set the `MESSAGES_PAGE_PASSWORD` environment variable for Apache. The environment variable takes precedence over the local config file.

When the first valid contact form is submitted, PHP creates `storage/` if needed and appends the submission to `storage/messages.jsonl`. Apache is instructed to deny direct web access to that folder by `storage/.htaccess`.

## Project structure

```text
04-php-site/
├── .gitignore
├── about.php
├── contact.php
├── index.php
├── messages.php
├── services.php
├── service.php
├── README.md
├── assets/
│   └── css/
│       └── style.css
├── includes/
│   ├── config.php
│   ├── data.php
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── local-config.php  (create locally; ignored by Git)
└── storage/
    ├── .htaccess
    └── messages.jsonl  (created when the first contact form is submitted)
```

## What I learned

- I learned how to use `require_once` and `__DIR__` to reuse PHP files: **[write your notes here]**
- I learned how to render data with PHP arrays and `foreach`: **[write your notes here]**
- I learned how to validate and safely display form input: **[write your notes here]**
- I learned how sessions and CSRF tokens work: **[write your notes here]**
- I learned how to save and read JSON Lines data: **[write your notes here]**

## Local password warning

The messages-page password is supplied through an untracked local config file or an environment variable; do not commit real passwords. This simple page is still not production-grade: there is no password hashing, rate limiting, account management, or HTTPS enforcement, and a session-based page password is not a full authentication system. Do not use it to protect real personal data. A real application should use a proper authentication system, securely stored password hashes and secrets, HTTPS, and additional access controls.
