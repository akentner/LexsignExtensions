<VirtualHost *:80>

    ServerAlias ${vhost.server.alias}
    DocumentRoot ${deploy.path}/public

    SetEnv APPLICATION_ENV ${application-env}

    <Directory ${deploy.path}/public  >
        Options Indexes MultiViews FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
        <IfModule mod_php5.c>
            php_value include_path ".:/usr/share/php:/var/www/lib/ZendFramework/current/library"
        </IfModule>
    </Directory>

    ErrorLog /var/log/apache2/error.log
    # Possible values include: debug, info, notice, warn, error, crit,
    # alert, emerg.
#    LogLevel warn
    LogLevel debug
    CustomLog /var/log/apache2/access.log combined
</VirtualHost>