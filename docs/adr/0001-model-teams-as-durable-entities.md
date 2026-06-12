# Model teams as durable entities

The README frames the exercise around tournament registrations, which could be modeled as one-off team registrations owned by a tournament. We decided to model teams as durable entities that can exist independently from tournaments, because the platform manages tournament participation over time: a team may exist before registering, skip tournaments, and register again later.

**Consequences**

Teams are not stored as embedded registration data. The domain needs separate teams and public users, plus a tournament registration relationship between teams and tournaments.
