<VirtualHost *:80>

    ServerAlias ${vhost.server.alias}
    DocumentRoot ${deploy.path}/public

    SetEnv APPLICATION_ENV ${application-env}

    <Directory ${deploy.path}/public  >
        Options Indexes MultiViews FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    ErrorLog /var/log/apache2/error.log
    # Possible values include: debug, info, notice, warn, error, crit,
    # alert, emerg.
#    LogLevel warn
    LogLevel debug
    CustomLog /var/log/apache2/access.log combined
</VirtualHost>