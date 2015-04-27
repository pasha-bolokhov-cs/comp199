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
	imageId		int NOT NULL AUTO_INCREMENT,
	ImageName	varchar(255) NOT NULL,
	imageType	varchar(255),
	PRIMARY KEY	(imageId)
);


/*
 * Cart
 * Add Status
 * TripNo & customerId : composit key?
 * TripNo is different per customer per trip
 */
DROP TABLE IF EXISTS cart;
CREATE TABLE cart (
	tripNo		int NOT NULL AUTO_INCREMENT,
	customerId	int,
	startDate	date,
	status		varchar(80),
	PRIMARY KEY	(tripNo),
	FOREIGN KEY	(customerId) REFERENCES customers(customerId)
			ON UPDATE CASCADE ON DELETE CASCADE
);


/*
 * Orders
 * 
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
 * delete : tripNo    int,
 */
DROP TABLE IF EXISTS packages;
CREATE TABLE packages (
	packageId	int NOT NULL AUTO_INCREMENT,
	origin		varchar(80),
	price		int,
	imageId		int,
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
 * Segments
 * REMOVED:  tripNo     int NOT NULL, 
 * Add: packageId	int,
 * Flight??? 
 */
DROP TABLE IF EXISTS segments;
CREATE TABLE segments (
	segId		int NOT NULL AUTO_INCREMENT,
	transportId	varchar(80),
	flightId	varchar(80),
	locationId	varchar(80),
	hotelId		varchar(80),
	activityId	varchar(80),
	duration	int,
	dealPrice	int,
	nextSeg		int,
	packageId	int,
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
 * Transport
 */
DROP TABLE IF EXISTS transport;
CREATE TABLE transport (
	transportId	varchar(80) NOT NULL,
	type		varchar(80),
	PRIMARY KEY	(transportId)
);


/*
 * Flights == alliance
 */
DROP TABLE IF EXISTS flights;
CREATE TABLE flights (
	flightId	varchar(80) NOT NULL,
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
	hotelId		int NOT NULL, AUTO_INCREMENT,
	name		varchar(80),
	rank		int,
	imageId		int,
	description	text,
	PRIMARY KEY	(hotelId),
	FOREIGN KEY	(imageId) REFERENCES images(imageId)
			ON UPDATE CASCADE ON DELETE CASCADE
);





/*
 * Re-enable foreign key constraints
 */
SET foreign_key_checks = 1;

