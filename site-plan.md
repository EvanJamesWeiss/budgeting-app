# Step-by-Step Implementation Plan: Shared Budgeting App

This document breaks down the development of the shared budgeting application into small, isolated, iterative steps optimized for an AI coding tool or incremental development.

## Tech Stack Overview
- **Backend Framework:** PHP 8.2+ / Symfony 7.x
- **Database:** MySQL 8.0+
- **Frontend Layer:** Multi-Page Application (MPA) using Symfony Twig templates & Material UI (MUI v5 free tier distributed via CDN)
- **Architecture:** Strict Controller -> Service -> Repository paradigm
- **Scope Constraints:**
    - Strictly 2 users (no authentication required, hardcoded or seeded rows)
    - Environment: Local execution only (`localhost`)

---

## Phase 1: Database Setup & Configuration

### Step 1.1: Initialize Symfony Framework
- Initialize a bare-bones Symfony project with standard web capabilities.
- Create or update the `.env.local` file to point to your local MySQL server instance.
- **Verification:** Run `php bin/console about` and confirm database connection readiness.

### Step 1.2: Generate Database Entities & Mappings
Create the four core Doctrine entities with clean annotations/attributes, strict type-hinting, getters, and setters:

1. **User**
    - id: int, primary key
    - name: string

2. **ExpenseTemplate** (For recurring rules)
    - id: int, primary key
    - title: string
    - defaultAmount: decimal(10,2), nullable
    - isStatic: boolean
    - paidBy: ManyToOne relationship to User
    - defaultSplitRatio: decimal(3,2) (e.g., 0.50 representing 50%)

3. **Expense** (Actual entries)
    - id: int, primary key
    - template: ManyToOne relationship to ExpenseTemplate, nullable
    - title: string
    - amount: decimal(10,2)
    - date: date_immutable
    - isPlaceholder: boolean
    - paidBy: ManyToOne relationship to User
    - splitRatio: decimal(3,2) (percentage of expense owed by the other person)

4. **Settlement** (The 'Paid back' logs)
    - id: int, primary key
    - monthYear: string (Format: YYYY-MM)
    - paidBy: ManyToOne relationship to User
    - receivedBy: ManyToOne relationship to User
    - amount: decimal(10,2)
    - timestamp: datetime_immutable

- **Verification:** Run `php bin/console doctrine:schema:update --dump-sql` then `--force` to create the MySQL schema.

### Step 1.3: Create Database Fixtures (Seed Data)
- Write a Doctrine fixture or SQL migration script to pre-populate exactly two users so the system is immediately usable.
    - User 1 ID: 1, Name: "User A"
    - User 2 ID: 2, Name: "User B"
- **Verification:** Query the user table directly to verify exactly two IDs exist.

---

## Phase 2: Core Calculation Engine (Services)

