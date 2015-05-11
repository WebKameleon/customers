CREATE TABLE decora_imports (
   date timestamp Default CURRENT_TIMESTAMP,
   fileid Text,
   username Varchar(32),
   ip Varchar(15)
);

CREATE INDEX decora_imports_key ON decora_imports (username,date);
