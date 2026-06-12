# Use users for admins and public participants

The domain originally considered separate members for team composition, while Laravel and Filament already provide a natural `User` model for authentication. We decided to use `User` for both administrators and public participants, distinguished by role, because it simplifies the data model and keeps team membership tied to a single identity concept.

**Consequences**

The application should not introduce a separate member model. Teams relate to public users, administrators are users with an admin role, and Filament access must be restricted by role. A simple role field on `users` is enough for the challenge; a permissions package would add unnecessary scope unless future requirements demand it.

Laravel authentication can exist for all users from the beginning, but the challenge's public registration page must remain accessible without requiring a logged-in public user.

Public registration may create public users with minimal identity data and a random password when needed. Account activation and richer public onboarding are outside the MVP.
