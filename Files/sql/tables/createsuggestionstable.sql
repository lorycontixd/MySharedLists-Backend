create table suggestions(
id int primary key not null,
userid int not null,
modeid int not null,
mode varchar(30) not null,
title varchar(80) not null,
description varchar(255) not null,
creationdate datetime not null
);