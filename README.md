<p align="center">
<b>TorrentTrader MVC PDO OOP</b><br>
<b>10.3.22 MariaDB</b><b><br>
<b>PHP 7.4.3</b><b>
</p>

## <a name="introduction"></a> :page_facing_up: Introduction

This my my copy of torrent trader updated to PDO with a MVC core<br>
I still have a lot to do but its fully functional for use, ill keep github up to date this is a long term project<br>
Playing with the code is a bit of a hobby and if anyone wants to help or suggest feel welcome to do so there is support at http://www.torrenttrader.xyz
Credit to original authors of any code or mods this is just a version i use to learn more about coding 

## <a name="features"></a> 💎 Some Features

  - Stack backtrace for exceptions
  - PDO Prepared Statements
  - MVC Core
  - Bootstrap
  - BCRYPT Passwords
  - Snatchlist
  - Magnets
  - and MUCH MORE!

## <a name="requirements"></a> :white_check_mark: Requirements

- A Web server
- PHP 7.4
- MySQL 5.9

## <a name="installation"></a> :computer: Installation

THERE IS NO INSTALLER REQUIRED!

1) Copy ALL files to your webserver

2) Import via phpmyadmin "Full Database.sql"

3) Edit the file config/config.php to suit your needs
   special note should be taken for sql connections, siteurl, email

4) Apply the following CHMOD's
777 - cache
777 - cache/imdb
777 - backups
777 - uploads
777 - uploads/images
777 - uploads/imdb
777 - import
600 - censor.txt

5) Run check.php from your browser to check you have configured everything ok
   check.php is designed for UNIX systems, if you are using WINDOWS it may not report the paths correctly.

6) Now register as a new user on the site.  The first user registered will become administrator

7) If check.php still exists, please remove it or rename.
   A warning will display on the site index until its removed

8) You should properly secure backupdatabase.php and the backups dir. (htaccess/htpasswd)

Any problems please visit http://www.torrenttrader.xyz

For xampp please use folder provided at http://www.torrenttrader.xyz