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
INSERT INTO packages (segId, name, origin, price, description, capacity, available, imageName)
       VALUES (LAST_INSERT_ID(), 'Hawaii - Summer 2015', 'San Francisco', 700, 'Breath-taking vacation on Hawaii', 40, 40, NULL);

