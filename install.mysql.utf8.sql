CREATE TABLE IF NOT EXISTS `#__download_counter` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(255) NOT NULL,
`clicks` int(11) NOT NULL,
PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;