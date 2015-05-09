/* 
 * This is the script which creates initial database tables 
 *
 * Notice: this script should not normally be run by hand
 *
 *
 * Authors: Pasha Bolokhov,
 *	    Toshiyasu Azakawa, 
 *	    Candise Wang
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
	name		varchar(80) NOT NULL,
	birth		date NOT NULL,
	nationality	varchar(20),
	passportNo	varchar(20) NOT NULL,
	passportExp	date NOT NULL,
	email		varchar(255) NOT NULL,
	phone		varchar(80),
	password	varchar(41) NOT NULL,
	salt		varchar(41) NOT NULL,
	PRIMARY KEY	(customerId)
);


/*
 * Images
 */
DROP TABLE IF EXISTS images;
CREATE TABLE images (
	imageName	varchar(255) NOT NULL,
	fileName	varchar(255),	
	type		varchar(20),
	PRIMARY KEY	(imageName)
);


/*
 * Regions
 */
DROP TABLE IF EXISTS regions;
CREATE TABLE regions (
	region	varchar(80) NOT NULL,
	PRIMARY KEY	(region)
);


/*
 * Locations
 */
DROP TABLE IF EXISTS locations;
CREATE TABLE locations (
	city		varchar(80) NOT NULL,
	region		varchar(80),
	country		varchar(80),
	PRIMARY KEY	(city),
	FOREIGN KEY	(region) REFERENCES regions(region)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Transport
 */
DROP TABLE IF EXISTS transport;
CREATE TABLE transport (
	transportId	varchar(80) NOT NULL,
	type		varchar(80),
	PRIMARY KEY	(transportId)
);


/*
 * Flights
 */
DROP TABLE IF EXISTS flights;
CREATE TABLE flights (
	flightId	varchar(80) NOT NULL,
	flightNo	varchar(80),
	origin		varchar(80),
	departDate	datetime,
	destination	varchar(80),
	arriveDate	datetime,
	PRIMARY KEY	(flightId),
	FOREIGN KEY	(origin) REFERENCES locations(city)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(destination) REFERENCES locations(city)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Activities
 */
DROP TABLE IF EXISTS activities;
CREATE TABLE activities (  
	activityId	varchar(80) NOT NULL,
	name		varchar(80),
	PRIMARY KEY	(activityId)
);


/*
 * Hotels
 */
DROP TABLE IF EXISTS hotels;
CREATE TABLE hotels (
	hotelId		varchar(80) NOT NULL, 
	rank		int,
	imageName	varchar(255),
	description	text,
	PRIMARY KEY	(hotelId),
	FOREIGN KEY	(imageName) REFERENCES images(imageName)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Segments
 */
DROP TABLE IF EXISTS segments;
CREATE TABLE segments (
	segId		int NOT NULL AUTO_INCREMENT,
	location	varchar(80),
	transportId	varchar(80),
	flightId	varchar(80),
	hotelId		varchar(80),
	activityId	varchar(80),
	duration	int,
	nextSeg		int,
	PRIMARY KEY	(segId),
	FOREIGN KEY	(transportId) REFERENCES transport(transportId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(flightId) REFERENCES flights(flightId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(location) REFERENCES locations(city)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(hotelId) REFERENCES hotels(hotelId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(activityId) REFERENCES activities(activityId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(nextSeg) REFERENCES segments(segId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Packages
 */
DROP TABLE IF EXISTS packages;
CREATE TABLE packages (
	packageId	int NOT NULL AUTO_INCREMENT,
	segId		int,
	name		varchar(80),
	region		varchar(80),
	origin		varchar(80),
	price		int,
	description	text,
	capacity	int,
	available	int,
	imageName	varchar(255),
	PRIMARY KEY	(packageId),
	FOREIGN KEY	(region) REFERENCES regions(region)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(origin) REFERENCES locations(city)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Orders
 */
DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
	customerId	int,
	packageId	int,
	status		varchar(80),
	purchaseDate	datetime,
	receiptId	varchar(80),
	PRIMARY KEY	(packageId, customerId),
	FOREIGN KEY	(customerId) REFERENCES customers(customerId)
			ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY	(packageId) REFERENCES packages(packageId)
			ON UPDATE CASCADE ON DELETE CASCADE
);

/*
 * cookies
 */

DROP TABLE IF EXISTS cookies;
CREATE TABLE cookies (
	sessionId	VARCHAR(255) NOT NULL,
	customerId	int NOT NULL,
	PRIMARY KEY	(sessionId),
	FOREIGN KEY	(customerId) REFERENCES customers(customerId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Re-enable foreign key constraints
 */
SET foreign_key_checks = 1;

