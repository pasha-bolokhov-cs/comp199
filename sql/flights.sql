/*
 *  Data for 'flights' tables
 *    How deal with the flightId for return trip flight on same company? 
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
/*
 * ADD flights for Amazon fishing tour
 */
INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
       VALUES ('Air Canada - Fall 2015-1', 'AC034', 'Vancouver', STR_TO_DATE('09/09/2015 09:00', '%d/%m/%Y %H:%i'),
                                                    'Toronto', STR_TO_DATE('09/09/2015 16:24', '%d/%m/%Y %H:%i')),
              ('Air Canada - Fall 2015-2', 'AC918', 'Toronto', STR_TO_DATE('09/09/2015 18:00', '%d/%m/%Y %H:%i'),
                                                        'Miami', STR_TO_DATE('09/09/2015 21:15', '%d/%m/%Y %H:%i')),
              ('American Airlines - Fall 2015-3', 'AA213', 'Miami', STR_TO_DATE('09/09/2015 23:54', '%d/%m/%Y %H:%i'),
                                                             'Brasilia', STR_TO_DATE('10/09/2015 08:27', '%d/%m/%Y %H:%i'));
INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
  		VALUES('TAM Airlines - Fall 2015-4', 'TAM3098', 'Brasilia', STR_TO_DATE('10/09/2015 12:08', '%d/%m/%Y %H:%i'),
                                                             'Santarem', STR_TO_DATE('10/09/2015 14:57', '%d/%m/%Y %H:%i'));
															 
/* Return */
INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
       VALUES ('TAM Airlines - Fall 2015-5', 'TAM3449', 'Santarem', STR_TO_DATE('20/09/2015 03:00', '%d/%m/%Y %H:%i'),
                                                    'Brasilia', STR_TO_DATE('20/09/2015 04:23', '%d/%m/%Y %H:%i')),
              ('TAM Airlines - Fall 2015-6', 'TAM8092', 'Brasilia', STR_TO_DATE('20/09/2015 16:13', '%d/%m/%Y %H:%i'),
                                                        'Miami', STR_TO_DATE('20/09/2015 21:30', '%d/%m/%Y %H:%i')),
              ('American Airlines - Fall 2015-7', 'AA061', 'Miami', STR_TO_DATE('21/09/2015 06:00', '%d/%m/%Y %H:%i'),
                                                           'Dallas', STR_TO_DATE('21/09/2015 08:07', '%d/%m/%Y %H:%i'));
INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
	   VALUES ('American Airlines - Fall 2015-8', 'AA1189', 'Dallas', STR_TO_DATE('21/09/2015 09:25', '%d/%m/%Y %H:%i'),
                                                            'Vancouver', STR_TO_DATE('21/09/2015 11:47', '%d/%m/%Y %H:%i'));
/* Istanbul */
INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
       VALUES ('Air Canada - Summer 2015-I1', 'AC162', 'Vancouver', STR_TO_DATE('06/07/2015 23:30', '%d/%m/%Y %H:%i'),
                                                    'Toronto', STR_TO_DATE('07/07/2015 06:55', '%d/%m/%Y %H:%i')),
              ('Air Canada - Summer 2015-I2', 'AC810', 'Toronto', STR_TO_DATE('07/07/2015 16:25', '%d/%m/%Y %H:%i'),
                                                        'Istanbul', STR_TO_DATE('08/07/2015 09:25', '%d/%m/%Y %H:%i'));
/* Return */      
INSERT INTO flights (flightId, flightNo, origin, departDate, destination, arriveDate)
       VALUES ('American Airlines - Summer 2015-I1', 'AC6612', 'Istanbul', STR_TO_DATE('20/07/2015 08:50', '%d/%m/%Y %H:%i'),
                                                    'London', STR_TO_DATE('20/07/2015 11:00', '%d/%m/%Y %H:%i')),
              ('American Airlines - Summer 2015-I2', 'AC810', 'London', STR_TO_DATE('20/07/2015 12:40', '%d/%m/%Y %H:%i'),
                                                        'Vancouver', STR_TO_DATE('08/09/2015 14:00', '%d/%m/%Y %H:%i'));															 															 