/*
 *  Data for 'flights' tables
 *    How deal with the flightId for return trip fligh on same company? 
 */

/*       
INSERT INTO flights(flightId, flightNo, departDate, arriveDate)
       VALUES ('AirCanada_Vancouover0501', 'AC003', STR_TO_DATE('05/01/15 13:40','%m/%d/%y %H:%i'), STR_TO_DATE('05/02/15 15:20', '%m/%d/%y %H:%i')),
              ('JapanAir_Vancouver0501', 'JL017', STR_TO_DATE('05/01/15 14:15','%m/%d/%y %H:%i'), STR_TO_DATE('05/02/15 16:30', '%m/%d/%y %H:%i')),
              ('NipponAir_Vancouver0501', 'NH115', STR_TO_DATE('05/01/15 16:20','%m/%d/%y %H:%i'), STR_TO_DATE('05/02/15 18:30', '%m/%d/%y %H:%i')),
              ('AirCanada_Tokyo0508','AC004', STR_TO_DATE('05/08/15 17:30','%m/%d/%y %H:%i'), STR_TO_DATE('05/08/12 10:15', '%m/%d/%y %H:%i')),
              ('JapanAir_Tokyo0508', 'JL018', STR_TO_DATE('05/08/15 18:20','%m/%d/%y %H:%i'), STR_TO_DATE('05/08/15 11:25', '%m/%d/%y %H:%i')),
              ('NipponAir_Tokyo0508', 'NH116', STR_TO_DATE('05/08/15 21:50','%m/%d/%y %H:%i'), STR_TO_DATE('05/08/15 11:25', '%m/%d/%y %H:%i'));
*/

INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
       VALUES ('American Airlines - Summer 2015', 'AA141', 'San Francisco', STR_TO_DATE('10/07/2015 7:00', '%d/%m/%Y %H:%i'), 
							   'Los Angeles', STR_TO_DATE('10/07/2015 8:00', '%d/%m/%Y %H:%i')),
	      ('Hawaiian Airlines - Summer 2015', 'HA2562', 'Los Angeles', STR_TO_DATE('10/07/2015 10:00', '%d/%m/%Y %H:%i'), 
							    'Honolulu', STR_TO_DATE('10/07/2015 14:00', '%d/%m/%Y %H:%i')),
	      ('Finnair - Winter 2016', 'AY315', 'Helsinki', STR_TO_DATE('20/01/2016 10:00', '%d/%m/%Y %H:%i'),
					         'Murmansk', STR_TO_DATE('20/01/2016 14:00', '%d/%m/%Y %H:%i'));

/*
 *  ADD OLD DATA
 */
INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
       VALUES ('Air Canada - Summer 2015', 'AC003', 'Vancouver', STR_TO_DATE('01/05/2015 13:40', '%d/%m/%Y %H:%i'),
                                                    'Tokyo', STR_TO_DATE('02/05/2015 15:20', '%d/%m/%Y %H:%i')),
              ('Japan Airlines - Summer 2015', 'JL017', 'Vancouver', STR_TO_DATE('01/05/2015 14:15', '%d/%m/%Y %H:%i'),
                                                        'Tokyo', STR_TO_DATE('02/05/2015 16:30', '%d/%m/%Y %H:%i')),
              ('All Nippon Airlines - Summer 2015', 'NH116', 'Tokyo', STR_TO_DATE('08/05/2015 21:50', '%d/%m/%Y %H:%i'),
                                                             'Vancouver', STR_TO_DATE('08/05/2015 11:25', '%d/%m/%Y %H:%i'));
