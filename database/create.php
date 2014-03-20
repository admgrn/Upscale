<?php
$query = <<<EOD
CREATE DATABASE upscale;

USE upscale;

CREATE TABLE restaurants (
	id INT PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,
	address VARCHAR(200) UNIQUE NOT NULL,
	phone_number VARCHAR(20) NOT NULL,
	longitude INT NOT NULL,
	latitude INT NOT NULL,
	reservation_time INT NOT NULL,
	min_notice TIME NOT NULL,
	max_notice TIME NOT NULL,
	status BOOL NOT NULL,
	);

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
	
CREATE TABLE reservations (
	id INT PRIMARY KEY AUTO_INCREMENT,
	restaurant_id INT,
	FOREIGN KEY (restaurant_id),
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	date DATE,
	start_time TIME,
	number_of people INT
	);
	
CREATE TABLE tables (
	id INT PRIMARY KEY AUTO_INCREMENT,
	FOREIGN KEY (restaurant_id),
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	capacity INT,
	can_combine BOOL,
	description VARCHAR(200),
	reserve_online BOOL
	);

CREATE TABLE hours (
	day_of_week INT NOT NULL,
	FOREIGN KEY (restaurant_id) NOT NULL,
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	open TIME,
	close TIME,
	all_day BOOL,
	PRIMARY KEY (day_of_week, restaurant_id)
	);

CREATE TABLE special_hours (
	date DATE NOT NULL,
	FOREIGN KEY (restaurant_id) NOT NULL,
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	open TIME,
	close TIME,
	all_day BOOL,
	PRIMARY KEY (date, restaurant_id)	
	);
	
CREATE TABLE tables_in_reservation (
	reservation_id INT NOT NULL,
		REFERENCES resevations(id)
		ON DELETE  CASCADE
	table_id,
		REFERENCES tables(id)
		ON DELETE CASCADE
	PRIMARY KEY (reservation_id, table_id)
	);
	
EOD;
?>
