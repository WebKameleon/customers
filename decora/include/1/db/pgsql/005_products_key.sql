DROP INDEX decora_products_key;
CREATE UNIQUE INDEX decora_products_key ON decora_products (ean,vendor,product);
