create table listitems(
id int primary key not null,
name varchar(255) not null,
description varchar(255),
quantity int not null,
listid int not null,
ischecked bit not null,
creatorid int not null,
creationdate datetime not null
);