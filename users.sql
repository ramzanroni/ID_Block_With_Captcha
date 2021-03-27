CREATE TABLE IF NOT EXISTS `users` (
`id` int(8) NOT NULL,
  `user_name` varchar(55) NOT NULL,
  `password` varchar(12) NOT NULL,
  `display_name` varchar(55) NOT NULL
)

INSERT INTO `users` (`id`, `user_name`, `password`, `display_name`) VALUES
(1, 'admin', 'admin', 'Admin');