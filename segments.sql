/*
 * package segments data
 * flight ID is not "int" in the flight table
 * 
 */

/* *** why is this using "activities" table? *** */
INSERT INTO segments ( tripNo, transportId, flightId, locationId, hotelId, activityId, duration, dealPrice, nextSeg)
       VALUES ( 1, 'flight', null, 'osaka', 'Hilton', 'climbing', 4, 6000, 2),
              ( 2, 'flight', null, 'tokyo', 'Hilton', 'fireworks', 2, null, null),
              ( 3, 'flight', null, 'osaka', 'Hilton', 'climbing', 4, 6000, 4),
              ( 4, 'flight', null, 'hakata', 'Hilton', 'fireworks', 3, null, null),
              ( 5, 'flights', 19, 'tokyo', 'Hilton', null, 1, null, 6),
	      ( 6, 'bus', null, 'tokyo', 'Hilton', 'sightseeing', 1, null, 7),
      	      ( 7, 'train', null, 'kyoto', 'HolidayInn', 'sightseeing', 3, null, 8),
	      ( 8, 'train', null, 'osaka', 'Hilton', 'sightseeing', 1, null, 9),
	      ( 9, 'flights', 24, 'vancouver', null, null, 1, null, null);
