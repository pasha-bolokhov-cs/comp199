/*
 * Data for 'packages' table
 *        segId ----> start segment Id
 */


/*
 * Hawaii
 */
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Honolulu', NULL, NULL, 'Hilton', 'surfing', 2, NULL);
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Honolulu', 'flight', 'Hawaiian Airlines - Summer 2015', 'Hilton', 'sightseeing', 4, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Los Angeles', 'flight', 'American Airlines - Summer 2015', NULL, NULL, 0, LAST_INSERT_ID());
INSERT INTO packages (segId, name, region, origin, price, description, capacity, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Hawaii - Summer 2015', 'North America', 'San Francisco', 700, 'Breath-taking vacation on Hawaii', 40, 40, NULL);

/*
 * Japan
 */
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Tokyo', 'flight', 'All Nippon Airlines - Summer 2015', NULL, NULL, 1, NULL );
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Kyoto', 'train', NULL, 'Hilton', 'sightseeing', 6, LAST_INSERT_ID());
INSERT INTO segments (location, transportId, flightId, hotelId, activityId, duration, nextSeg)
       VALUES ('Vancouver', 'flight', 'Air Canada -Summer 2015', NULL, NULL, 1, LAST_INSERT_ID());
INSERT INTO packages (segId, name, region, origin, price, description, capacity, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Kyoto - Summer 2015', 'Asia', 'Vancouver', 3500, 'Trip to traditional Japan', 40, 40, NULL);
