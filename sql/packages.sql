/*
 * Data for 'packages' table
 *        segId ----> start segment Id
 */

INSERT INTO packages ( packageId, segId, name, origin, imageName, description, price, capacity, available)
       VALUES ( 1, NULL, 'Shanghai travel pack', 'Shanghai', 'p1', 'tobeupdated', 6000, 40, 40),
              ( 2, 5, 'Japan travel pack', 'vancouver', null, 'Japan sight seeing', 7000, 40, 40);
