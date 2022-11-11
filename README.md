# :hamburger: Fastuga :fries:

<p>Este projeto foi desenvolvido no âmbito da unidade curricular de <b>Desenvolvimento de Aplicações Distribuídas</b> de Engenharia Informática no ano letivo 2022/2023.</p>
<b>Colaboradores:</b>

- Alexandre <sup>[g](https://github.com/Alex-Jer)</sup>
- Bruna <sup>[g](https://github.com/bbarbie)</sup>
- Gonçalo <sup>[g](https://github.com/trwygon)</sup>
- Tomás <sup>[g](https://github.com/TomasPereiraa)</sup>

## :pencil: Introdução

O objetivo deste projeto é [um pequeno resumo].
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam mollis, ligula quis consectetur tempus, libero odio maximus odio, sit amet consequat mauris erat eget nulla. Nam commodo vehicula sagittis. Praesent rhoncus orci ac dolor iaculis, eget aliquet nibh porttitor. Maecenas id lorem ac ipsum varius dapibus.

Clique [aqui](materials/2022-23-EI-DAD-Project.pdf) para ler o enunciado fornecido pelos docentes.
(Contém informações adicionais sobre o cenário e implementação)

## :computer: Linguagens e Tecnologias Utilizadas

<p><a href="https://laravel.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/laravel/laravel-plain-wordmark.svg" alt="laravel" title="Laravel" width="40" height="40" /></a><a href="https://www.php.net" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" title="PHP" width="40" height="40" /></a><a href="https://www.mysql.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" title="MySQL" width="40" height="40" /></a><!--<a href="https://tailwindcss.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/tailwindcss/tailwindcss-plain.svg" alt="tailwind" title="Tailwind CSS" width="40" height="40" /></a>--><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/html5/html5-original-wordmark.svg" alt="html5" title="HTML5" width="40" height="40" /><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/css3/css3-original-wordmark.svg" alt="css3" title="CSS3" width="40" height="40" /><a href="https://git-scm.com/" target="_blank" rel="noreferrer"> <img src="https://www.vectorlogo.zone/logos/git-scm/git-scm-icon.svg" alt="git" title="Git" width="40" height="40" /></a><a href="https://code.visualstudio.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/vscode/vscode-original.svg" alt="vscode" title="Visual Studio Code" width="40" height="40" /></a><a href="https://www.nginx.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/nginx/nginx-original.svg" alt="nginx" title="NGINX" width="40" height="40" /></a><a href="https://www.npmjs.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/npm/npm-original-wordmark.svg" alt="npm" title="NPM" width="40" height="40" /></a><a href="https://getcomposer.org" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/composer/composer-original.svg" alt="composer" title="Composer" width="40" height="40" /></a><!--<a href="https://heroicons.dev" target="_blank" rel="noreferrer"> <img src="https://heroicons.dev/static/favicon.ico" alt="hero icons" title="Heroicons" width="40" height="40" /></a>--><a href="https://github.com/devicons/devicon" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/devicon/devicon-original-wordmark.svg" alt="devicon" title="Devicon" width="40" height="40" /></a></p>
<b>Aplicações Laravel:</b>

<!--- [Mail CSS Inliner](https://github.com/fedeisas/laravel-mail-css-inliner) **[!TODO!]**-->

- [Debugbar](https://github.com/barryvdh/laravel-debugbar) [?]
- [IDE Helper](https://github.com/barryvdh/laravel-ide-helper) [?]
- [Ignition](https://github.com/spatie/laravel-ignition) - Better error pages.

<p></p>
<b>Outras aplicações:</b>

- [Axios](https://github.com/axios/axios)
- [FakerPHP](https://github.com/fakerphp/faker) - Generate fake data & seed database.

Todos os _packages_ utilizados estão no [composer.json](composer.json)

## :man_technologist: Configurar o Projeto

Para começar deve renomear o `.env.example` para `.env` e preenchê-lo com as informações corretas para a sua área de trabalho.

Após isso deve correr os seguintes comandos em um terminal na pasta _root_:

```bash
npm install
composer install
composer update
composer dump-autoload
php artisan ide-helper:generate
php artisan migrate
php artisan db:seed
```

## :x: Erros Comuns e Soluções

#### Rota não encontrada

Por vezes após alterar as rotas pode ser necessário atualizar a cache das mesmas, para fazer isso execute o seguinte comando no terminal: `php artisan route:cache`.

#### Problemas com o CSS

O tailwind compila as classes css que estão a ser utilizadas e que serão necessárias. Para compilar o css deve-se executar o comando `npm run dev`, ou alternativamente o comando `npm run watch` para que isto seja realizado automaticamente quando forem verificadas alterações.

O tailwind não detetará as classes que não estiverem em html puro, por exemplo as classes que estiverem a ser inseridas através de Blade e/ou PHP. Para isso existe uma _safelist_ no ficheiro [`tailwind.config.js`](tailwind.config.js) onde se deve inserir essas mesmas classes.

<!--## :100: Avaliação Final

Obtivemos uma classificação de <b>17,7 valores</b> (em 20). As cotações em cada ponto foram as seguintes:

<img src="materials/nota.png"/>

<p></p>
<b>Notas do docente:</b>

- Não permitem alterar password.
- Têm muitas validações nullable.
- No carrinho não permitem adicionar o mesmo lugar a duas sessões diferentes do mesmo filme (e assinalaram a verde na vista os mesmos lugares em todas as sessões do mesmo filme).
- No registo próprio, não é criado o "Cliente", logo dá erro.
- Devia-se pedir o nome, NIF, ref_pagamento e tipo_pagamento ao cliente, para ele alterar os dados.
- Não enviam o pdf para o email.
-->

## :mortar_board: Outras Informações

- Licenciatura em [Engenharia Informática](https://www.ipleiria.pt/curso/licenciatura-em-engenharia-informatica/)

<a href="https://www.ipleiria.pt/estg/"><img src="https://www.ipleiria.pt/normasgraficas/wp-content/uploads/sites/80/2017/09/estg_h-01.jpg" width="300" alt="Escola Superior de Tecnologia e Gestão" title="Escola Superior de Tecnologia e Gestão"></a>
