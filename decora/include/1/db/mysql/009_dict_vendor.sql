ALTER TABLE decora_dict ADD vendor Varchar(16);
UPDATE decora_dict SET vendor='arbiton';


DROP INDEX decora_dict_key;
CREATE UNIQUE INDEX decora_dict_key ON decora_dict (vendor, dkey, type);
