SET foreign_key_checks = 0; #turn off constraints temporarily

#since constraints cause problems, drop tables first, working backward
DROP TABLE IF EXISTS sp17_feed;
DROP TABLE IF EXISTS sp17_Categories;
DROP TABLE IF EXISTS sp17_news;

#all tables must be of type InnoDB to do transactions, foreign key constraints
CREATE TABLE sp17_news(
NewsID INT UNSIGNED NOT NULL AUTO_INCREMENT,
AdminID INT UNSIGNED DEFAULT 0,
Title VARCHAR(255) DEFAULT '',
Description TEXT DEFAULT '',
DateAdded DATETIME,
LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
TimesViewed INT DEFAULT 0,
Status INT DEFAULT 0,
PRIMARY KEY (NewsID)
)ENGINE=INNODB;

#assigning first News to AdminID == 1
INSERT INTO sp17_news VALUES (NULL,1,'Our First RSS','Description of RSS',NOW(),NOW(),0,0);


#foreign key field must match size and type, hence NewsID is INT UNSIGNED
CREATE TABLE sp17_Categories(
CategoriesID INT UNSIGNED NOT NULL AUTO_INCREMENT,
NewsID INT UNSIGNED DEFAULT 0,
Categories TEXT DEFAULT '',
Description TEXT DEFAULT '',
DateAdded DATETIME,
LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (CategoriesID),
INDEX NewsID_index(NewsID),
FOREIGN KEY (NewsID) REFERENCES sp17_news(NewsID) ON DELETE CASCADE
)ENGINE=INNODB;



INSERT INTO sp17_Categories VALUES (NULL,1,'Entertainment','USA Top Entertainment News',NOW(),NOW());
INSERT INTO sp17_Categories VALUES (NULL,1,'Soprts','More News For Sports ',NOW(),NOW());
INSERT INTO sp17_Categories VALUES (NULL,1,'World','Word News',NOW(),NOW());






CREATE TABLE sp17_Feed(
FeedID INT UNSIGNED NOT NULL AUTO_INCREMENT,
CategoriesID INT UNSIGNED DEFAULT 0,
Feed TEXT DEFAULT '',
Description TEXT DEFAULT '',
DateAdded DATETIME,
LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
Status INT DEFAULT 0,
PRIMARY KEY (FeedID),
INDEX CategoriesID_index(CategoriesID),
FOREIGN KEY (CategoriesID) REFERENCES sp17_Categories(CategoriesID) ON DELETE CASCADE
)ENGINE=INNODB;

INSERT INTO sp17_Feed VALUES (NULL,1,'First Link','More',NOW(),NOW());
INSERT INTO sp17_Feed VALUES (NULL,1,'Second Link','More',NOW(),NOW());
