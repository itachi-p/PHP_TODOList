DROP TABLE todolist;

CREATE TABLE todolist
    (id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
     subject varchar(30) NOT NULL,
     staff varchar(21) NOT NULL,
     term date NOT NULL,
     done varchar(10) NOT NULL DEFAULT '未'
     );

INSERT INTO todolist (subject, staff, term, done) VALUES
('項目1', 'Jason', '2019/03/10', '未'),
('項目2', 'Gandhi', '2019/02/28', '2019/02/10'),
('項目3', 'Siddhattha', '2020/01/25', '2018/12/20'),
('項目4', 'staff0', '2019/03/20', '未'),
('項目5', 'staff7', '2019/02/28', '2019/02/11'),
('項目6', 'Gandhi', '2020/03/31', '未'),
('項目7', 'staff0','2019/02/15', '未'),
('項目8', 'staff0', DATE_FORMAT(now() - INTERVAL 1 MONTH, '%Y/%m/%d'), '未'),
('項目9', 'staff0', '2018/12/10', '2019/01/01'),
('項目10', 'staff7', '2019/02/04', '未'),
('項目11', 'staff7', '2019/02/04', '2019/02/08'),
('項目12', 'staff7', '2019/02/10', '2019/02/08'),
('項目13', 'staff1', DATE_FORMAT(now() + INTERVAL 2 WEEK,'%Y/%m/%d'), '2019/02/11'),
('項目14', 'staff2', '2019/03/31', '未'),
('項目15', 'staff2', '2019/01/31', '未'),
('項目16', 'staff2', '2019/02/28', '2019/02/11'),
('項目17', 'staff3', '2019/02/15', '未'),
('項目18', 'staff7', DATE_FORMAT(now() + INTERVAL 10 DAY, '%Y/%m/%d'), '未'),
('項目19', 'staff7', '2019/01/25', '2019/01/23'),
('項目20', 'staff7', '2019/01/31', '2019/02/01')
;