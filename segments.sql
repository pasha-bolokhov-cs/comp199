/*
 * package segments data
 * flight ID is not "int" in the flight table
 */

INSERT INTO activities (segId, tripNo, transportId,flightId, locationId, hotelId, activityId, duration, dealPrice, nextSeg)
       VALUES (1, 1, 'flight', null,'osaka', 'Hilton', 'climbing', 4, 6000, 2),
              (2, 2, 'flight', null,'tokyo', 'Hilton', 'fireworks', 2, null, null),
              (3, 3, 'flight', null,'osaka', 'Hilton', 'climbing', 4, 6000, 4),
              (4, 4, 'flight', null,'hakata', 'Hilton', 'fireworks', 3, null, null)
	     ; 
