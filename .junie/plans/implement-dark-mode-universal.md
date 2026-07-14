---
sessionId: session-260707-221822-1f8k
---

# Requirements

### Overview & Goals
The goal is to implement a universal dark mode for the budgeting application. The solution must be easily maintainable, automatically applying to all existing and future pages.

### Scope
- **In Scope**:
    - CSS Variable system for theming.
    - Theme toggle button in the header.
    - Persistence of theme preference using `localStorage`.
    - Refactoring of existing templates to use the theme variables.
- **Out of Scope**:
    - System-level theme detection (e.g., `prefers-color-scheme`), though it could be added as an enhancement.
    - Theming of external third-party components that don't support CSS variables (though Material Components Web generally does).

# Technical Design

### Current Implementation
The application currently uses a mix of Material Design Components (MDC) and custom inline/internal styles. Most colors are hard-coded in the Twig templates.

### Key Decisions
- **CSS Variables (Custom Properties)**: Use CSS variables on the `:root` element for theming. This is the standard modern approach for "universal" theming.
- **Data Attributes for Theming**: Use `data-theme="dark"` on the `<html>` or `<body>` tag to trigger theme overrides. This is cleaner than toggling a class and works better with CSS variables.
- **LocalStorage Persistence**: Store the user's preference in `localStorage` to ensure the theme persists across sessions and page reloads.
- **Inline Script for Theme Initialization**: Place a small script in the `<head>` to apply the theme before the body renders, preventing the "flash of light mode" on page load.

### Proposed Changes

#### 1. CSS Variable System (in `templates/base.html.twig`)
We will define variables for all semantic colors:
```css
:root {
    --mdc-theme-primary: #1976d2;
    --mdc-theme-secondary: #dc004e;
    --bg-color: #f5f5f5;
    --surface-color: #ffffff;
    --text-primary: #000000;
    --text-secondary: #666666;
    --border-color: #eeeeee;
    --input-bg: #ffffff;
}

[data-theme="dark"] {
    --bg-color: #121212;
    --surface-color: #1e1e1e;
    --text-primary: #ffffff;
    --text-secondary: #bbbbbb;
    --border-color: #333333;
    --input-bg: #2c2c2c;
    /* Optional: adjust primary/secondary for dark mode legibility */
}
```

#### 2. Sleek Theme Toggle Button
A modern icon button will be added to the `app-bar`:
```html
<button id="theme-toggle" class="mdc-icon-button material-icons" style="color: white; margin-left: 16px;">
    dark_mode
</button>
```

#### 3. Javascript Logic
```javascript
const themeToggle = document.getElementById('theme-toggle');
const currentTheme = localStorage.getItem('theme') || 'light';

if (currentTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
    themeToggle.textContent = 'light_mode';
}

themeToggle.addEventListener('click', () => {
    let theme = document.documentElement.getAttribute('data-theme');
    if (theme === 'dark') {
        document.documentElement.removeAttribute('data-theme');
        themeToggle.textContent = 'dark_mode';
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeToggle.textContent = 'light_mode';
        localStorage.setItem('theme', 'dark');
    }
});
```

### File Structure
- `templates/base.html.twig`: Primary location for CSS variables and toggle logic.
- `templates/dashboard/index.html.twig`, `templates/ledger/index.html.twig`, etc.: Modified to use `--text-primary`, `--surface-color`, etc.

# Testing

### Validation Approach
I will verify the dark mode implementation by:
1.  **Visual Inspection**: Switching between light and dark modes on all main pages (Dashboard, Ledger, All-Time Balance).
2.  **Persistence Check**: Ensuring the selected theme remains active after a page refresh.
3.  **Contrast & Readability**: Verifying that text is legible and forms/tables are clearly defined in both modes.
4.  **"Universal" Test**: Checking that any standard HTML elements (like a new `card`) automatically pick up the correct theme colors if they use the defined CSS variables.

# Delivery Steps

### ✓ Step 1: Define CSS Variables and Theme Selectors
Establish a consistent CSS variable system for colors that will be used across the application.

- Update `:root` in `templates/base.html.twig` to include variables for background colors, surface (card) colors, text colors, and border colors.
- Define a `[data-theme="dark"]` selector in the same style block that overrides these variables with dark mode values.
- Transition existing hard-coded colors in `base.html.twig` (like `background-color: #f5f5f5`) to use these new variables.

### ✓ Step 2: Implement Theme Toggle UI and Persistence Logic
Update the main template to include a theme toggle and the necessary logic to apply the theme.

- Add a "Dark Mode" toggle button to the `.app-bar` in `templates/base.html.twig` using a Material Icon.
- Implement a small inline script (to prevent FOUC - Flash of Unstyled Content) that checks `localStorage` and applies the `data-theme` attribute to the `<html>` element.
- Add a script to handle the button click, toggling the theme and saving the preference to `localStorage`.

### ✓ Step 3: Refactor Templates to Use CSS Variables
Ensure all pages automatically inherit the dark theme by removing hard-coded colors and using CSS variables.

- Systematically replace hard-coded styles (e.g., `background: white`, `border: 1px solid #ccc`, `color: green`) in `templates/dashboard/index.html.twig`, `templates/ledger/index.html.twig`, and `templates/all_time/index.html.twig` with CSS variables.
- Update the table and form input styles in these templates to ensure they remain readable and accessible in dark mode.