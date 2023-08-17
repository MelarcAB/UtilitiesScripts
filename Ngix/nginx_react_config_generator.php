<?php
echo "Ingrese el nombre del subdominio (por ejemplo, 'nombreapp'): ";
$subdomain = trim(fgets(STDIN));

echo "Ingrese el dominio completo (por ejemplo, 'melarc.dev'): ";
$domain = trim(fgets(STDIN));

echo "Ingrese la ruta completa de la aplicación (por ejemplo, '/var/www/pokedex'): ";
$path = trim(fgets(STDIN));

$filename = "/etc/nginx/sites-available/{$subdomain}.conf";

$template = <<<EOT
server {
    server_name $subdomain.$domain;
    root $path/dist;
    index index.html;

    location / {
        try_files \$uri \$uri/ /index.html;
    }

    error_page 404 /index.html;

    location ~* \.(?:ico|css|js|gif|jpe?g|png)$ {
        expires max;
        add_header Cache-Control public;
    }

    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/$subdomain.$domain/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/$subdomain.$domain/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot
}

server {
    if (\$host = $subdomain.$domain) {
        return 301 https://\$host\$request_uri;
    } # managed by Certbot

    listen 80;
    server_name $subdomain.$domain;
    return 404; # managed by Certbot
}
EOT;

if (file_put_contents($filename, $template)) {
    echo "Configuración creada con éxito en $filename\n";
} else {
    echo "Error al crear el archivo de configuración.\n";
}

?>
