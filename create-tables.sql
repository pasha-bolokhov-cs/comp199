/* 
 * This is the script which creates initial database tables 
 *
 */

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
	tripNo		int NOT NULL,
	customerId	int,
	startDate	date,
	PRIMARY KEY	(tripNo),
	FOREIGN KEY	(customerId) REFERENCES customers(customerId)
			ON DELETE CASCADE ON UPDATE CASCADE
);

