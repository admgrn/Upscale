<?php
$query = <<<EOD
CREATE DATABASE upscale;

USE upscale;

CREATE TABLE users (
	id INT PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,
	username VARCHAR(60) UNIQUE NOT NULL,
	email VARCHAR(40) UNIQUE NOT NULL,
	password CHAR(32) NOT NULL,
	phone_number VARCHAR(20) NOT NULL
	);

CREATE TABLE managers (
	id INT PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,
	username VARCHAR(60) UNIQUE NOT NULL,
	email VARCHAR(40) UNIQUE NOT NULL,
	password CHAR(32) NOT NULL
	);
	
	
EOD;
?>