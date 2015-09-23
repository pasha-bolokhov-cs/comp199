/*
 * Data for 'transport' table
 */

INSERT INTO transport (transportId, type)
       VALUE ('bus', 'Bus'),
             ('vehicle_e', 'Economy'),
	     ('vehicle_c', 'Compact'),
	     ('vehicle_m', 'Minivan'),
	     ('vehicle_v', 'Van'),
	     ('vehicle_s', 'Standard'),
   	     ('vehicle_f', 'Full size'),
	     ('vehicle_l', 'Luxury'),
	     ('vehicle_su', 'SUV'),
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
