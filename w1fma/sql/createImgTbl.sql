CREATE TABLE Images (
id INT AUTO_INCREMENT,
thumbpath VARCHAR(255),
largepath VARCHAR(255),
name VARCHAR(255),
title VARCHAR(100),
description CHAR(255),
width smallint , 
height smallint,
sha_1 CHAR(40),
PRIMARY KEY(id)
);