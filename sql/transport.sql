/*
 * Data for 'transport' table
 */

INSERT INTO transport (transportId, type)
       VALUE ('bus', 'Bus'),
             ('vehicle_e', 'Economy'),
	     ('vehicle_c', 'Compact'),
	     ('vehicle_m', 'Minivan'),
	     ('vehicle_v', 'Van'),
         ('vechicle_s', 'Standard'),
   	     ('vechicle_f', 'Full size'),
	     ('vechicle_l', 'Luxury'),
	     ('vechicle_su', 'SUV'),
	     ('bike', 'Bike'),
  	     ('train', 'Train'),
	     ('ferry', 'Ferry'),
	     ('cruise', 'Cruise Ship'),
         ('flight', 'Flight');
/*
 * Add boat for amazon tour
 */
INSERT INTO transport (transportId, type)
       VALUE ('boat', 'Boat');