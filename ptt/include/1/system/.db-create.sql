CREATE TABLE messages (
msg_id serial,
msg_label char(128),
msg_lang char(2),
msg_msg text
); 

CREATE INDEX messages_label_key ON messages(msg_label);
CREATE INDEX messages_lang_key ON messages(msg_lang);

CREATE TABLE klienci (
	id       serial,
	login    char(16),
	pass     char(16),
	imie     char(40),
	nazwisko char(50),
	email    char(80),
	telefon  char(40),
	gsm      char(40),
	adres    text,
	kod      char(10),
	miasto   char(100)
);

CREATE INDEX klienci_login_key ON klienci(login);


CREATE TABLE obiekty (
	id serial,
	kod char(16),
	nazwa char(40),
	adres text,
	grupa int2
);


CREATE INDEX obiekty_kod_key ON obiekty(kod);

CREATE TABLE kursy (
	id            serial,
	cykl          char(2),
	godz_od       time,
	godz_do       time,
	obiekt        char(16),
	pomieszczenie char(16),
	taniec        char(50),
	zaawansowanie char(50),
	prowadzacy    char(50),
	miejsc        int2,
	cena          float(8)
);
CREATE INDEX kursy_cykl_key ON kursy(cykl);
CREATE INDEX kursy_obiekt_key ON kursy(obiekt);
CREATE INDEX kursy_taniec_key ON kursy(taniec);


CREATE TABLE zapisy (
	id            serial,
	klient_id     int4,
	kurs_id       int4,
	d_zgloszenia  date,
	d_rezygnacji  date,
	p_rezygnacji  text,
	ilosc         int2,
	cena          float8
);

CREATE INDEX zapisy_klient_key ON zapisy(klient_id);
CREATE INDEX zapisy_kurs_key ON zapisy(kurs_id);
CREATE INDEX zapisy_zgloszenie_key ON zapisy(d_zgloszenia);


CREATE TABLE wplaty (
	id            serial,
	klient_id     int4,
	d_wplaty      date,
	uwagi         text,
	kwota         float8
);
CREATE INDEX wplaty_klient_key ON wplaty(klient_id);



CREATE TABLE "mailer" (
        "id" serial,
        "action" character(32),
        "mailfrom" character(100),
        "type" character(10),
        "subject" text,
        "msg" text,
        "grupa" int4
);
CREATE  INDEX "mailer_action_key" on "mailer" using btree ( "action" "bpchar_ops" );


CREATE TABLE undo (
	id            serial,
	username      char(16),
	d_wykonania   date,
	opis          text,
	undo          text
);        

CREATE TABLE akademiki (
	id            serial,
	nazwa         text,
	adres         text,
	cena          float8,
	miejsc        int2
);


CREATE TABLE zapisy_a (
	id            serial,
	klient_id     int4,
	akademik_id       int4,
	d_zgloszenia  date,
	d_przyjazdu   date,
	d_rezygnacji  date,
	p_rezygnacji  text,
	ilosc         int2,
	cena          float8
);

CREATE INDEX zapisy_a_klient_key ON zapisy_a(klient_id);
CREATE INDEX zapisy_a_akademik_key ON zapisy_a(akademik_id);
CREATE INDEX zapisy_a_zgloszenie_key ON zapisy_a(d_zgloszenia);

