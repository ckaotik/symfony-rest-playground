SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

INSERT INTO `product` (`id`, `name`, `description`, `image_url`, `price`, `created`, `status`) VALUES
(1, 'Boot', 'Ein altehrwürdiges Boot im rustikalen Ambiente.', 'https://picsum.photos/id/211/300/200.jpg', 9999999, '2023-12-25 11:14:44', 1),
(2, 'Oldtimer Auto', 'Diese wunderschönen Oldtimer-Modelle gibt es jetzt in mehreren Farben zum Sonderpreis. Nur solange der Vorrat reicht!', 'https://picsum.photos/id/133/300/200.jpg', 4999900, '2023-12-25 11:18:44', 1),
(3, 'Kinder-Laufrad', 'Bringt den Nachwuchs auf Touren! Das einmalige Laufrad der Marke Eigenbau ist ausschließlich in unserem Shop erhältlich.', 'https://picsum.photos/id/146/300/200.jpg', 29900, '2023-12-25 11:20:28', 1),
(4, 'Seilbahn', 'Ihre höchst eigene Seilbahn. Das kann wahrlich nicht jeder von sich sagen! Beeindrucken Sie Ihre Nachbarn und zeigen Sie allen, was Sie sich leisten können. Kein Berg zur Hand? Nutzen Sie unser XXS Modell für den Weg ins Obergeschoss. Nie wieder Treppen steigen!', 'https://picsum.photos/id/328/300/200.jpg', 999999999, '2023-12-25 11:23:46', 0);
COMMIT;
