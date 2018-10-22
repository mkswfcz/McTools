create table articles(
  id serial PRIMARY KEY NOT NULL ,
  title VARCHAR (255),
  content text,
  created_at INTEGER ,
  updated_at integer
);