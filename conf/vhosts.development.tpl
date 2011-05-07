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

    <Location /server-status>
        SetHandler server-status
        Order deny,allow
        Deny from all
        Allow from all
        #Allow from localhost ip6-localhost
        #Allow from .example.com
    </Location>

    <Location /server-info>
        SetHandler server-info
        Order deny,allow
        Deny from all
        Allow from all
        #Allow from localhost ip6-localhost
        #Allow from .example.com
    </Location>


    ErrorLog /var/log/apache2/error.log
    # Possible values include: debug, info, notice, warn, error, crit,
    # alert, emerg.
#    LogLevel warn
    LogLevel debug
    CustomLog /var/log/apache2/access.log combined
</VirtualHost>