CREATE TABLE decora_recipients (
   id Integer,
   lang char(2),
   name varchar(128),
   pri Integer,
   hours_week Varchar(32),
   hours_sa Varchar(32),
   hours_su Varchar(32),
   zip Varchar(6),
   province Varchar(64),
   city Varchar(64),
   street Varchar(64),
   tel1 Varchar(32),
   tel2 Varchar(32),
   fax Varchar(32),
   mail Varchar(128),
   www Varchar(128),
   receives Bigint
);

CREATE UNIQUE INDEX decora_recipients_key ON decora_recipients (id,lang,province);

