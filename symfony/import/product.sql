SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

INSERT INTO `product` (`id`, `name`, `description`, `image_url`, `price`, `created`, `status`) VALUES
(1, 'Boat', 'A time-honored boat in a rustic ambience.', 'https://picsum.photos/id/211/300/200.jpg', 9999999, '2023-12-25 11:14:44', 1),
(2, 'Oldtimer car', 'These beautiful vintage models are now available in several colors at a special price. Only while stocks last!', 'https://picsum.photos/id/133/300/200.jpg', 4999900, '2023-12-25 11:18:44', 1),
(3, 'Balance bike', 'Get the little ones going! The unique balance bike of the brand Eigenbau is only available in our store.', 'https://picsum.photos/id/146/300/200.jpg', 29900, '2023-12-25 11:20:28', 1),
(4, 'Cable car', 'Your very own cable car. Not everyone can say that about themselves! Impress your neighbors and show everyone what you can afford. No mountain at hand? Use our XXS model to get to the second floor. Never climb stairs again!', 'https://picsum.photos/id/328/300/200.jpg', 999999999, '2023-12-25 11:23:46', 0);
COMMIT;
