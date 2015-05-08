/*
 * Data for 'locations' table
 */

INSERT INTO locations (city, region, country)
       VALUES ('Shanghai', 'Asia', 'China'),
	      ('Tokyo', 'Asia', 'Japan'),
	      ('Kyoto', 'Asia', 'Japan'),   
	      ('Osaka', 'Asia', 'Japan'),
	      ('Hakata', 'Asia', 'Japan'),
	      ('Sochi', 'Europe', 'Russia'),
	      ('Paris', 'Europe', 'France'),
	      ('Victoria', 'North America', 'Canada'),
	      ('Vancouver', 'North America', 'Canada'),
	      ('Seattle', 'North America', 'US'),
	      ('Honolulu', 'North America', 'US'),
	      ('Los Angeles', 'North America', 'US'),
	      ('San Francisco', 'North America', 'US'),
	      ('Murmansk', 'Europe', 'Russia'),
	      ('North Pole', 'North Pole', NULL),
	      ('Helsinki', 'Europe', 'Finland');		  

/*
 * ADD cities for Amazon
 */ 
INSERT INTO locations (city, region, country)
       VALUES ('Miami', 'North America', 'US'),
	      ('Dallas', 'North America', 'US'),
		  ('Toronto', 'North America', 'Canada');   		  
INSERT INTO locations (city, region, country)
	   VALUES ('Brasilia', 'South America', 'Brazil'),
	      ('Santarem', 'South America', 'Brazil'); 
		