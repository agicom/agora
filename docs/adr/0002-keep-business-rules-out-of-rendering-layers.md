# Keep business rules out of rendering layers

The project uses Filament resources, Livewire components, controllers, and possibly commands as entry points, but the tournament registration rules must remain reusable and testable outside those rendering or delivery layers. We decided to place sensitive business logic in dedicated domain services so UI code orchestrates requests while services enforce rules such as capacity, team size, duplicate registration, and member conflicts.

**Consequences**

Livewire and Filament should not become the source of truth for registration rules. Business services should be expressed as small use-case actions under `app/Actions/`, grouped by domain when useful, such as registering a team to a tournament or closing tournament registrations. Tests should target those actions directly for business behavior, with UI/component tests covering integration and user interaction.
