Status: ready-for-agent

## Problem Statement

Agora doit devenir une mini-plateforme de gestion de tournois LAN pour le challenge technique Agicom. Le repo contient l'enonce original du challenge, mais il manque encore un cadrage produit et technique precis sur le domaine, les responsabilites applicatives, les regles d'inscription, les choix d'architecture et les tests attendus.

Le besoin est de livrer une application Laravel/TALL/Filament qui montre un usage idiomatique de Filament pour le back-office, un composant Livewire public ecrit a la main, et des tests Pest ciblant les regles metier sensibles. Le projet doit aussi rester explicable en entretien: les choix doivent etre coherents, documentes, et faciles a relier au README original conserve dans `CHALLENGE.md`.

## Solution

Construire une plateforme Laravel avec PostgreSQL, Filament, Livewire, Alpine.js, Tailwind CSS, Livewire Flux et Pest. Les administrateurs gerent les Tournois, les Equipes, les Utilisateurs publics et les Inscriptions depuis un back-office Filament. Le public peut acceder a une page d'Inscription publique via le slug d'un Tournoi, sans authentification obligatoire, pour creer une Equipe et l'inscrire au Tournoi si les regles metier le permettent.

Le domaine doit distinguer clairement les Tournois, Equipes, Utilisateurs publics, Administrateurs et Inscriptions. Les Equipes sont durables et peuvent exister independamment des Tournois. Les Utilisateurs publics et les Administrateurs partagent le modele `User`, distingues par role. Toute logique metier sensible doit etre portee par des Actions metier testables, et non par les composants Livewire, les Resources Filament, les controleurs ou les commandes.

## User Stories

