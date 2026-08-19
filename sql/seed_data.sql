USE covoiturage;

INSERT INTO agencies (name) VALUES
('Paris'),('Lyon'),('Marseille'),('Toulouse'),('Nice'),('Nantes'),
('Strasbourg'),('Montpellier'),('Bordeaux'),('Lille'),('Rennes'),('Reims');

-- Mot de passe 'secret' hashé en SHA256
INSERT INTO users (last_name, first_name, phone, email, password_hash, is_admin) VALUES
('Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Dubois', 'Sophie', '0698765432', 'sophie.dubois@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Bernard', 'Julien', '0622446688', 'julien.bernard@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Moreau', 'Camille', '0611223344', 'camille.moreau@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Lefevre', 'Lucie', '0777889900', 'lucie.lefevre@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Leroy', 'Thomas', '0655443322', 'thomas.leroy@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Roux', 'Chloe', '0633221199', 'chloe.roux@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Petit', 'Maxime', '0766778899', 'maxime.petit@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Garnier', 'Laura', '0688776655', 'laura.garnier@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Dupuis', 'Antoine', '0744556677', 'antoine.dupuis@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Lefebvre', 'Emma', '0699887766', 'emma.lefebvre@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Fontaine', 'Louis', '0655667788', 'louis.fontaine@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Chevalier', 'Clara', '0788990011', 'clara.chevalier@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Robin', 'Nicolas', '0644332211', 'nicolas.robin@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Gauthier', 'Marine', '0677889922', 'marine.gauthier@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Fournier', 'Pierre', '0722334455', 'pierre.fournier@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Girard', 'Sarah', '0688665544', 'sarah.girard@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Lambert', 'Hugo', '0611223366', 'hugo.lambert@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Masson', 'Julie', '0733445566', 'julie.masson@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0),
('Henry', 'Arthur', '0666554433', 'arthur.henry@email.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 0);

-- Admin (id 21)
INSERT INTO users (last_name, first_name, phone, email, password_hash, is_admin) VALUES
('Admin', 'Super', '0102030405', 'admin@covoiturage.fr', '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b', 1);

-- Quelques trajets
INSERT INTO trips (departure_agency_id, arrival_agency_id, departure_datetime, arrival_datetime, total_seats, available_seats, user_id) VALUES
(1, 2, '2026-09-01 09:00:00', '2026-09-01 11:00:00', 3, 3, 2),
(2, 3, '2026-09-02 14:00:00', '2026-09-02 15:00:00', 4, 4, 3),
(1, 9, '2026-09-03 08:00:00', '2026-09-03 11:00:00', 2, 2, 1),
(10, 6, '2026-09-04 10:00:00', '2026-09-04 14:00:00', 3, 0, 4),
(4, 5, '2026-09-05 16:00:00', '2026-09-05 18:00:00', 5, 5, 5);