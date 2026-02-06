DELIMITER $$
CREATE DEFINER=`learning`@`localhost` PROCEDURE `GetAudioLog`()
    NO SQL
SELECT * FROM AudioLog$$
DELIMITER ;