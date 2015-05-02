/*
 * package segments data
 * 
 * 
 */

/* *** why is this using "activities" table? *** */
INSERT INTO segments ( segId, transportId, flightId, location, hotelId, activityId, duration, nextSeg)
       VALUES ( 1, 'flight', null, 'osaka', 'Hilton', 'climbing', 4, 2),
              ( 2, 'flight', null, 'tokyo', 'Hilton', 'fireworks', 2, null),
              ( 3, 'flight', null, 'osaka', 'Hilton', 'climbing', 4, 4),
              ( 4, 'flight', null, 'hakata', 'Hilton', 'fireworks', 3, null),
              ( 5, 'flights', 'AirCanada_Vancouver0501', 'tokyo', 'Hilton', null, 1, 6),
	      ( 6, 'bus', null, 'tokyo', 'Hilton', 'sightseeing', 1, 7),
      	      ( 7, 'train', null, 'kyoto', 'HolidayInn', 'sightseeing', 3, 8),
	      ( 8, 'train', null, 'osaka', 'Hilton', 'sightseeing', 1, 9),
	      ( 9, 'flights', 'NipponAir_Tokyo0508', 'vancouver', null, null, 1, null);
