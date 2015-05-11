CREATE TABLE wbp_imports (
   date timestamp Default CURRENT_TIMESTAMP,
   title Text,
   fileid Text,
   username Varchar(32),
   ip Varchar(15)
);

CREATE INDEX wbp_imports_key ON wbp_imports (username,date);
