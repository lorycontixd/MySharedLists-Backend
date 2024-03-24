create table users(
id int primary key not null,
username varchar(80) not null,
password varchar(255) not null,
firstname varchar(80),
lastname varchar(80),
lastupdated datetime not null,
creationdate datetime not null
);