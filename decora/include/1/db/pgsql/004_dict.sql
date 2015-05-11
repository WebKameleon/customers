CREATE TABLE decora_dict (
   dkey Varchar(32),
   type Char(1),
   name_pl Text,
   name_en Text,
   name_de Text,
   name_ru Text
);

CREATE UNIQUE INDEX decora_dict_key ON decora_dict (dkey,type);
