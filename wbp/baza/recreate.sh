
echo "DROP DATABASE wbp;" | mysql -u root 
echo "CREATE DATABASE wbp;" | mysql -u root 
mysql -u root wbp < wbppoz_serwis.sql
