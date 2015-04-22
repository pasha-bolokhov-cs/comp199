/* 
 * This is the script which creates initial database tables 
 *
 * Changes to do on the diagram:
 * 	* "segments" references itself
 *	* change data types of some of the primary keys to string
 *	  this includes: "transportId", "locationId", "activityId", "hotelId", "imageId"
 *	* remove "tinyint"
 *
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
 * Cart
 */
DROP TABLE IF EXISTS cart;
CREATE TABLE cart (
	tripNo		int NOT NULL AUTO_INCREMENT,
	customerId	int,
	startDate	date,
	PRIMARY KEY	(tripNo),
	FOREIGN KEY	(customerId) REFERENCES customers(customerId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Images
 */
DROP TABLE IF EXISTS images;
CREATE TABLE images (
	imageId		int NOT NULL AUTO_INCREMENT,
	fileName	varchar(255),
	PRIMARY KEY	(imageId)
);


/*
 * Transport
 */
DROP TABLE IF EXISTS transport;
CREATE TABLE transport (
	transportId	tinyint NOT NULL AUTO_INCREMENT,
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
	activityId	tinyint NOT NULL AUTO_INCREMENT,
	name		varchar(80),
	rate		int,
	PRIMARY KEY	(activityId)
);


/*
 * Locations
 */
DROP TABLE IF EXISTS locations;
CREATE TABLE locations (
	locationId	int NOT NULL AUTO_INCREMENT,
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
	hotelId		int NOT NULL AUTO_INCREMENT,
	name		varchar(80),
	rank		tinyint,
	imageId		int,
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
	transportId	tinyint,
	flightId	int,
	locationId	int,
	hotelId		int,
	activityId	tinyint,
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

