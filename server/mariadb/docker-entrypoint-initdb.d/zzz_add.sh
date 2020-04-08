#!/bin/sh
PASSWORD=$(htpasswd -nbBC 10 "$LOGIN_USERNAME" "$LOGIN_PASSWORD" | cut -d ":" -f2)
mysql --user="$MYSQL_USER" --password="$MYSQL_PASSWORD" "$MYSQL_DATABASE" << EOF
REPLACE INTO users (\`id\`, \`username\`, \`password\`) VALUES (1, "$LOGIN_USERNAME", "$PASSWORD");
EOF