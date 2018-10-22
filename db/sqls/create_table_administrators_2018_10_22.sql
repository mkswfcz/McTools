create table administrators(
    id serial PRIMARY KEY NOT NULL ,
    name VARCHAR (255),
    username VARCHAR (255),
    password VARCHAR (255),
    role VARCHAR (255),
    created_at INTEGER ,
    updated_at INTEGER
);
create index role_on_administrators on administrators(role);