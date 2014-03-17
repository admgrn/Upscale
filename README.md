Upscale
=======

Reservation Finder

For this application to run properly include the following mod_rewrite information in your httpd.conf file if using MAMP:

<IfModule mod_rewrite.c>
   RewriteEngine On
   RewriteRule !(MAMP|phpMyAdmin|SQLiteManager)|\.(js|ico|gif|jpg|png|css|html|swf|mp3|wav|txt)$ /index.php/$1 [L]
</IfModule>
