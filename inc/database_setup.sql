SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

CREATE TABLE `Gallery` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `GalleryContents` (
  `gallery` tinyint(4) NOT NULL DEFAULT '0',
  `image` varchar(200) NOT NULL DEFAULT '',
  `number` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `Images` (
  `filename` varchar(200) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `Gallery`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `GalleryContents`
  ADD PRIMARY KEY (`gallery`,`image`);

ALTER TABLE `Images`
  ADD PRIMARY KEY (`filename`);
COMMIT;