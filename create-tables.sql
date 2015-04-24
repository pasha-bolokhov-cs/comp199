/* 
 * This is the script which creates initial database tables 
 *
 * Authors: Pasha, Toshi, Candise
 */

/*
 * Disable foreign key constraints temporarily
 */
SET foreign_key_checks = 0;

/*
 * Customers
 */

DROP TABLE IF EXISTS customers;
CREATE TABLE customers (
	customerId	int NOT NULL AUTO_INCREMENT,
	name		varchar(80),
	birth		date,
	nationality	varchar(20),
	passportNo	varchar(20),
	passportExp	date,
	email		varchar(255),
	phone		varchar(80),
	PRIMARY KEY	(customerId)
);


/*
 * Images
 */
DROP TABLE IF EXISTS images;
CREATE TABLE images (
	imageId		varchar(255) NOT NULL,
	fileName	varchar(255),
	PRIMARY KEY	(imageId)
);


/*
 * Cart
 */
DROP TABLE IF EXISTS cart;
CREATE TABLE cart (
	tripNo		int NOT NULL,
	customerId	int,
	startDate	date,
	PRIMARY KEY	(tripNo),
	FOREIGN KEY	(customerId) REFERENCES customers(customerId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Orders
 */
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
	tripNo		int NOT NULL,
	purchaseDate	datetime,
	receiptId	varchar(80),
	PRIMARY KEY	(tripNo)
);


/*
 * Packages
 */
DROP TABLE IF EXISTS packages;
CREATE TABLE packages (
	packageId	int NOT NULL AUTO_INCREMENT,
	tripNo		int,
	origin		varchar(80),
	price		int,
	imageId		varchar(255),
	description	text,
	capacity	int,
	available	int,
	PRIMARY KEY	(packageId),
	FOREIGN KEY	(origin) REFERENCES locations(locationId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(imageId) REFERENCES images(imageId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Transport
 */
DROP TABLE IF EXISTS transport;
CREATE TABLE transport (
	transportId	varchar(80) NOT NULL,
	type		varchar(80),
	rate		int,
	PRIMARY KEY	(transportId)
);


/*
 * Flights
 */
DROP TABLE IF EXISTS flights;
CREATE TABLE flights (
	flightId	int NOT NULL AUTO_INCREMENT,
	alliance	varchar(80),
	departDate	datetime,
	arriveDate	datetime,
	PRIMARY KEY	(flightId)
);


/*
 * Activities
 */
DROP TABLE IF EXISTS activities;
CREATE TABLE activities (
	activityId	varchar(80) NOT NULL,
	name		varchar(80),
	rate		int,
	PRIMARY KEY	(activityId)
);


/*
 * Locations
 */
DROP TABLE IF EXISTS locations;
CREATE TABLE locations (
	locationId	varchar(80) NOT NULL,
	region		varchar(80),
	country		varchar(80),
	city		varchar(80),
	PRIMARY KEY	(locationId)
);


/*
 * Hotels
 */
DROP TABLE IF EXISTS hotels;
CREATE TABLE hotels (
	hotelId		varchar(80) NOT NULL,
	name		varchar(80),
	rank		int,
	imageId		varchar(255),
	description	text,
	PRIMARY KEY	(hotelId),
	FOREIGN KEY	(imageId) REFERENCES images(imageId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Segments
 */
DROP TABLE IF EXISTS segments;
CREATE TABLE segments (
	segId		int NOT NULL AUTO_INCREMENT,
	tripNo		int NOT NULL,
	transportId	varchar(80),
	flightId	int,
	locationId	varchar(80),
	hotelId		varchar(80),
	activityId	varchar(80),
	duration	int,
	dealPrice	int,
	nextSeg		int,
	PRIMARY KEY	(segId),
	FOREIGN KEY	(transportId) REFERENCES transport(transportId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(flightId) REFERENCES flights(flightId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(locationId) REFERENCES locations(locationId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(hotelId) REFERENCES hotels(hotelId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(activityId) REFERENCES activities(activityId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(nextSeg) REFERENCES segments(segId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Re-enable foreign key constraints
 */
SET foreign_key_checks = 1;

