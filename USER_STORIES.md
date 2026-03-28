# User Stories
U1. "As a user, I want to define my values so that I can align my goals with what matters most to me."
U2. "As a user, I want to create a weekly plan so that I can sharpen the saw in all four areas."
U3. "As a user, I want to be able to be able to go through a guided process to create a weekly plan that aligns with my values."
U4. "As a user, I want to be able to export my plan to my calendar so that I can easily track my progress."
U5. "As a user, I want to be able to establish my values, identify roles, and create todos that align with my values"
U6. "As a user, I want to be able to create a weekly plan that aligns with my values and goals."

U1.1 "As a user, I want to enter 5–10 value names first, then add personal descriptions for each, and finally narrow them to 3–5 core values so I can clearly prioritize what to live by."

Acceptance Criteria:
- Phase 1: User can add value names only (no description) and see a list; the UI shows a recommended count (5–10).
- Phase 2: User is prompted to add a short personal description for each entered name.
- Phase 3: User can mark/select 3–5 values as their core values; the UI enforces min/max and shows validation messages.
- Data is persisted (SQLite when available, otherwise `db/fallback.json`).
- The UI shows progress for Phase 1 → Phase 2 → Phase 3 and allows navigating back to edit.
