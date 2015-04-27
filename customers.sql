/*
 * Customer Account Information
 */

INSERT INTO customers (name, birth, nationality, passportNo, passportExp, email, phone)
       VALUES ('Paul Walker', to_date('1973-09-12'), 'America', 'C01234567', to_date('2017-06-30'), 'PWalker@gmail.com', '1250349876'),
	      ('Sam Shao', to_date('1990-09-12'), 'China', 'C01230067', to_date('2016-06-30'), 'SShao@gmail.com', '1250349886');
