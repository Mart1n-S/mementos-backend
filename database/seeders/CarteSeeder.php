<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use App\Models\Carte;

class CarteSeeder extends Seeder
{
    protected $themeQuestions = [
        'La Renaissance' => [
            ['question' => "Quelle invention a révolutionné l'Europe pendant la Renaissance ?", 'reponse' => "L'imprimerie"],
            ['question' => "Quel artiste est célèbre pour ses peintures de la chapelle Sixtine ?", 'reponse' => "Michel-Ange"],
            ['question' => "En quelle année a été peinte la Joconde ?", 'reponse' => "Vers 1503"],
            ['question' => "Quel explorateur a découvert la route maritime vers l'Inde en contournant le cap de Bonne-Espérance ?", 'reponse' => "Vasco de Gama"],
            ['question' => "Quel humaniste a écrit 'Éloge de la folie' ?", 'reponse' => "Érasme"]
        ],
        'La Révolution française' => [
            ['question' => "Quelle année a vu le début de la Révolution française ?", 'reponse' => "1789"],
            ['question' => "Quel roi de France a été exécuté pendant la Révolution ?", 'reponse' => "Louis XVI"],
            ['question' => "Quelle femme a dirigé les sans-culottes pendant la Révolution ?", 'reponse' => "Théroigne de Méricourt"],
            ['question' => "Quel général français a mené les armées révolutionnaires en Italie ?", 'reponse' => "Napoléon Bonaparte"],
            ['question' => "Quelle bataille a vu la défaite de Napoléon en 1815 ?", 'reponse' => "Waterloo"]
        ],
        'Introduction à Python' => [
            ['question' => "Qu'est-ce qu'une liste en Python ?", 'reponse' => "Une collection ordonnée et modifiable"],
            ['question' => "Comment créer une fonction en Python ?", 'reponse' => "Utiliser le mot clé def"],
            ['question' => "Quelle structure de données en Python est utilisée pour stocker des paires clé-valeur ?", 'reponse' => "Un dictionnaire"],
            ['question' => "Comment gérer les exceptions en Python ?", 'reponse' => "Avec les blocs try et except"],
            ['question' => "Comment installer une bibliothèque externe en Python ?", 'reponse' => "Utiliser pip, le gestionnaire de paquets pour Python"]
        ],
        'Développement Web avec JavaScript' => [
            ['question' => "Qu'est-ce que JavaScript ?", 'reponse' => "Un langage de programmation pour le web"],
            ['question' => "Comment sélectionner un élément HTML avec JavaScript ?", 'reponse' => "Utiliser document.querySelector()"],
            ['question' => "Quelle méthode est utilisée pour envoyer des requêtes HTTP en JavaScript ?", 'reponse' => "fetch()"],
            ['question' => "Qu'est-ce que JSON et pourquoi est-il utilisé avec JavaScript ?", 'reponse' => "Un format de données pour l'échange de données"],
            ['question' => "Qu'est-ce que Node.js et pourquoi est-il utilisé avec JavaScript ?", 'reponse' => "Un environnement d'exécution JavaScript côté serveur"]
        ],
        'Grammaire anglaise pour débutants' => [
            ['question' => "Quelle est la forme correcte du verbe 'to be' au présent simple ?", 'reponse' => "I am, you are, he/she/it is, we/you/they are"],
            ['question' => "Comment former une phrase négative en anglais ?", 'reponse' => "Ajouter 'not' après le verbe auxiliaire"],
            ['question' => "Quelle est la différence entre 'much' et 'many' ?", 'reponse' => "'Much' est utilisé avec des noms non comptables, 'many' avec des noms comptables"],
            ['question' => "Quelle est la forme comparative de 'good' ?", 'reponse' => "Better"],
            ['question' => "Quelle est la forme superlative de 'far' ?", 'reponse' => "Farthest"]
        ],
        'Conversation avancée' => [
            ['question' => "Comment exprimer une hypothèse en anglais ?", 'reponse' => "Utiliser 'if' ou 'unless'"],
            ['question' => "Quelle est la différence entre 'will' et 'going to' pour exprimer le futur ?", 'reponse' => "'Will' est utilisé pour les décisions spontanées, 'going to' pour les intentions prévues"],
            ['question' => "Quelle est la structure correcte pour une phrase conditionnelle de type 1 ?", 'reponse' => "If + présent simple, futur simple"],
            ['question' => "Comment exprimer une obligation en anglais ?", 'reponse' => "Utiliser 'have to' ou 'must'"],
            ['question' => "Quelle est la différence entre 'during' et 'while' ?", 'reponse' => "'During' est suivi d'un nom, 'while' d'une phrase"]
        ],
        'Culture japonaise' => [
            ['question' => "Quelle est la cérémonie traditionnelle du thé au Japon ?", 'reponse' => "Le chanoyu"],
            ['question' => "Quel art martial japonais signifie 'voie du sabre' ?", 'reponse' => "Kendo"],
            ['question' => "Quel est le nom du théâtre japonais traditionnel ?", 'reponse' => "Nô"],
            ['question' => "Quel est le nom du kimono porté par les hommes ?", 'reponse' => "Yukata"],
            ['question' => "Quel est le nom du kimono porté par les femmes ?", 'reponse' => "Komon"]
        ],
        'Histoire des États-Unis' => [
            ['question' => "Quelle guerre a vu la victoire des États-Unis sur l'Espagne en 1898 ?", 'reponse' => "La guerre hispano-américaine"],
            ['question' => "Quel président a signé la Déclaration d'indépendance des États-Unis ?", 'reponse' => "Thomas Jefferson"],
            ['question' => "Quel événement a marqué le début de la guerre de Sécession en 1861 ?", 'reponse' => "La bataille de Fort Sumter"],
            ['question' => "Quel président a été assassiné en 1865 après la fin de la guerre de Sécession ?", 'reponse' => "Abraham Lincoln"],
            ['question' => "Quel est le nom du premier président des États-Unis ?", 'reponse' => "George Washington"]
        ],
        'Sujets divers en science' => [
            ['question' => "Quelle est la plus petite particule de matière ?", 'reponse' => "Le quark"],
            ['question' => "Quelle est la force qui maintient les planètes en orbite autour du Soleil ?", 'reponse' => "La gravité"],
            ['question' => "Quelle est la vitesse de la lumière dans le vide ?", 'reponse' => "299 792 458 m/s"],
            ['question' => "Quelle est la formule de la relativité restreinte d'Einstein ?", 'reponse' => "E=mc²"],
            ['question' => "Quelle est la théorie qui décrit l'origine de l'univers ?", 'reponse' => "Le Big Bang"]
        ],
        'Innovations en technologie' => [
            ['question' => "Quelle entreprise a été fondée par Steve Jobs, Steve Wozniak et Ronald Wayne en 1976 ?", 'reponse' => "Apple Inc."],
            ['question' => "Quel est le nom du premier ordinateur personnel commercialisé par IBM en 1981 ?", 'reponse' => "IBM PC"],
            ['question' => "Quelle technologie de stockage utilise des disques magnétiques rotatifs ?", 'reponse' => "Le disque dur"],
            ['question' => "Quel est le nom du premier navigateur web créé par Tim Berners-Lee en 1990 ?", 'reponse' => "WorldWideWeb"],
            ['question' => "Quelle entreprise a lancé le premier smartphone Android en 2008 ?", 'reponse' => "HTC"]
        ],
        'Les bases de la physique quantique' => [
            ['question' => "Quel physicien a proposé la théorie des quanta en 1900 ?", 'reponse' => "Max Planck"],
            ['question' => "Quel est le nom de la particule élémentaire qui compose les électrons et les protons ?", 'reponse' => "Le quark"],
            ['question' => "Quelle expérience célèbre a montré le caractère ondulatoire de la lumière ?", 'reponse' => "L'expérience de Young"],
            ['question' => "Quel est le nom de la théorie qui décrit le comportement des particules à l'échelle quantique ?", 'reponse' => "La mécanique quantique"],
            ['question' => "Quel est le nom de la particule qui transmet la force électromagnétique ?", 'reponse' => "Le photon"]
        ],
        'Biologie moderne' => [
            ['question' => "Quel est le nom du processus par lequel les plantes produisent de la nourriture ?", 'reponse' => "La photosynthèse"],
            ['question' => "Quel est le nom de la molécule qui contient les instructions génétiques ?", 'reponse' => "L'ADN"],
            ['question' => "Quel est le nom de la théorie qui explique l'évolution des espèces par la sélection naturelle ?", 'reponse' => "La théorie de l'évolution"],
            ['question' => "Quel est le nom de la cellule reproductrice mâle chez les plantes ?", 'reponse' => "Le pollen"],
            ['question' => "Quel est le nom de la cellule reproductrice femelle chez les animaux ?", 'reponse' => "L'ovule"]
        ],
        'Algèbre de base' => [
            ['question' => "Quelle est la formule pour calculer l'aire d'un rectangle ?", 'reponse' => "Longueur x largeur"],
            ['question' => "Quelle est la formule pour calculer le périmètre d'un carré ?", 'reponse' => "4 x côté"],
            ['question' => "Quelle est la formule pour calculer le volume d'un cube ?", 'reponse' => "Côté x côté x côté"],
            ['question' => "Quelle est la formule pour calculer la pente d'une droite ?", 'reponse' => "(y2 - y1) / (x2 - x1)"],
            ['question' => "Quelle est la formule pour résoudre une équation quadratique ?", 'reponse' => "ax² + bx + c = 0"]
        ],
        'Calcul différentiel' => [
            ['question' => "Quelle est la définition de la dérivée d'une fonction ?", 'reponse' => "Le taux de variation instantané"],
            ['question' => "Quelle est la règle pour dériver une somme de fonctions ?", 'reponse' => "La somme des dérivées"],
            ['question' => "Quelle est la règle pour dériver un produit de fonctions ?", 'reponse' => "Le produit des dérivées"],
            ['question' => "Quelle est la règle pour dériver un quotient de fonctions ?", 'reponse' => "La dérivée du numérateur fois le dénominateur moins le numérateur fois la dérivée du dénominateur"],
            ['question' => "Quelle est la règle pour dériver une fonction composée ?", 'reponse' => "La dérivée de la fonction extérieure évaluée en la fonction intérieure fois la dérivée de la fonction intérieure"]
        ],
        'Impressionnisme' => [
            ['question' => "Quel artiste est considéré comme le fondateur de l'impressionnisme ?", 'reponse' => "Claude Monet"],
            ['question' => "Quel est le nom de la première exposition impressionniste en 1874 ?", 'reponse' => "Exposition des peintres impressionnistes"],
            ['question' => "Quel artiste est célèbre pour ses peintures de danseuses et de ballerines ?", 'reponse' => "Edgar Degas"],
            ['question' => "Quel artiste est connu pour ses peintures de champs de coquelicots ?", 'reponse' => "Vincent van Gogh"],
            ['question' => "Quel artiste a peint 'Un dimanche après-midi à l'Île de la Grande Jatte' ?", 'reponse' => "Georges Seurat"]
        ],
        'Art contemporain' => [
            ['question' => "Quel artiste est célèbre pour ses sculptures en acier inoxydable ?", 'reponse' => "Jeff Koons"],
            ['question' => "Quel est le nom du mouvement artistique qui utilise des images de la culture populaire ?", 'reponse' => "Le pop art"],
            ['question' => "Quel artiste est connu pour ses peintures de drapeaux américains ?", 'reponse' => "Jasper Johns"],
            ['question' => "Quel artiste est célèbre pour ses installations artistiques en milieu urbain ?", 'reponse' => "Banksy"],
            ['question' => "Quel artiste est connu pour ses peintures de femmes aux formes géométriques ?", 'reponse' => "Fernand Léger"]
        ],
        'Histoire du jazz' => [
            ['question' => "Quel musicien est considéré comme le père du jazz ?", 'reponse' => "Buddy Bolden"],
            ['question' => "Quel est le nom du premier style de jazz enregistré ?", 'reponse' => "Le jazz New Orleans"],
            ['question' => "Quel musicien de jazz est célèbre pour sa trompette et sa voix ?", 'reponse' => "Louis Armstrong"],
            ['question' => "Quel est le nom du style de jazz qui a émergé dans les années 1940 ?", 'reponse' => "Le bebop"],
            ['question' => "Quel musicien de jazz est connu pour son saxophone et sa composition 'Take Five' ?", 'reponse' => "Dave Brubeck"]
        ],
        'Techniques de composition' => [
            ['question' => "Qu'est-ce qu'une progression d'accords en musique ?", 'reponse' => "Une série d'accords qui forment la base d'une chanson"],
            ['question' => "Qu'est-ce qu'une mélodie en musique ?", 'reponse' => "Une séquence de notes qui crée un motif musical"],
            ['question' => "Qu'est-ce qu'un contrepoint en musique ?", 'reponse' => "Une technique de composition qui superpose des lignes mélodiques indépendantes"],
            ['question' => "Qu'est-ce qu'une cadence en musique ?", 'reponse' => "Une séquence d'accords qui marque la fin d'une phrase musicale"],
            ['question' => "Qu'est-ce qu'une modulation en musique ?", 'reponse' => "Un changement de tonalité au cours d'une chanson"]
        ],
        'Le cinéma d\'horreur' => [
            ['question' => "Quel est le nom du réalisateur de 'Psychose' et 'Les Oiseaux' ?", 'reponse' => "Alfred Hitchcock"],
            ['question' => "Quel est le nom du réalisateur de 'L'Exorciste' et 'French Connection' ?", 'reponse' => "William Friedkin"],
            ['question' => "Quel est le nom du réalisateur de 'Halloween' et 'Scream' ?", 'reponse' => "John Carpenter"],
            ['question' => "Quel est le nom du réalisateur de 'Shining' et 'Orange mécanique' ?", 'reponse' => "Stanley Kubrick"],
            ['question' => "Quel est le nom du réalisateur de 'Le Silence des agneaux' et 'Philadelphia' ?", 'reponse' => "Jonathan Demme"]
        ],
        'L\'ère du cinéma muet' => [
            ['question' => "Quel est le nom du premier film de l'histoire du cinéma ?", 'reponse' => "La Sortie de l'usine Lumière à Lyon"],
            ['question' => "Quel est le nom du réalisateur de 'Le Voyage dans la Lune' ?", 'reponse' => "Georges Méliès"],
            ['question' => "Quel est le nom du premier film de science-fiction ?", 'reponse' => "Le Voyage dans la Lune"],
            ['question' => "Quel est le nom du premier film de Charlie Chaplin ?", 'reponse' => "Charlot est content de lui"],
            ['question' => "Quel est le nom du premier film de Buster Keaton ?", 'reponse' => "Sherlock Jr."]
        ],
        'Intelligence Artificielle' => [
            ['question' => "Qu'est-ce que l'intelligence artificielle ?", 'reponse' => "La simulation de processus cognitifs humains par des machines"],
            ['question' => "Quelle est la différence entre l'intelligence artificielle forte et faible ?", 'reponse' => "L'intelligence artificielle forte peut penser et apprendre comme un humain, l'intelligence artificielle faible est spécialisée dans une tâche"],
            ['question' => "Quelle est la différence entre l'apprentissage supervisé et non supervisé en intelligence artificielle ?", 'reponse' => "L'apprentissage supervisé utilise des données étiquetées, l'apprentissage non supervisé utilise des données non étiquetées"],
            ['question' => "Quelle est la différence entre l'intelligence artificielle et le machine learning ?", 'reponse' => "L'intelligence artificielle est un domaine de recherche, le machine learning est une technique pour réaliser des tâches d'intelligence artificielle"],
            ['question' => "Quelle est la différence entre l'intelligence artificielle et l'apprentissage profond ?", 'reponse' => "L'apprentissage profond est une technique d'intelligence artificielle basée sur des réseaux de neurones artificiels"]
        ],
        'Blockchain expliqué' => [
            ['question' => "Qu'est-ce que la blockchain ?", 'reponse' => "Un registre numérique décentralisé et sécurisé"],
            ['question' => "Quelle est la différence entre la blockchain publique et privée ?", 'reponse' => "La blockchain publique est ouverte à tous, la blockchain privée est contrôlée par une organisation"],
            ['question' => "Qu'est-ce qu'un bloc dans une blockchain ?", 'reponse' => "Un ensemble de transactions validées"],
            ['question' => "Qu'est-ce qu'un mineur de blockchain ?", 'reponse' => "Un utilisateur qui valide les transactions et les ajoute à la blockchain"],
            ['question' => "Qu'est-ce qu'un contrat intelligent dans une blockchain ?", 'reponse' => "Un programme informatique auto-exécutable qui applique les termes d'un accord"]
        ],
        'Nutrition et bien-être' => [
            ['question' => "Quels sont les macronutriments essentiels pour une alimentation équilibrée ?", 'reponse' => "Les protéines, les lipides, les glucides"],
            ['question' => "Quels sont les micronutriments essentiels pour une alimentation équilibrée ?", 'reponse' => "Les vitamines, les minéraux, les oligo-éléments"],
            ['question' => "Quelle est la différence entre les graisses saturées et insaturées ?", 'reponse' => "Les graisses saturées sont solides à température ambiante, les graisses insaturées sont liquides"],
            ['question' => "Quelle est la différence entre les sucres simples et complexes ?", 'reponse' => "Les sucres simples sont rapidement absorbés, les sucres complexes sont digérés lentement"],
            ['question' => "Quelle est la valeur quotidienne recommandée de fibres alimentaires pour un adulte ?", 'reponse' => "25 à 30 grammes"]
        ],
        'Premiers secours' => [
            ['question' => "Quelle est la première étape à suivre en cas d'urgence ?", 'reponse' => "Appeler les secours"],
            ['question' => "Quelle est la position à adopter pour une personne inconsciente mais qui respire ?", 'reponse' => "La position latérale de sécurité"],
            ['question' => "Quelle est la conduite à tenir en cas de brûlure ?", 'reponse' => "Refroidir la brûlure à l'eau froide"],
            ['question' => "Quelle est la conduite à tenir en cas d'étouffement ?", 'reponse' => "Pratiquer la méthode de Heimlich"],
            ['question' => "Quelle est la conduite à tenir en cas de saignement abondant ?", 'reponse' => "Exercer une pression sur la plaie pour arrêter le saignement"]
        ],
        'Football moderne' => [
            ['question' => "Quelle est la durée d'un match de football ?", 'reponse' => "90 minutes"],
            ['question' => "Quelle est la taille réglementaire d'un terrain de football ?", 'reponse' => "100 à 110 mètres de long, 64 à 75 mètres de large"],
            ['question' => "Quelle est la hauteur réglementaire d'un but de football ?", 'reponse' => "2,44 mètres"],
            ['question' => "Quelle est la durée d'un match de football en prolongation ?", 'reponse' => "2 fois 15 minutes"],
            ['question' => "Quelle est la taille réglementaire d'un ballon de football ?", 'reponse' => "68 à 70 centimètres de circonférence"]
        ],
        'Psychologie du sport' => [
            ['question' => "Qu'est-ce que la motivation extrinsèque en psychologie du sport ?", 'reponse' => "La motivation basée sur des récompenses externes"],
            ['question' => "Qu'est-ce que la motivation intrinsèque en psychologie du sport ?", 'reponse' => "La motivation basée sur des récompenses internes"],
            ['question' => "Qu'est-ce que la théorie de l'autodétermination en psychologie du sport ?", 'reponse' => "La théorie qui décrit les différents types de motivation"],
            ['question' => "Qu'est-ce que la théorie de l'autoefficacité en psychologie du sport ?", 'reponse' => "La théorie qui décrit la croyance en ses propres capacités"],
            ['question' => "Qu'est-ce que la théorie de l'activation en psychologie du sport ?", 'reponse' => "La théorie qui décrit l'état d'activation optimal pour la performance"]
        ],
        'Conservation de la biodiversité' => [
            ['question' => "Qu'est-ce que la biodiversité ?", 'reponse' => "La variété des formes de vie sur Terre"],
            ['question' => "Qu'est-ce qu'une espèce en voie de disparition ?", 'reponse' => "Une espèce dont le nombre diminue rapidement"],
            ['question' => "Qu'est-ce qu'une réserve naturelle ?", 'reponse' => "Un espace protégé pour la faune et la flore"],
            ['question' => "Qu'est-ce qu'un corridor biologique ?", 'reponse' => "Un passage naturel entre deux habitats"],
            ['question' => "Qu'est-ce qu'une espèce invasive ?", 'reponse' => "Une espèce qui se propage rapidement et nuit à l'écosystème"]
        ],
        'Changement climatique' => [
            ['question' => "Qu'est-ce que l'effet de serre ?", 'reponse' => "Le phénomène qui retient la chaleur dans l'atmosphère"],
            ['question' => "Qu'est-ce que le réchauffement climatique ?", 'reponse' => "L'augmentation de la température moyenne de la Terre"],
            ['question' => "Qu'est-ce que l'empreinte carbone ?", 'reponse' => "La quantité de gaz à effet de serre émise par une activité"],
            ['question' => "Qu'est-ce que l'Accord de Paris sur le climat ?", 'reponse' => "Un accord international pour limiter le réchauffement climatique"],
            ['question' => "Qu'est-ce que la neutralité carbone ?", 'reponse' => "L'équilibre entre les émissions de CO2 et leur absorption"]
        ],
        'Cuisines du monde' => [
            ['question' => "Quel est le plat traditionnel de la cuisine italienne à base de pâtes et de tomates ?", 'reponse' => "Les spaghettis à la bolognaise"],
            ['question' => "Quel est le plat traditionnel de la cuisine mexicaine à base de tortillas et de viande ?", 'reponse' => "Les tacos al pastor"],
            ['question' => "Quel est le plat traditionnel de la cuisine indienne à base de riz et d'épices ?", 'reponse' => "Le curry de poulet"],
            ['question' => "Quel est le plat traditionnel de la cuisine chinoise à base de nouilles et de légumes ?", 'reponse' => "Les nouilles sautées"],
            ['question' => "Quel est le plat traditionnel de la cuisine japonaise à base de poisson cru et de riz ?", 'reponse' => "Les sushis"]
        ],
        'Techniques de pâtisserie' => [
            ['question' => "Quelle est la différence entre la farine de blé et la farine de maïs ?", 'reponse' => "La farine de blé contient du gluten, la farine de maïs est sans gluten"],
            ['question' => "Quelle est la différence entre le sucre glace et le sucre cristallisé ?", 'reponse' => "Le sucre glace est fin et fondant, le sucre cristallisé est granuleux"],
            ['question' => "Quelle est la différence entre la levure chimique et la levure de boulanger ?", 'reponse' => "La levure chimique est pour les pâtisseries, la levure de boulanger est pour le pain"],
            ['question' => "Quelle est la différence entre le beurre doux et le beurre salé ?", 'reponse' => "Le beurre salé contient du sel, le beurre doux n'en contient pas"],
            ['question' => "Quelle est la différence entre la crème pâtissière et la crème chantilly ?", 'reponse' => "La crème pâtissière est à base de lait, la crème chantilly est à base de crème"]
        ],
        'Investissement pour débutants' => [
            ['question' => "Qu'est-ce qu'une action en bourse ?", 'reponse' => "Une part de propriété dans une entreprise"],
            ['question' => "Qu'est-ce qu'une obligation en bourse ?", 'reponse' => "Un prêt à une entreprise ou un gouvernement"],
            ['question' => "Qu'est-ce qu'un fonds commun de placement ?", 'reponse' => "Un portefeuille d'actions et d'obligations géré par des professionnels"],
            ['question' => "Qu'est-ce qu'un ETF en bourse ?", 'reponse' => "Un fonds négocié en bourse qui suit un indice"],
            ['question' => "Qu'est-ce qu'un CFD en bourse ?", 'reponse' => "Un contrat pour la différence basé sur la variation du prix d'un actif"]
        ],
        'Crypto-monnaies' => [
            ['question' => "Qu'est-ce que Bitcoin ?", 'reponse' => "La première crypto-monnaie décentralisée"],
            ['question' => "Qu'est-ce que l'Ether ?", 'reponse' => "La crypto-monnaie de la blockchain Ethereum"],
            ['question' => "Qu'est-ce que la blockchain ?", 'reponse' => "Un registre numérique décentralisé et sécurisé"],
            ['question' => "Qu'est-ce qu'un wallet de crypto-monnaie ?", 'reponse' => "Un portefeuille numérique pour stocker des crypto-monnaies"],
            ['question' => "Qu'est-ce qu'un mineur de crypto-monnaie ?", 'reponse' => "Un utilisateur qui valide les transactions et les ajoute à la blockchain"]
        ],
        'Politique comparée' => [
            ['question' => "Qu'est-ce que la démocratie ?", 'reponse' => "Un système politique où le pouvoir est exercé par le peuple"],
            ['question' => "Qu'est-ce que la monarchie ?", 'reponse' => "Un système politique où le pouvoir est exercé par un roi ou une reine"],
            ['question' => "Qu'est-ce que la république ?", 'reponse' => "Un système politique où le pouvoir est exercé par des représentants élus"],
            ['question' => "Qu'est-ce que le fédéralisme ?", 'reponse' => "Un système politique où le pouvoir est partagé entre l'État central et les États fédérés"],
            ['question' => "Qu'est-ce que le totalitarisme ?", 'reponse' => "Un système politique où le pouvoir est concentré entre les mains d'un seul parti"]
        ],
        'Élections et démocratie' => [
            ['question' => "Qu'est-ce qu'une élection présidentielle ?", 'reponse' => "Un vote pour élire le président d'un pays"],
            ['question' => "Qu'est-ce qu'une élection législative ?", 'reponse' => "Un vote pour élire les membres du parlement"],
            ['question' => "Qu'est-ce qu'un référendum ?", 'reponse' => "Un vote pour décider d'une question politique"],
            ['question' => "Qu'est-ce qu'un parti politique ?", 'reponse' => "Un groupe d'individus qui partagent des idées politiques communes"],
            ['question' => "Qu'est-ce qu'un gouvernement de coalition ?", 'reponse' => "Un gouvernement formé par plusieurs partis politiques"]
        ],
        'Voyager en Asie' => [
            ['question' => "Quelle est la capitale de la Chine ?", 'reponse' => "Pékin"],
            ['question' => "Quelle est la capitale du Japon ?", 'reponse' => "Tokyo"],
            ['question' => "Quelle est la capitale de la Corée du Sud ?", 'reponse' => "Séoul"],
            ['question' => "Quelle est la capitale de l'Inde ?", 'reponse' => "New Delhi"],
            ['question' => "Quelle est la capitale de la Thaïlande ?", 'reponse' => "Bangkok"]
        ],
        'Écotourisme' => [
            ['question' => "Qu'est-ce que l'écotourisme ?", 'reponse' => "Un type de tourisme qui préserve l'environnement et soutient les communautés locales"],
            ['question' => "Quels sont les principes de l'écotourisme ?", 'reponse' => "La conservation de la nature, le respect des cultures locales, le soutien économique aux communautés"],
            ['question' => "Quels sont les avantages de l'écotourisme ?", 'reponse' => "La protection de l'environnement, la préservation des cultures, le développement économique local"],
            ['question' => "Quels sont les exemples d'activités d'écotourisme ?", 'reponse' => "L'observation des oiseaux, la randonnée en montagne, la plongée sous-marine"],
            ['question' => "Quels sont les destinations d'écotourisme populaires ?", 'reponse' => "Les Galápagos, le Costa Rica, la Norvège"]
        ],
        'Systèmes éducatifs mondiaux' => [
            ['question' => "Quel pays a le meilleur système éducatif au monde ?", 'reponse' => "La Finlande"],
            ['question' => "Quel pays a le plus grand nombre d'universités de renommée mondiale ?", 'reponse' => "Les États-Unis"],
            ['question' => "Quel pays a le plus grand nombre d'étudiants étrangers ?", 'reponse' => "Les États-Unis"],
            ['question' => "Quel pays a le plus grand nombre de diplômés en sciences et en ingénierie ?", 'reponse' => "La Chine"],
            ['question' => "Quel pays a le plus grand nombre de prix Nobel en littérature ?", 'reponse' => "La France"]
        ],
        'E-learning et technologie éducative' => [
            ['question' => "Qu'est-ce que l'e-learning ?", 'reponse' => "L'apprentissage en ligne par le biais de plateformes numériques"],
            ['question' => "Quels sont les avantages de l'e-learning ?", 'reponse' => "La flexibilité, l'accessibilité, l'interactivité"],
            ['question' => "Quels sont les types de contenu utilisés dans l'e-learning ?", 'reponse' => "Les vidéos, les quiz, les forums de discussion"],
            ['question' => "Quels sont les outils utilisés dans l'e-learning ?", 'reponse' => "Les plateformes LMS, les webinaires, les applications mobiles"],
            ['question' => "Quels sont les exemples de plateformes d'e-learning ?", 'reponse' => "Coursera, Udemy, edX"]
        ],
        'Histoire de la mode' => [
            ['question' => "Quel couturier français est connu pour ses robes de soirée et ses parfums ?", 'reponse' => "Christian Dior"],
            ['question' => "Quelle créatrice de mode italienne est célèbre pour ses robes de mariée ?", 'reponse' => "Vera Wang"],
            ['question' => "Quel couturier américain est connu pour ses jeans et ses parfums ?", 'reponse' => "Calvin Klein"],
            ['question' => "Quelle créatrice de mode britannique est célèbre pour ses sacs à main et ses chaussures ?", 'reponse' => "Stella McCartney"],
            ['question' => "Quel couturier japonais est connu pour ses vêtements unisexes et ses collections avant-gardistes ?", 'reponse' => "Yohji Yamamoto"]
        ],
        'Tendances mode 2024' => [
            ['question' => "Quelle couleur sera à la mode en 2024 ?", 'reponse' => "Le vert émeraude"],
            ['question' => "Quel motif sera tendance en 2024 ?", 'reponse' => "Les carreaux"],
            ['question' => "Quel tissu sera à la mode en 2024 ?", 'reponse' => "Le velours côtelé"],
            ['question' => "Quel accessoire sera tendance en 2024 ?", 'reponse' => "Le sac banane"],
            ['question' => "Quel style de chaussures sera à la mode en 2024 ?", 'reponse' => "Les bottines à talons"]
        ],

    ];


    public function run()
    {
        $themeQuestions = $this->themeQuestions;

        foreach (Theme::all() as $theme) {
            if (isset($themeQuestions[$theme->nom])) {
                foreach ($themeQuestions[$theme->nom] as $qa) {
                    Carte::factory()->create([
                        'theme_id' => $theme->id,
                        'question' => $qa['question'],
                        'reponse' => $qa['reponse']
                    ]);
                }
            }
        }
    }
}
