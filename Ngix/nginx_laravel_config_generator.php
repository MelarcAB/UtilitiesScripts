<?php

$configurationTemplate = <<<CONF
server {
    server_name %s.%s;
    root /var/www/%s/public;

    index index.php;
    include fastcgi_params;

    location / {
        try_files \$uri \$uri/ @%s;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$request_filename;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location @%s {
        rewrite ^/(.*)$ /index.php?/\$1 last;
    }

    listen [::]:443 ssl; # managed by Certbot
    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/%s.%s/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/%s.%s/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot
}

server {
    if (\$host = %s.%s) {
        return 301 https://\$host\$request_uri;
    } # managed by Certbot

    listen 80;
    listen [::]:80;
    server_name %s.%s;
    return 404; # managed by Certbot
}
CONF;

echo "Please enter the application name: ";
$appName = trim(fgets(STDIN));

echo "Please enter the desired subdomain: ";
$subdomainName = trim(fgets(STDIN));

echo "Please enter the full domain name: ";
$domainName = trim(fgets(STDIN));

$configuration = sprintf(
    $configurationTemplate, 
    $subdomainName, 
    $domainName, 
    $appName, 
    $appName, 
    $appName, 
    $subdomainName, 
    $domainName, 
    $subdomainName, 
    $domainName, 
    $subdomainName, 
    $domainName, 
    $subdomainName, 
    $domainName
);

$fileLocation = '/etc/nginx/sites-available/'.$subdomainName.'.'.$domainName.'.conf';

file_put_contents($fileLocation, $configuration);

echo "Configuration file created at $fileLocation\n";

?>
