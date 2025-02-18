# ❄️ SnowTricks - Snowboarding Community Website 🏂

> **This project was created as part of my learning journey in the OpenClassrooms curriculum (PHP/Symfony Application Developer).**  
> --> *Version : [Français](README_fr.md)* 📖

## 📖 Description

**SnowTricks** is a community platform dedicated to snowboarders.  
Users can discover and share snowboarding tricks, as well as interact through a comment system.

![Project Preview - SnowTricks](screenshot.jpg)

## 🚀 Features

- **Trick Catalog** : All visitors can view the list of tricks with descriptions.
- **Trick Management** : Registered users can add, edit, and delete tricks.
- **Comment System** : Members can comment on tricks and share tips.
- **User Authentication** : Secure sign-up and login.
- **Security Enhancements** : Protection against XSS, CSRF, and SQL injection.
- **Responsive Design** : Optimized for all screen sizes.

## 🚧 Installation

### Prerequisites

Before starting, ensure you have the following installed:

- **PHP and Composer**
- **Symfony CLI**
- **MySQL**
- **Git**

### Installation Steps

1. **Clone the repository**  
   ```sh
   git clone https://github.com/TolMen/OCProject6.git
   cd OCProject6
   ```

2. **Install dependencies**  
   ```sh
   symfony console composer install
   ```

3. **Set up the database**  
   - Edit the `.env` file and update the following line with your database settings :  
     ```sh
     DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
     ```
   - Create the database :  
     ```sh
     symfony console doctrine:database:create
     ```
   - Generate the database structure :  
     ```sh
     symfony console make:migration
     symfony console doctrine:migrations:migrate
     ```
   - (Optional) Insert dummy data :  
     ```sh
     symfony console doctrine:fixtures:load
     ```

4. **Configure email sending**  
   - Edit `.env.local` and set up MAILER_DSN :  
     ```sh
     MAILER_DSN=smtp://your_mail_server
     ```
---

Thank you for exploring this project.  
Feel free to explore, modify, and improve it ! ✨  

**For any questions or collaboration, don’t hesitate to reach out ! 📩**

[TolMen](https://github.com/TolMen) - [LinkedIn](https://www.linkedin.com/in/jessyfrachisse/)
