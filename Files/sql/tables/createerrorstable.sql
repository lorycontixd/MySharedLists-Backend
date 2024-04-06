create table errors(
id int primary key not null,
code int not null,
modeid int not null,
title varchar(80),
description varchar(255),
userid int,
source varchar(80),
stacktrace varchar(255),
devicename varchar(80),
devicemodel varchar(80),
devicetype varchar(80),
creationdate datetime not null
);