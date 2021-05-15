<p align="center">
<b>TorrentTrader MVC PDO OOP</b><br>
<b>10.3.22 MariaDB</b><b><br>
<b>PHP 8</b><b>
</p>

## <a name="introduction"></a> :page_facing_up: Introduction

This is my copy of torrent trader updated in MVC using PDO with bootstrap. <br>
Its fully functional for use, ill keep github up to date. This is a long term project <br><br>
Playing with the code is a bit of a hobby and if anyone wants to help or suggest feel welcome to do so there is support at https://torrenttrader.uk<br>
Credit to original authors of any code or mods this is just a version i use to learn more about coding 

## <a name="features"></a> 💎 Some Features

  - Stack backtrace for exceptions
  - PDO Prepared Statements
  - MVC Core
  - Bootstrap
  - BCRYPT Passwords
  - Snatchlist
  - Magnets
  - Scraper
  - and MUCH MORE!

## <a name="requirements"></a> :white_check_mark: Requirements

- A Web server
- PHP 8
- MySQL 5.9

## <a name="installation"></a> :computer: Installation

THERE IS NO INSTALLER REQUIRED!

1) Copy ALL files to your webserver, NOTE the name of the public folder "public_html" where index.php is<br>

   If public folder is not named public_html rename the folder public_html to match (public, home etc)\
   Only the contents of public_html go in the public folder.
   
   if you rename public_html you must also adjust the htaccess\
   .htaccess\
   RewriteRule ^$ public_html/ [L]\
   RewriteRule (.*) public_html/$1 [L]

   For xampp only the public_html/htaccess might need ajusting<br>
   RewriteBase /TorrentTraderMVC/public_html

2) Import via phpmyadmin "SQL/Full Database.sql"

3) Edit the file app/config/config.php to suit your needs\
   // Database Details\
   define("DB_HOST", "localhost");\
   define("DB_USER", "username");\
   define("DB_PASS", "password");\
   define("DB_NAME", "dbname");\
   define('DB_CHAR', 'utf8');\
   // Your Site Address\
   define('URLROOT', 'http://localhost/TorrentTraderMVC'); 

4) Apply the following CHMOD's\
   777 - data/backups\
   777 - data/cache\
   777 - data/cache/imdb\
   777 - data/import\
   777 - data/logs\
   777 - data/uploads\
   777 - data/uploads/images\
   777 - data/uploads/imdb\
   777 - data/uploads/attachment\
   777 - public_html/thumbnail\
   600 - dta/logs/censor.txt\

5) Now register as a new user on the site.  The first user registered will become administrator

Any problems please visit https://torrenttrader.uk
