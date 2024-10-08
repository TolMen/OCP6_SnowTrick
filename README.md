# Symfony Project

SnowTricks is a community website for snowboarders.
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
<ul>
    <li>Update DATABASE_URL .env file with your database configuration
        <pre>
            <code>DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name</code>
        </pre>
    </li>
    <li>Create database : 
        <pre>
            <code>symfony console doctrine:database:create</code>
        </pre>
    </li>
    <li>Create database structure :
        <pre>
            <code>symfony console make:migration</code>
        </pre>
    </li>
    <li>Insert fictive data (optional)
        <pre>
            <code>symfony console doctrine:fixtures:load</code>
        </pre>
    </li>
</ul>

<p><strong>4 - Configure MAILER_DSN of Symfony mailer in .env.local file</strong></p>

## Author

[TolMen](https://github.com/TolMen) - [LinkedIn](https://www.linkedin.com/in/jessyfrachisse/)

## License

This project is licensed under MIT - View file [license](LICENSE) for more details.

Feel free to contact me with any questions or contributions. Have a nice visit on our blog !
