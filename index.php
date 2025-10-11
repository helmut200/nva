<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFrance - Soutien Scolaire et Accompagnement vers la France</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styles généraux */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        section {
            padding: 4rem 0;
        }

        h2 {
            font-size: 2.2rem;
            color: #1a2a6c;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Header et Navigation */
        header {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            color: #fdbb2d;
            transform: translateY(-2px);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Section Hero */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 6rem 2rem;
        }

        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2.5rem;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-primary, .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #fdbb2d;
            color: #333;
        }

        .btn-primary:hover {
            background-color: #f7a800;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background-color: white;
            color: #333;
            transform: translateY(-3px);
        }

        /* À propos */
        .about {
            background-color: white;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .about-text h2 {
            text-align: left;
        }

        .about-text p {
            margin-bottom: 1.5rem;
        }

        .about-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Services */
        .services {
            background-color: #f8f9fa;
        }

        .services h2 {
            text-align: center;
            margin-bottom: 3rem;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-card .icon {
            font-size: 3rem;
            color: #1a2a6c;
            margin-bottom: 1.5rem;
        }

        .service-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #1a2a6c;
        }

        /* Témoignages */
        .testimonials {
            background-color: white;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .testimonial-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1.5rem;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #1a2a6c;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Statistiques */
        .stats {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f);
            color: white;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .stat-item {
            padding: 1.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        /* FAQ */
        .faq {
            background-color: #f8f9fa;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .faq-question {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
        }

        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .faq-item.active .faq-answer {
            padding: 0 1.5rem 1.5rem;
            max-height: 500px;
        }

        .faq-toggle {
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-toggle {
            transform: rotate(180deg);
        }

        /* Blog */
        .blog {
            background-color: white;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .blog-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-5px);
        }

        .blog-image {
            height: 200px;
            background-color: #1a2a6c;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .blog-content {
            padding: 1.5rem;
        }

        .blog-date {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .blog-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #1a2a6c;
        }

        .blog-excerpt {
            margin-bottom: 1rem;
        }

        .blog-link {
            color: #b21f1f;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Section Contact */
        .contact {
            background-color: #1a2a6c;
            color: white;
        }

        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
        }

        .contact-info h2, .contact-form h2 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .contact-info p {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: white;
            color: #1a2a6c;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background-color: #fdbb2d;
            transform: translateY(-3px);
        }

        .contact-form form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input, .form-group textarea, .form-group select {
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-btn {
            background-color: #fdbb2d;
            color: #333;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            background-color: #f7a800;
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background-color: #0d1b4d;
            color: white;
            padding: 3rem 0 1.5rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: #fdbb2d;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 0.8rem;
        }

        .footer-column ul li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: #fdbb2d;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom p {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Bouton retour en haut */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background-color: #1a2a6c;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background-color: #fdbb2d;
            transform: translateY(-3px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .modal-title {
            margin-bottom: 1.5rem;
            color: #1a2a6c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav ul {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: linear-gradient(135deg, #1a2a6c, #b21f1f);
                flex-direction: column;
                padding: 1rem 0;
                text-align: center;
            }

            nav ul.active {
                display: flex;
            }

            .mobile-menu-btn {
                display: block;
            }

            .about-content {
                grid-template-columns: 1fr;
            }

            .hero h2 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            footer p {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">
                <h1><i class="fas fa-graduation-cap"></i> NVA+</h1>
            </div>
            <ul id="nav-menu">
                <li><a href="#"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="#about"><i class="fas fa-info-circle"></i> À propos</a></li>
                <li><a href="#services"><i class="fas fa-book"></i> Services</a></li>
                <li><a href="#testimonials"><i class="fas fa-comments"></i> Témoignages</a></li>
                <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                <li><a href="#" id="login-btn"><i class="fas fa-user"></i> Espace Client</a></li>
            </ul>
            <button class="mobile-menu-btn" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h2>Votre réussite scolaire et votre projet d'études en France</h2>
            <p>Nous accompagnons les étudiants dans leur parcours scolaire et les guidons vers la réalisation de leur projet d'études en France avec une expertise reconnue.</p>
            <div class="cta-buttons">
                <a href="#" class="btn-primary" id="register-btn">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </a>
                <a href="#" class="btn-secondary">
                    <i class="fas fa-search"></i> Découvrir nos cours
                </a>
            </div>
        </section>

        <section id="about" class="about">
            <div class="container">
                <div class="about-content">
                    <div class="about-text">
                        <h2>À propos de NVA+</h2>
                        <p>Fondée en 2010, EduFrance est devenue une référence dans l'accompagnement éducatif et l'orientation vers les études en France. Notre mission est d'offrir à chaque étudiant les outils nécessaires pour réussir son parcours académique.</p>
                        <p>Notre équipe est composée d'enseignants qualifiés, de conseillers en orientation et d'experts en procédures administratives pour les études en France. Nous nous engageons à fournir un service personnalisé et de qualité.</p>
                        <a href="#" class="btn-primary">
                            <i class="fas fa-book-open"></i> En savoir plus
                        </a>
                    </div>
                    <div class="about-image">
                        <div style="height: 300px; background-color: #e9ecef; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #666;">
                            <img href="Capture d’écran 2025-02-26 110617.png">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="services">
            <div class="container">
                <h2>Nos Services </h2>
                <div class="service-grid">
                    <div class="service-card">
                        <div class="icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Soutien Scolaire Personnalisé</h3>
                        <p>Cours particuliers sur mesure dans toutes les matières, adaptés à tous les niveaux avec suivi individualisé.</p>
                    </div>
                    <div class="service-card">
                        <div class="icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3>Accompagnement France Intégral</h3>
                        <p>Guidance complète pour vos démarches d'études en France : inscriptions, visas, logement et intégration.</p>
                    </div>
                    <div class="service-card">
                        <div class="icon">
                            <i class="fas fa-language"></i>
                        </div>
                        <h3>Préparation Linguistique Excellence</h3>
                        <p>Cours de français spécialisés DELF/DALF avec préparation aux exigences académiques françaises.</p>
                    </div>
                    <div class="service-card">
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3>Préparation aux Concours</h3>
                        <p>Formation intensive pour les concours d'entrée aux grandes écoles françaises et universités.</p>
                    </div>
                    <div class="service-card">
                        <div class="icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h3>Orientation Scolaire et Professionnelle</h3>
                        <p>Bilan de compétences et accompagnement dans le choix de filière et de métier.</p>
                    </div>
                    <div class="service-card">
                        <div class="icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3>Cours en Ligne</h3>
                        <p>Plateforme d'apprentissage en ligne avec ressources pédagogiques et classes virtuelles.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number" data-target="1500">0</div>
                        <div class="stat-label">Étudiants accompagnés</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="95">0</div>
                        <div class="stat-label">% de réussite</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="50">0</div>
                        <div class="stat-label">Enseignants experts</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-target="12">0</div>
                        <div class="stat-label">Années d'expérience</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials" class="testimonials">
            <div class="container">
                <h2>Témoignages de nos étudiants</h2>
                <div class="testimonial-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Grâce à EduFrance, j'ai pu intégrer l'école d'ingénieurs de mes rêves à Paris. Leur accompagnement a été précieux tout au long du processus."
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">KD</div>
                            <div>
                                <div class="author-name">Koffi D.</div>
                                <div class="author-info">Étudiant en Génie Civil</div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Les cours de français m'ont permis d'obtenir le DALF C1 et de réussir mon intégration en France. Je recommande vivement leurs services !"
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">AM</div>
                            <div>
                                <div class="author-name">Amina M.</div>
                                <div class="author-info">Étudiante en Médecine</div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Le soutien scolaire personnalisé a transformé mes résultats. Mes notes en mathématiques sont passées de 10/20 à 16/20 en seulement 3 mois."
                        </div>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">JS</div>
                            <div>
                                <div class="author-name">Jean S.</div>
                                <div class="author-info">Lycéen en Terminale S</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="faq">
            <div class="container">
                <h2>Questions Fréquentes</h2>
                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-question">
                            Comment fonctionnent les cours de soutien scolaire ?
                            <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>Nos cours de soutien scolaire sont entièrement personnalisés. Après un premier bilan, nous définissons un programme adapté aux besoins de l'étudiant. Les cours peuvent avoir lieu en présentiel ou en ligne, selon vos préférences.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Combien de temps faut-il pour préparer un visa étudiant ?
                            <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>Nous recommandons de commencer les démarches au moins 6 mois avant la date prévue de départ. Cela permet de préparer tous les documents nécessaires et de suivre les procédures administratives sans précipitation.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Proposez-vous des préparations aux concours spécifiques ?
                            <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>Oui, nous proposons des préparations pour de nombreux concours : écoles d'ingénieurs, de commerce, médecine, etc. Nos enseignants spécialisés vous aideront à maîtriser les spécificités de chaque concours.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Quelles sont vos modalités de paiement ?
                            <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>Nous proposons plusieurs modalités de paiement : par virement bancaire, mobile money, ou en espèces. Des facilités de paiement peuvent être accordées selon les situations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="blog">
            <div class="container">
                <h2>Actualités et Conseils</h2>
                <div class="blog-grid">
                    <div class="blog-card">
                        <div class="blog-image">
                            <i class="fas fa-newspaper fa-3x"></i>
                        </div>
                        <div class="blog-content">
                            <div class="blog-date">15 Mars 2023</div>
                            <h3 class="blog-title">Nouvelles réformes des visas étudiants pour la France</h3>
                            <p class="blog-excerpt">Découvrez les dernières modifications apportées aux procédures de visa étudiant pour la France et comment nous vous accompagnons dans ces démarches.</p>
                            <a href="#" class="blog-link">Lire la suite <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="blog-card">
                        <div class="blog-image">
                            <i class="fas fa-graduation-cap fa-3x"></i>
                        </div>
                        <div class="blog-content">
                            <div class="blog-date">28 Février 2023</div>
                            <h3 class="blog-title">Comment réussir son intégration en école d'ingénieurs en France</h3>
                            <p class="blog-excerpt">Conseils pratiques pour les étudiants internationaux qui souhaitent intégrer une école d'ingénieurs en France et s'y adapter rapidement.</p>
                            <a href="#" class="blog-link">Lire la suite <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="blog-card">
                        <div class="blog-image">
                            <i class="fas fa-book fa-3x"></i>
                        </div>
                        <div class="blog-content">
                            <div class="blog-date">10 Février 2023</div>
                            <h3 class="blog-title">Les avantages du soutien scolaire régulier</h3>
                            <p class="blog-excerpt">Découvrez pourquoi un accompagnement régulier tout au long de l'année scolaire est plus efficace que des cours intensifs de dernière minute.</p>
                            <a href="#" class="blog-link">Lire la suite <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact">
            <div class="container">
                <div class="contact-container">
                    <div class="contact-info">
                        <h2>Contactez-nous</h2>
                        <p><i class="fas fa-phone"></i> +228 99844847</p>
                        <p><i class="fas fa-envelope"></i> agbegniganhelmut2@gmail.com</p>
                        <p><i class="fas fa-map-marker-alt"></i> 123 Avenue de France, 75000 Paris</p>
                        
                        <div class="social-links">
                            <a href="#" id="whatsapp-link" title="Nous contacter sur WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="#" title="Nous suivre sur Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" title="Nous suivre sur Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" title="Nous suivre sur LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-form">
                        <h2>Envoyez-nous un message</h2>
                        <form id="email-form">
                            <div class="form-group">
                                <label for="name">Nom complet</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Adresse email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Sujet</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="soutien-scolaire">Soutien scolaire</option>
                                    <option value="accompagnement-france">Accompagnement vers la France</option>
                                    <option value="cours-francais">Cours de français</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" required></textarea>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane"></i> Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>EduFrance</h3>
                    <p>Votre partenaire pour la réussite scolaire et l'accompagnement vers les études en France.</p>
                </div>
                <div class="footer-column">
                    <h3>Liens rapides</h3>
                    <ul>
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#testimonials">Témoignages</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Services</h3>
                    <ul>
                        <li><a href="#">Soutien scolaire</a></li>
                        <li><a href="#">Accompagnement France</a></li>
                        <li><a href="#">Cours de français</a></li>
                        <li><a href="#">Préparation aux concours</a></li>
                        <li><a href="#">Orientation scolaire</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> +228 99844847</li>
                        <li><i class="fas fa-envelope"></i> contact@edufrance.fr</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Avenue de France, Paris</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 EduFrance. Tous droits réservés. | <i class="fas fa-phone"></i> +228 99844847 | <i class="fas fa-envelope"></i> contact@edufrance.fr</p>
            </div>
        </div>
    </footer>

    <a href="#" class="back-to-top" id="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Modal d'inscription -->
    <div class="modal" id="register-modal">
        <div class="modal-content">
            <button class="modal-close" id="register-modal-close">&times;</button>
            <h2 class="modal-title">Inscription</h2>
            <form id="register-form">
                <div class="form-group">
                    <label for="register-name">Nom complet</label>
                    <input type="text" id="register-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Adresse email</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="register-phone">Téléphone</label>
                    <input type="tel" id="register-phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="register-service">Service souhaité</label>
                    <select id="register-service" name="service" required>
                        <option value="">Sélectionnez un service</option>
                        <option value="soutien-scolaire">Soutien scolaire</option>
                        <option value="accompagnement-france">Accompagnement vers la France</option>
                        <option value="cours-francais">Cours de français</option>
                        <option value="preparation-concours">Préparation aux concours</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>
        </div>
    </div>

    <!-- Modal de connexion -->
    <div class="modal" id="login-modal">
        <div class="modal-content">
            <button class="modal-close" id="login-modal-close">&times;</button>
            <h2 class="modal-title">Espace Client</h2>
            <form id="login-form">
                <div class="form-group">
                    <label for="login-email">Adresse email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Mot de passe</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            <p style="margin-top: 1rem; text-align: center;">
                <a href="#" style="color: #1a2a6c;">Mot de passe oublié ?</a>
            </p>
        </div>
    </div>

    <script>
        // Menu mobile
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            document.getElementById('nav-menu').classList.toggle('active');
        });

        // Bouton retour en haut
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });

        // FAQ - Accordéon
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', () => {
                // Fermer tous les autres éléments
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                
                // Ouvrir/fermer l'élément actuel
                item.classList.toggle('active');
            });
        });

        // Animations des statistiques
        const statNumbers = document.querySelectorAll('.stat-number');
        
        function animateStats() {
            statNumbers.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target'));
                const duration = 2000; // 2 secondes
                const step = target / (duration / 16); // 60fps
                let current = 0;
                
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(current);
                }, 16);
            });
        }
        
        // Observer pour déclencher l'animation des stats quand elles sont visibles
        const statsSection = document.querySelector('.stats');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(statsSection);

        // Gestion des modales
        const registerBtn = document.getElementById('register-btn');
        const registerModal = document.getElementById('register-modal');
        const registerModalClose = document.getElementById('register-modal-close');
        
        const loginBtn = document.getElementById('login-btn');
        const loginModal = document.getElementById('login-modal');
        const loginModalClose = document.getElementById('login-modal-close');
        
        registerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            registerModal.style.display = 'flex';
        });
        
        registerModalClose.addEventListener('click', function() {
            registerModal.style.display = 'none';
        });
        
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.style.display = 'flex';
        });
        
        loginModalClose.addEventListener('click', function() {
            loginModal.style.display = 'none';
        });
        
        // Fermer les modales en cliquant à l'extérieur
        window.addEventListener('click', function(e) {
            if (e.target === registerModal) {
                registerModal.style.display = 'none';
            }
            if (e.target === loginModal) {
                loginModal.style.display = 'none';
            }
        });

        // Gestion du formulaire de contact
        document.getElementById('email-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupération des données du formulaire
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            // Construction du lien mailto
            const mailtoLink = `mailto:agbegniganhelmut2@gmail.com?subject=${encodeURIComponent(subject)} - ${encodeURIComponent(name)}&body=${encodeURIComponent(message + "\n\nDe: " + name + "\nEmail: " + email)}`;
            
            // Ouverture du client de messagerie par défaut
            window.location.href = mailtoLink;
            
            // Réinitialisation du formulaire
            document.getElementById('email-form').reset();
            
            // Message de confirmation
            alert('Votre client de messagerie va s\'ouvrir. Veuillez compléter l\'envoi de votre message.');
        });
        
        // Configuration du lien WhatsApp
        document.getElementById('whatsapp-link').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Numéro de téléphone (format international sans +)
            const phoneNumber = '22899844847';
            
            // Message par défaut
            const defaultMessage = 'Bonjour, je souhaite obtenir des informations sur vos services.';
            
            // Construction du lien WhatsApp
            const whatsappLink = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(defaultMessage)}`;
            
            // Ouverture de WhatsApp
            window.open(whatsappLink, '_blank');
        });

        // Gestion des formulaires des modales
        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Merci pour votre inscription ! Nous vous contacterons rapidement.');
            registerModal.style.display = 'none';
            this.reset();
        });
        
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Connexion réussie ! Redirection vers votre espace client...');
            loginModal.style.display = 'none';
            this.reset();
        });

        // Navigation fluide
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Fermer le menu mobile si ouvert
                    document.getElementById('nav-menu').classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>