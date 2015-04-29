/*
 *  Data for 'flights' table
 */
       
INSERT INTO flights(alliance, departDate, arriveDate)
       VALUE ('AC003', STR_TO_DATE('05/01/15 13:40','%m/%d/%y %H:%i'), STR_TO_DATE('05/02/15 15:20', '%m/%d/%y %H:%i')),
             ('JL017', STR_TO_DATE('05/01/15 14:15','%m/%d/%y %H:%i'), STR_TO_DATE('05/02/15 16:30', '%m/%d/%y %H:%i')),
             ('NH115', STR_TO_DATE('05/01/15 16:20','%m/%d/%y %H:%i'), STR_TO_DATE('05/02/15 18:30', '%m/%d/%y %H:%i')),
             ('AC004', STR_TO_DATE('05/08/15 17:30','%m/%d/%y %H:%i'), STR_TO_DATE('05/08/12 10:15', '%m/%d/%y %H:%i')),
             ('JL018', STR_TO_DATE('05/08/15 18:20','%m/%d/%y %H:%i'), STR_TO_DATE('05/08/15 11:25', '%m/%d/%y %H:%i')),
             ('NH116', STR_TO_DATE('05/08/15 21:50','%m/%d/%y %H:%i'), STR_TO_DATE('05/08/15 11:25', '%m/%d/%y %H:%i'));

