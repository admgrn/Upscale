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

CREATE TABLE restaurants (
	id INT PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(60) NOT NULL,
	address VARCHAR(200) UNIQUE NOT NULL,
	phone_number VARCHAR(20) NOT NULL,
	longitude DOUBLE(100,30) NOT NULL,
	latitude DOUBLE(100,30) NOT NULL,
	reservation_time DECIMAL(11,2) NOT NULL,
	min_notice DECIMAL(11,2) NOT NULL,
	max_notice DECIMAL(11,2) NOT NULL,
	manager_id INT NOT NULL,
	status BOOL NOT NULL,
	FOREIGN KEY (manager_id)
		REFERENCES managers(id)
		ON DELETE CASCADE
	);
	
CREATE TABLE reservations (
	id INT PRIMARY KEY AUTO_INCREMENT,
	restaurant_id INT NOT NULL,
	user_id INT NOT NULL,
	date DATE,
	start_time TIME,
	number_of_people INT,
	FOREIGN KEY (restaurant_id)
		REFERENCES restaurants(id)
		ON DELETE CASCADE,
	FOREIGN KEY (user_id)
		REFERENCES users(id)
		ON DELETE CASCADE
	);
	
CREATE TABLE tables (
	id INT PRIMARY KEY AUTO_INCREMENT,
	restaurant_id INT NOT NULL,
	capacity INT NOT NULL,
	can_combine BOOL,
	description VARCHAR(200),
	reserve_online BOOL,
	FOREIGN KEY (restaurant_id)
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	);

CREATE TABLE hours (
	day_of_week INT NOT NULL,
	restaurant_id INT NOT NULL,
	open TIME,
	close TIME,
	all_day BOOL,
	PRIMARY KEY (day_of_week, restaurant_id),
	FOREIGN KEY (restaurant_id)
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	);

CREATE TABLE special_hours (
	date DATE NOT NULL,
	restaurant_id INT NOT NULL,
	open TIME,
	close TIME,
	all_day BOOL,
	PRIMARY KEY (date, restaurant_id),
	FOREIGN KEY (restaurant_id)
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	);
	
CREATE TABLE tables_in_reservation (
	reservation_id INT NOT NULL,
	table_id INT NOT NULL,
	PRIMARY KEY (reservation_id, table_id),
	FOREIGN KEY (reservation_id)
		REFERENCES reservations(id)
		ON DELETE CASCADE,
	FOREIGN KEY (table_id)
		REFERENCES tables(id)
		ON DELETE CASCADE
	);
	
EOD;
?>
