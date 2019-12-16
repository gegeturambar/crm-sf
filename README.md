# crm-sf

server {
        listen 80;

        root /var/www/api-crm;
        index index.php;

        server_name api-crm;

        location / {
                try_files $uri /index.php$is_args$args;
        }
        location ~ ^/index\.php(/|$) {
                fastcgi_pass unix:/var/run/php/php7.3-fpm.sock;
                fastcgi_split_path_info ^(.+\.php)(/.*)$;
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
                fastcgi_param DOCUMENT_ROOT $realpath_root;
                internal;
                fastcgi_read_timeout 600;
       }

}
