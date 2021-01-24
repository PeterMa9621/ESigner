## About ESigner

ESigner is a website that allows people to upload their pdf documents and sign documents by themselves or others.

## Development environment

Language: PHP 7.2

Framework: Laravel and Vue.js

Library: FPDF, pdf.js, signature_pad.js

Software: pdftk

## Install

1. Install php, node.js, pdftk, apache and mysql
2. Install composer
    * <strong>curl -sS https://getcomposer.org/installer | php</strong>
    * <strong>sudo mv composer.phar /usr/local/bin/composer</strong>
    * <strong>sudo chmod +x /usr/local/bin/composer</strong>
3. Clone source code inside www/html folder
4. CD into your folder
5. Install php-mbstring
    * <strong>sudo apt-get install php-mbstring</strong>
6. Install php7.2-xml
    * <strong>sudo apt-get install php7.2-xml</strong>
7. Run <strong>sudo composer install</strong>
8. Copy <strong>.env.example</strong> and rename it to <strong>.env</strong>, modify it
    * After modify, run <strong>sudo php artisan config:cache</strong>
9. Install php-mysql
    * <strong>sudo apt-get install php-mysql</strong>
10. Restart apache
    * <strong>sudo service apache2 restart</strong>
11. Migrate database
    * <strong>sudo php artisan migrate</strong>
12. Clear config and cache
    * <strong>php artisan config:clear</strong>
    * <strong>php artisan config:cache</strong>
13. Give permission to storage folder
    * <strong>sudo chmod -R 0777 storage/</strong>
14. Run <strong>sudo npm install</strong>
15. Run <strong>sudo npm run prod</strong>
16. Done

## Apache Configuration
Without Proxy Https
```
<VirtualHost *:443>
   ServerName thedomain.com
   ServerAdmin webmaster@thedomain.com
   SSLEngine on
   DocumentRoot /var/www/html/example/public

   <Directory /var/www/html/example>
       AllowOverride All
       Order allow,deny
       allow from all
   </Directory>
   ErrorLog ${APACHE_LOG_DIR}/error.log
   CustomLog ${APACHE_LOG_DIR}/access.log combined
   
   SSLCertificateFile /yourfile.pem
   SSLCertificateKeyFile /yourkey.pem
</VirtualHost>
```

With Proxy Https
```
<VirtualHost *:443>
	ServerName thedomain.com
	ServerAdmin webmaster@thedomain.com
	SSLEngine on
	DocumentRoot /var/www/html/example/public
    
    <Directory "/var/www/html/example">
        AllowOverride All
        Order allow,deny
        allow from all
	</Directory>
    
    SSLProxyEngine on
	ProxyRequests off
    ProxyPreserveHost On
    <Location />
    	ProxyPass !
    </Location>

	ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
	
    SSLCertificateFile /yourfile.pem
    SSLCertificateKeyFile /yourkey.pem
</VirtualHost>
```

## License

ESigner is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
