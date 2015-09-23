/*
 * Data for 'packages' table
 *        segId ----> start segment Id
 */


/*
 * Hawaii
 */
INSERT INTO images (imageName, fileName, type)
       VALUES ('Hawaii', 'xl_hawaii-1.jpg', NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Honolulu', NULL, NULL, 'Hilton', 'surfing', 2, NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Honolulu', 'flight', 'Hawaiian Airlines - Summer 2015', 'Hilton', 'sightseeing', 4, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Los Angeles', 'flight', 'American Airlines - Summer 2015', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO packages (segId, name, region, origin, price, description, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Hawaii - Summer 2015', 'North America', 'San Francisco', 700, 'Mango-sweet vacation on Hawaii', 40, 'Hawaii');

/*
 * Japan
 */
INSERT INTO images (imageName, filename, type)
       VALUES ('Kyoto', 'xl_kyoto-2.jpg', NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Tokyo', 'flight', 'All Nippon Airlines - Summer 2015', NULL, NULL, 1, NULL );
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Kyoto', 'train', NULL, 'Hilton', 'sightseeing', 6, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Tokyo', 'flight', 'Air Canada - Summer 2015', NULL, NULL, 1, LAST_INSERT_ID());
INSERT INTO packages (segId, name, region, origin, price, description, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Kyoto - Summer 2015', 'Asia', 'Vancouver', 3500, 'Trip to traditional Japan', 40, 'Kyoto');

/*
 * North Pole
 */
INSERT INTO images (imageName, filename, type)
       VALUES ('North Pole', 'xl_rosatom-4.jpg', NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('North Pole', 'cruise', NULL, NULL, 'cruise', 4, NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Murmansk', 'flight', 'Finnair - Winter 2016', NULL, NULL, 1, LAST_INSERT_ID());
INSERT INTO packages (segId, name, region, origin, price, description, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Cruise to the North Pole', 'North Pole', 'Helsinki', 4000, 'Discover the breath of Arctic', 20, 'North Pole');

/*
 * Amazon
 */ 
INSERT INTO images (imageName, filename, type)
       VALUES ('Amazon', 'amazon_fishing.jpg', NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Dallas', 'flight', 'American Airlines - Fall 2015-8', NULL, NULL, 0, NULL);	   
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Miami', 'flight', 'American Airlines - Fall 2015-7', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Brasilia', 'flight', 'TAM Airlines - Fall 2015-6', NULL, NULL, 1, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Santarem', 'flight', 'TAM Airlines - Fall 2015-5', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Santarem', 'vehicle_v', NULL, 'Local Lodge', 'fishing', 10, LAST_INSERT_ID());	   
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Brasilia', 'flight', 'TAM Airlines - Fall 2015-4', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Miami', 'flight', 'American Airlines - Fall 2015-3', NULL, NULL, 1, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Toronto', 'flight', 'Air Canada - Fall 2015-2', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Vancouver', 'flight', 'Air Canada - Fall 2015-1', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO packages (segId, name, region, origin, price, description, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Fishing on the Amazon River', 'South America', 'Vancouver', 5500, 'Challenge the world fishing record', 5, 'Amazon');

/*
 * Istanbul
 */ 
INSERT INTO images (imageName, filename, type)
       VALUES ('Istanbul', 'istanbul.jpg', NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('London', 'flight', 'American Airlines - Summer 2015-I2', NULL, NULL, 0, NULL);	   
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Istanbul', 'flight', 'American Airlines - Summer 2015-I1', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Istanbul', 'vehicle_l', NULL, 'Shangri-La', 'sightseeing', 12, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Toronto', 'flight', 'Air Canada - Summer 2015-I2', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Vancouver', 'flight', 'Air Canada - Summer 2015-I1', NULL, NULL, 0, LAST_INSERT_ID());	   
INSERT INTO packages (segId, name, region, origin, price, description, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Istanbul - Exotic City', 'Europe', 'Vancouver', 9800, 'Feel the exotic atmosphere of Istanbul', 20, 'Istanbul');
                      
