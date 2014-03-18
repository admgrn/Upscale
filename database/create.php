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
	FOREIGN KEY (restaurant_id)
		REFERENCES restaurants(id)
		ON DELETE CASCADE
	date DATE,
	start_time TIME
	number_of people INT,
	);
	
CREATE TABLE tables (
	
	);

CREATE TABLE hours (
	
	);

CREATE TABLE special_hours (
	
	);
	
CREATE TABLE tables_in_reservation (
	
	);
	
EOD;
?>
