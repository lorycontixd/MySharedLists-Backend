create table listinvitations(
id int primary key not null,
creatorid int not null,
invitedid int not null,
listid int not null,
wasviewed bit not null,
dayduration int not null,
creationdate datetime not null
);