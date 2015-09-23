/* 
 * This is the script which creates and pre-fills tables with packages
 * This is the ONLY initialization script which is supposed to be run
 * by hand, e.g. after a database re-instantiation
 *
 * Authors: Pasha Bolokhov,
 *	    Toshiyasu Azakawa, 
 *	    Candise Wang
 */

/*
 * Create the necessary tables
 */
source create-tables.sql;

/*
 * Pre-fill the tables
 */
source activities.sql;
source regions.sql;
source locations.sql;
source flights.sql;
source images.sql;
source hotels.sql;
source transport.sql;
source packages.sql;