1. As an administrateur, I want to sign in to a Filament back-office, so that I can manage the tournament platform securely.
2. As an administrateur, I want public users to be separated from administrators by role, so that back-office access remains restricted.
3. As an administrateur, I want to create a Tournoi, so that teams can later register for it.
4. As an administrateur, I want to edit a Tournoi, so that I can correct its public information and registration rules.
5. As an administrateur, I want to define a Tournoi slug, so that its public registration page has a stable URL.
6. As an administrateur, I want tournament slugs to be unique, so that each public page resolves to exactly one Tournoi.
7. As an administrateur, I want to set a Tournoi status to brouillon, ouvert, or clos, so that I can control whether registrations are accepted.
8. As an administrateur, I want a brouillon Tournoi to be unavailable for public registrations, so that unfinished tournaments are not exposed.
9. As an administrateur, I want an ouvert Tournoi to accept registrations while capacity remains available, so that public teams can join.
10. As an administrateur, I want a clos Tournoi to reject new registrations, so that I can stop registrations intentionally.
11. As an administrateur, I want to define a Tournoi capacity in number of Equipes, so that I can limit how many teams can register.
12. As an administrateur, I want to define a minimum team size per Tournoi, so that each Equipe has enough Utilisateurs publics.
13. As an administrateur, I want to define a maximum team size per Tournoi, so that each Equipe respects the tournament format.
14. As an administrateur, I want team size limits to be configured per Tournoi, so that solo, duo, and team formats are all possible.
15. As an administrateur, I want to search Tournois in Filament, so that I can find a tournament quickly.
16. As an administrateur, I want to filter Tournois by status, so that I can review brouillon, ouvert, and clos tournaments separately.
17. As an administrateur, I want to sort Tournois, so that I can inspect them by date, status, or name.
18. As an administrateur, I want status badges in the TournamentResource table, so that each tournament state is readable at a glance.
19. As an administrateur, I want validation in the TournamentResource form, so that invalid registration rules are rejected early.
20. As an administrateur, I want to view Inscriptions from a Tournoi page, so that I can see which Equipes are registered.
21. As an administrateur, I want a Relation Manager for Inscriptions, so that I can inspect registrations without leaving the tournament context.
22. As an administrateur, I want a stats widget for registered teams, so that I can monitor participation quickly.
23. As an administrateur, I want the stats widget to refresh by polling when practical, so that the back-office feels current.
24. As an administrateur, I want to close registrations from a Tournoi, so that I can stop new Inscriptions with a clear domain action.
25. As an administrateur, I want to view Equipes as durable records, so that teams are not reduced to one-off registration data.
26. As an administrateur, I want to view an Equipe's captain, so that I know the primary contact.
27. As an administrateur, I want to view the Utilisateurs publics attached to an Equipe, so that team composition is inspectable.
28. As an administrateur, I want to view Utilisateurs publics separately from administrators, so that public identities can be managed without confusing roles.
29. As an administrateur, I want seeded demo data, so that I can demonstrate the application quickly.
30. As an administrateur, I want a seeded admin account, so that setup and demo are straightforward.
31. As an administrateur, I want seeded Tournois in different states, so that brouillon, ouvert, clos, and complet cases can be demonstrated.
32. As an administrateur, I want seeded Equipes, Utilisateurs publics, and Inscriptions, so that the back-office has realistic data.
33. As a visiteur public, I want to open a Tournoi page by slug, so that I can register without navigating the back-office.
34. As a visiteur public, I want to see public Tournoi information, so that I know what I am registering for.
35. As a visiteur public, I want to see whether a Tournoi is complet, so that I understand why registration is unavailable.
36. As a visiteur public, I want to see whether registrations are closes, so that I understand why registration is unavailable.
37. As a visiteur public, I want the page to remain accessible when a Tournoi is complet, so that the state is explicit instead of hidden.
38. As a visiteur public, I want the page to remain accessible when a Tournoi is clos, so that the state is explicit instead of hidden.
39. As a visiteur public, I want to create an Equipe during public registration, so that I can register without an existing team workflow.
40. As a visiteur public, I want to provide the captain information, so that the Equipe has a primary contact.
41. As a visiteur public, I want the captain to count as part of the Equipe, so that team size rules are intuitive.
42. As a visiteur public, I want to add other Utilisateurs publics by email and pseudo, so that the Equipe composition is complete.
43. As a visiteur public, I want real-time validation in the Livewire form, so that I can correct mistakes before submission.
44. As a visiteur public, I want the form to reject an Equipe that is too small, so that the Tournoi format is respected.
45. As a visiteur public, I want the form to reject an Equipe that is too large, so that the Tournoi format is respected.
46. As a visiteur public, I want the form to reject duplicate emails inside the same Equipe, so that the team composition is valid.
47. As a visiteur public, I want the form to reject use of an administrator email, so that admin identities are not enrolled publicly.
48. As a visiteur public, I want an existing public user email to be reused, so that identities are not duplicated.
49. As a visiteur public, I want a missing public user to be created automatically, so that registration remains simple.
50. As a visiteur public, I want public user creation to avoid account onboarding complexity, so that registration stays lightweight.
51. As a visiteur public, I want an Equipe name to be required, so that the registration is identifiable.
52. As a visiteur public, I want an Equipe already registered to a Tournoi to be rejected, so that duplicate team registration is impossible.
53. As a visiteur public, I want a user already present in another Equipe for the same Tournoi to be rejected, so that a person cannot play for two teams in the same tournament.
54. As a visiteur public, I want a user to be allowed in teams for different Tournois, so that recurring participation remains possible.
55. As a visiteur public, I want a confirmation toast after registration, so that I know the registration succeeded.
56. As a visiteur public, I want a copy-link micro-interaction when useful, so that sharing the Tournoi page is easy.
57. As a connected utilisateur public, I want the captain fields to be prefilled from my account, so that registration is faster.
58. As a connected utilisateur public, I want myself to become the Equipe captain during registration, so that the Equipe has a clear contact.
59. As a developer, I want business rules outside rendering layers, so that they are reusable from Livewire, Filament, controllers, commands, and tests.
60. As a developer, I want small Actions by use case, so that behavior is easy to test and explain.
61. As a developer, I want Pest tests for registration rules, so that critical domain behavior is protected.
62. As a developer, I want Livewire component tests for the public form, so that user-facing integration is verified.
63. As a reviewer, I want a completed README, so that I can install, run, test, and understand the project quickly.
64. As a reviewer, I want the original challenge preserved, so that I can compare the implementation with the initial brief.

## Implementation Decisions

