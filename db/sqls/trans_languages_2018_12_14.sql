CREATE TABLE trans_languages(
  id serial PRIMARY KEY NOT NULL ,
  country_en VARCHAR (255),
  country_zh VARCHAR (255),
  code VARCHAR (255),
  created_at INTEGER ,
  updated_at INTEGER
);
create index id_on_trans_languages on trans_languages(id);