### Step 2.1: Write the Monthly Calculations inside LedgerService
Create a service class at `src/Service/LedgerService.php`.
- Implement a public method `calculateMonthSummary(string $monthYear): MonthSummaryDto`.
- **Internal Logic Flow:**
    1. Fetch all Expense records where date matches $monthYear AND isPlaceholder = false.
    2. Fetch all Settlement records where monthYear equals $monthYear.
    3. Loop through expenses:
        - Keep track of total pool spent.
        - Keep track of out-of-pocket spending for User 1 and User 2.
        - Calculate debt share using splitRatio. For instance, if User 1 paid $100 and the splitRatio is 0.50, User 2 owes User 1 $50. If splitRatio is 0.40, User 2 owes User 1 $40.
    4. Aggregate total settlements for the month (e.g., if User 2 paid User 1 $30 back, reduce User 2's net debt by $30).
    5. Return a Data Transfer Object (DTO) containing: totalSpent, user1Spent, user2Spent, netOwedAmount, debtorId, creditorId, isSettled.

### Step 2.2: Implement Cumulative Calculations inside LedgerService
- Implement a public method `calculateAllTimeBalances(): array`.
- **Internal Logic Flow:**
    1. Query all historical months present across both Expense and Settlement tables.
    2. Loop through each month and run `calculateMonthSummary()`.
    3. Filter down to months where `isSettled = false`.
    4. Aggregate an all-time summary array showing the historical net balance totals.

### Step 2.3: Build the ExpenseGenerationService for Placeholders
Create a service class at `src/Service/ExpenseGenerationService.php`.
- Implement a public method `generateMonthlyPlaceholders(string $monthYear): void`.
- **Internal Logic Flow:**
    1. Query all templates in ExpenseTemplate.
    2. For each template, check if an Expense entry already exists for that template_id in the targeted $monthYear.
    3. If an entry does not exist:
        - If `template.isStatic == true`: Create a standard Expense entry with `amount = template.defaultAmount`, `isPlaceholder = false`.
        - If `template.isStatic == false`: Create an Expense entry with `amount = 0.00`, `isPlaceholder = true`.
    4. Flush all new entities to the database.

---

## Phase 3: Twig Layout & Material UI Integration

### Step 3.1: Build Base Template Layout
- Open `templates/base.html.twig`.
- Add the official Material UI Google Web Fonts (Roboto) and Material Icons CDN link tags in the `<head>`.
- Add standard compiled Material UI CSS stylesheets / component bindings or standard HTML/CSS templates mirroring Material UI styling guidelines via CDN.
- Navigation links to add: "Dashboard", "Monthly Ledger", "All-Time Balance".

---

## Phase 4: Controllers & Web Views

### Step 4.1: Dashboard View Controller (`/`)
- Create `src/Controller/DashboardController.php`.
- **Action Workflow:**
    1. Detect the current month string (e.g., `2026-06`).
    2. Call `ExpenseGenerationService::generateMonthlyPlaceholders()` for the current month.
    3. Call `LedgerService::calculateMonthSummary()` for the current month.
    4. Pass the summary calculation DTO, along with hardcoded user IDs, to the Twig template.

### Step 4.2: Handle Quick-Add Form Submission
- Build an endpoint route `/expense/add` (POST) inside an `ExpenseController`.
- **Action Workflow:**
    1. Parse form inputs: title, amount, paid_by_id, date, custom_split_ratio (optional).
    2. Create a brand new Expense entity. Set `isPlaceholder = false`.
    3. If custom split ratio isn't specified, default to 0.50.
    4. Save to the database using EntityManagerInterface.
    5. Redirect back to the Dashboard.

### Step 4.3: Monthly Ledger View Controller (`/ledger/{monthYear}`)
- Create `src/Controller/LedgerController.php`.
- **Action Workflow:**
    1. Accept an optional route parameter `{monthYear}`. Default to current month if omitted.
    2. Ensure placeholders exist for that month using ExpenseGenerationService.
    3. Pull all expenses (both placeholders and regular transactions) ordered by date.
- **Twig View Design:**
    - Render an HTML data table styled using Material UI table definitions.
    - Highlight rows where `isPlaceholder == true` with a subtle amber background color. Add a mini-form or link next to placeholders saying "Enter Bill Amount".

### Step 4.4: Inline Placeholder Update Handler
- Build an endpoint route `/expense/update/{id}` (POST).
- **Action Workflow:**
    1. Fetch the targeted Expense entity.
    2. Bind the new amount passed from the UI input.
    3. Flip `isPlaceholder` to `false`.
    4. Persist and flush changes.
    5. Redirect back to the Monthly Ledger view page.

### Step 4.5: Monthly Settlement Log Button ("Mark as Settled")
- Build an endpoint route `/settlement/log` (POST).
- **Action Workflow:**
    1. Expect inputs: monthYear, paid_by_id, amount.
    2. Calculate who the receiver should be automatically based on the opposite user ID.
    3. Instantiate a new Settlement entity, setting the timestamp to the current time.
    4. Persist and flush to the database.
    5. Redirect back to the view page where the submission originated.

### Step 4.6: Historical Cumulative View (`/all-time`)
- Create `src/Controller/AllTimeController.php`.
- **Action Workflow:**
    1. Call `LedgerService::calculateAllTimeBalances()`.
    2. Send the un-settled historical calculation matrix to the Twig view.
