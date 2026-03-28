# System Design Document
Data models and storage (like memory.json)
API design
    We will leverage .php files to create the API endpoints.
Component architecture
    each component will have a separate file

Technology stack choices
    To be run on a PHP 8 server. We have PDO access for SQLite3, but may need local environment configuration or installs for this.
    We will utilize w3css for front end framework. 
    Leveraging HTML, CSS, and Java
Deployment strategy
    We use FTP, so a deploy.bat file will be used to deploy.
    We will use a .gitignore file to ignore the memory.json file.