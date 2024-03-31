create table lists(
id int primary key not null,
name varchar(80) not null,
description varchar(255) not null,
creatorid int not null,
color int not null,
code varchar(80) not null,
lastupdated datetime not null,
creationdate datetime not null
);