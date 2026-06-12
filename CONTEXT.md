# Gestion de tournois LAN

Ce contexte couvre une mini-plateforme de gestion de tournois LAN. Il sert a stabiliser le vocabulaire metier autour des tournois publics et de leurs inscriptions.

## Language

**Tournoi**:
Un evenement organise auquel des participants peuvent s'inscrire dans la limite d'une capacite definie. Un tournoi peut etre decrit publiquement, planifie avec une date de debut, et etre en brouillon, ouvert aux inscriptions, ou clos aux inscriptions. Un tournoi possede un slug unique qui identifie sa page publique.
_Avoid_: evenement, LAN, competition

**Brouillon**:
Statut d'un tournoi prepare en back-office mais non accessible publiquement pour les inscriptions.
_Avoid_: draft, inactif

**Ouvert**:
Statut d'un tournoi accessible publiquement et acceptant les inscriptions tant que sa capacite n'est pas atteinte.
_Avoid_: publie, actif

**Clos**:
Statut d'un tournoi qui n'accepte plus d'inscriptions par decision d'administration. Un tournoi clos peut rester visible publiquement.
_Avoid_: ferme, archive

**Cloture des inscriptions**:
Decision d'administration qui fait passer un tournoi a l'etat clos.
_Avoid_: archivage, suppression, desactivation

**Complet**:
Etat calcule d'un tournoi dont le nombre d'inscriptions a atteint la capacite. Un tournoi complet peut rester visible publiquement.
_Avoid_: statut complet

**Equipe**:
Un groupe durable d'utilisateurs publics qui peut exister independamment de tout tournoi. Une equipe peut s'inscrire a des tournois et possede un capitaine comme contact principal.
_Avoid_: participant, joueur, utilisateur

**Capitaine**:
L'utilisateur public contact principal d'une equipe. Une equipe a exactement un capitaine, qui fait partie de l'equipe et compte dans sa taille.
_Avoid_: responsable, owner, chef d'equipe

**Utilisateur public**:
Un utilisateur non administrateur pouvant appartenir a plusieurs equipes, identifie par son email et affiche par son pseudo. Un meme utilisateur public ne peut pas appartenir a deux equipes inscrites au meme tournoi, mais peut appartenir a des equipes sur des tournois differents.
_Avoid_: membre d'equipe, joueur, participant

**Administrateur**:
Un utilisateur authentifie du back-office Filament. Un administrateur est distingue d'un utilisateur public par son role.
_Avoid_: membre, participant, joueur

**Inscription**:
Une entite dediee qui rattache une equipe existante ou nouvellement creee a un tournoi ouvert aux inscriptions. Au MVP, une inscription existe ou n'existe pas, sans statut propre. Une inscription est refusee si le tournoi n'est pas ouvert, si le tournoi est complet, si l'equipe est deja inscrite au tournoi, si un utilisateur public appartient deja a une autre equipe inscrite au meme tournoi, ou si la taille de l'equipe ne respecte pas les limites du tournoi.
_Avoid_: reservation, candidature, participation

**Inscription publique**:
Parcours accessible sans authentification dans lequel une equipe est creee en meme temps que son inscription a un tournoi. Ce parcours ne rattache pas automatiquement la demande a une equipe existante. Il reutilise les utilisateurs publics existants par email, cree les utilisateurs publics manquants, et refuse l'utilisation d'un email administrateur. Lorsqu'un utilisateur public est connecte, il devient le capitaine pre-rempli de l'equipe.
_Avoid_: choix d'equipe existante, connexion obligatoire

**Capacite**:
Le nombre maximal d'equipes inscrites qu'un tournoi peut accepter.
_Avoid_: quota, limite, places totales

**Taille maximale d'equipe**:
Le nombre maximal d'utilisateurs publics qu'une equipe peut declarer lors de son inscription a un tournoi. Cette limite est definie par tournoi.
_Avoid_: capacite d'equipe, taille limite

**Taille minimale d'equipe**:
Le nombre minimal d'utilisateurs publics qu'une equipe doit declarer lors de son inscription a un tournoi. Cette limite est definie par tournoi et vaut 1 par defaut.
_Avoid_: minimum de joueurs, equipe incomplete
