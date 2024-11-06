# Symfony Project

> **Ce projet a été réalisé dans le cadre de mon apprentissage pour le parcours d'OpenClassrooms (Développeur d'application PHP/Symfony).**

SnowTricks is a community website for snowboarders :
- The list of figures and the description are visible to all visitors
- Registered users are allowed to comment on tips, add/edit tricks

# Installation

<p><strong>1 - Git clone the project</strong></p>
<pre>
    <code>https://github.com/TolMen/OCProject6</code>
</pre>

<p><strong>2 - Install libraries</strong></p>
<pre>
    <code>symfony console composer install</code>
</pre>

<p><strong>3 - Create database</strong></p>

- Update DATABASE_URL .env file with your database configuration :  <br>
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name <br> <br>

- Create database : <br> symfony console doctrine:database:create <br> <br>

- Create database structure : <br> symfony console make:migration <br> <br>

- Insert fictive data (optional) : <br> symfony console doctrine:fixtures:load <br> <br>

<p><strong>4 - Configure MAILER_DSN of Symfony mailer in .env.local file</strong></p>

## Author

[TolMen](https://github.com/TolMen) - [LinkedIn](https://www.linkedin.com/in/jessyfrachisse/)

## License

This project is licensed under MIT - View file [license](LICENSE) for more details.

Feel free to contact me with any questions or contributions. Have a nice visit on our blog !