- Preserve the original challenge statement in `CHALLENGE.md`.
- Complete the delivery README with prerequisites, installation, startup commands, tests, notable choices, seeded account information, and a link to `CHALLENGE.md`.
- Use Laravel with the required TALL stack, Filament v5, Livewire Flux, Pest, and PostgreSQL.
- Use PostgreSQL as the database.
- Use a single `User` identity model for administrators and public users.
- Distinguish administrators and public users with a simple role field.
- Restrict Filament access to administrators.
- Keep the public Tournoi registration page accessible without requiring authentication, even though Laravel authentication exists in the project.
- When a public user is connected, prefill and lock that user as the Equipe captain during registration.
- When a visitor is not connected, collect captain identity in the registration form.
- Represent Equipes as durable entities that can exist independently of Tournois.
- Represent Inscription as a dedicated entity between Equipe and Tournoi, not as embedded form data.
- Keep Inscription stateless for the MVP: it exists or does not exist.
- Do not automatically attach public registration requests to an existing Equipe.
- Create a new Equipe during public registration.
- Reuse existing public users by email during public registration.
- Create missing public users during public registration with minimal identity data and a random password when necessary.
- Refuse use of administrator emails in public registration.
- Model an Equipe with a name, a captain, and public users.
- Require exactly one captain per Equipe.
- Count the captain in the Equipe size.
- Allow a public user to belong to multiple Equipes.
- Refuse registration when a public user already belongs to another Equipe registered to the same Tournoi.
- Allow a public user to belong to Equipes registered to different Tournois.
- Model a Tournoi with name, slug, description, status, capacity, team minimum size, team maximum size, and optional start date.
- Use Tournoi statuses brouillon, ouvert, and clos.
- Treat complet as a calculated state, not a persisted Tournoi status.
- Count capacity as the maximum number of Equipes registered to a Tournoi.
- Define team minimum and maximum sizes per Tournoi.
- Public Tournoi pages remain visible for complet and clos tournaments, but the registration form is replaced by an explicit unavailable state.
- Build a complete Filament TournamentResource with search, filters, sorting, status badges, and validated create/edit forms.
- Add a Relation Manager on TournamentResource for Inscriptions.
- Add a stats widget for registered teams, ideally refreshed with polling.
- Build supporting Filament resources for Equipes and Users with less depth than TournamentResource.
- Prioritize seeders as the first bonus, then closing registrations, then CSV export, then policies if time allows.
- Place sensitive business logic in Actions under an application actions area, grouped by domain when useful.
- Use Actions such as registering a team to a tournament, creating a team, and closing tournament registrations.
- Keep Livewire components, Filament resources, controllers, and commands as orchestration layers.
- Expected core tables are users, tournaments, teams, team_user, and registrations.
- Enforce unique tournament slugs.
- Enforce uniqueness of registration per tournament/team.
- Enforce member conflict, capacity, and team size rules in business Actions, backed by database constraints where practical.
- Use factories for users, tournaments, teams, and registrations.
- Use demo seeders for an admin, public users, teams, tournaments, and registrations.

## Testing Decisions

- Tests should focus on observable behavior and domain rules, not implementation details of Filament or Livewire internals.
- Pest is the required test runner.
- Unit or feature-level tests should target Actions directly for business behavior.
- Livewire tests should cover the public registration form as the key user-facing integration.
- Factories should be used throughout tests and seeders.
- Test that a complet Tournoi refuses a new Inscription.
- Test that remaining capacity is calculated correctly.
- Test that a non-ouvert Tournoi refuses a new Inscription.
- Test that an Equipe cannot register twice to the same Tournoi.
- Test that a public user cannot belong to two Equipes registered to the same Tournoi.
- Test that a public user can belong to Equipes registered to different Tournois.
- Test that team minimum size is enforced.
- Test that team maximum size is enforced.
- Test that an administrator email is refused by public registration.
- Test that an existing public user email is reused by public registration.
- Test that a missing public user is created by public registration.
- Test that public registration creates an Equipe, attaches its users, assigns the captain, and creates the Inscription.
- Test that the public Livewire component validates required fields.
- Test that the public Livewire component blocks registration when the Tournoi is complet.
- Test that the public Livewire component blocks registration when the Tournoi is clos.
- Test Filament access at least enough to prove public users cannot access the back-office and administrators can.
- The suite should pass with `php artisan test` or `./vendor/bin/pest`.

## Out of Scope

- Full public account onboarding.
- Mandatory login for the public registration page.
- Selecting and managing an existing Equipe from the public registration page.
- Rich public dashboards for users or teams.
- Registration statuses such as pending, confirmed, cancelled, or waitlisted.
- Waiting lists.
- Payments.
- Match scheduling, brackets, scores, or tournament progression.
- Complex permission matrix or a dedicated permissions package.
- Exhaustive Filament customization beyond what demonstrates idiomatic Resources, Relation Managers, actions, and widgets.
- CSV export unless the core scope and higher-priority bonuses are complete.
- Policies unless the core scope and higher-priority bonuses are complete.
- Multi-context domain documentation.

## Further Notes

- The original challenge brief is preserved in `CHALLENGE.md`.
- Domain vocabulary is captured in `CONTEXT.md`.
- Architectural decisions are captured in ADRs under `docs/adr/`.
- The most important explanation for the final interview is the boundary between rendering layers and business Actions.
- The project should optimize for clear reasoning, focused tests, and a polished but scoped implementation rather than broad feature count.
