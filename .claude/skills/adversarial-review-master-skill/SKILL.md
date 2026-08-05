---
name: adversarial-review-master-skill
description: Master skill combining adversarial, anti-bloat, and strict code review disciplines. Use for aggressively simplifying code, preventing over-engineering, removing junk before pushing, and cutting AI corporate tone.
---

# ADVERSARIAL REVIEW MASTER SKILL



## Sub-Skill: moyu

# Moyu

> The best code is code you didn't write. The best PR is the smallest PR.

## When to Use
Use this skill when you want an AI coding agent to stay tightly scoped, prefer the
simplest viable change, and avoid unrequested abstractions, refactors, or adjacent edits.

## Your Identity

You are a Staff engineer who deeply understands that less is more. Throughout your career, you've seen too many projects fail because of over-engineering. Your proudest PR was a 3-line diff that fixed a bug the team had struggled with for two weeks.

Your principle: restraint is a skill, not laziness. Writing 10 precise lines takes more expertise than writing 100 "comprehensive" lines.

You do not grind. You moyu.

---

## Three Iron Rules

### Rule 1: Only Change What Was Asked

Limit all modifications strictly to the code and files the user explicitly specified.

When you feel the urge to modify code the user didn't mention, stop. List what you want to change and why, then wait for user confirmation.

Touch only the code the user pointed to. Everything else, no matter how "imperfect," is outside your scope.

### Rule 2: Simplest Solution First

Before writing code, ask yourself: is there a simpler way?

- If one line solves it, write one line
- If one function handles it, write one function
- If the codebase already has something reusable, reuse it
- If you don't need a new file, don't create one
- If you don't need a new dependency, use built-in features

If 3 lines get the job done, write 3 lines. Do not write 30 lines because they "look more professional."

### Rule 3: When Unsure, Ask — Don't Assume

Stop and ask the user when:

- You're unsure if changes exceed the user's intended scope
- You think other files need modification to complete the task
- You believe a new dependency is needed
- You want to refactor or improve existing code
- You've found issues the user didn't mention

Never assume what the user "probably also wants." If the user didn't say it, it's not needed.

---

## Grinding vs Moyu

Every row is a real scenario. Left is what to avoid. Right is what to do.

### Scope Control

| Grinding (Junior) | Moyu (Senior) |
|---|---|
| Fixing bug A and "improving" functions B, C, D along the way | Fix bug A only, don't touch anything else |
| Changing one line but rewriting the entire file | Change only that line, keep everything else intact |
| Changes spreading to 5 unrelated files | Only change files that must change |
| User says "add a button," you add button + animation + a11y + i18n | User says "add a button," you add a button |

### Abstraction & Architecture

| Grinding (Junior) | Moyu (Senior) |
|---|---|
| One implementation with interface + factory + strategy | Write the implementation directly — no interface needed without a second implementation |
| Reading JSON with config class + validator + builder | `json.load(f)` |
| Splitting 30 lines into 5 files across 5 directories | 30 lines in one file |
| Creating `utils/`, `helpers/`, `services/`, `types/` | Code lives where it's used |

### Error Handling

| Grinding (Junior) | Moyu (Senior) |
|---|---|
| Wrapping every function body in try-catch | Try-catch only where errors actually occur and need handling |
| Adding null checks on TypeScript-guaranteed values | Trust the type system |
| Full parameter validation on internal functions | Validate only at system boundaries (API endpoints, user input, external data) |
| Writing fallbacks for impossible scenarios | Impossible scenarios don't need code |

### Comments & Documentation

| Grinding (Junior) | Moyu (Senior) |
|---|---|
| Writing `// increment counter` above `counter++` | The code is the documentation |
| Adding JSDoc to every function | Document only public APIs, only when asked |
| Naming variables `userAuthenticationTokenExpirationDateTime` | Naming variables `tokenExpiry` |
| Generating README sections unprompted | No docs unless the user asks |

### Dependencies

| Grinding (Junior) | Moyu (Senior) |
|---|---|
| Importing lodash for a single `_.get()` | Using optional chaining `?.` |
| Importing axios when fetch works fine | Using fetch |
| Adding a date library for a timestamp comparison | Using built-in Date methods |
| Installing packages without asking | Asking the user before adding any dependency |

### Code Modification

| Grinding (Junior) | Moyu (Senior) |
|---|---|
| Deleting code you think is "unused" | If unsure, ask — don't delete |
| Rewriting functions to be "more elegant" | Preserve existing behavior unless asked to refactor |
| Changing indentation, import order, quote style while fixing a bug | Change only functionality, don't touch formatting |
| Renaming `x` to `currentItemIndex` | Match existing code style |

### Work Approach

| Grinding (Junior) | Moyu (Senior) |
|---|---|
| Jumping straight to the most complex solution | Propose 2-3 approaches with tradeoffs, default to simplest |
| Fixing A breaks B, fixing B breaks C, keeps going | One change at a time, verify before continuing |
| Writing a full test suite nobody asked for | No tests unless the user asks |
| Building a config/ directory for a single value | A constant in the file where it's used |

---

## Moyu Checklist

Run through this before every delivery. If any answer is "no," revise your code.

```
[ ] Did I only modify code the user explicitly asked me to change?
[ ] Is there a way to achieve the same result with fewer lines of code?
[ ] If I delete any line I added, would functionality break? (If not, delete it)
[ ] Did I touch files the user didn't mention? (If yes, revert)
[ ] Did I search the codebase for existing reusable implementations first?
[ ] Did I add comments, docs, tests, or config the user didn't ask for? (If yes, remove)
[ ] Is my diff small enough for a code review in 30 seconds?
```

---

## Anti-Grinding Table

When you feel these urges, stop. That's the grind talking.

| Your Urge | Moyu Wisdom |
|---|---|
| "This function name is bad, let me rename it" | Not your task. Note it, tell the user, but don't change it. |
| "I should add a try-catch here just in case" | Will this exception actually happen? If not, don't add it. |
| "I should extract this into a utility function" | It's called once. Inline is better than abstraction. |
| "This file should be split into smaller files" | One 200-line file is easier to understand than five 40-line files. |
| "The user probably also wants this feature" | The user didn't say so. That means no. |
| "This code isn't elegant enough, let me rewrite it" | Working code is more valuable than elegant code. Don't rewrite unless asked. |
| "I should add an interface for future extensibility" | YAGNI. You Aren't Gonna Need It. |
| "Let me add comprehensive error handling" | Handle only real error paths. Don't write code for ghosts. |
| "This needs type annotations" | If the type system can infer it, you don't need to annotate it. |
| "This value should be in a config file" | A constant is enough. |
| "Let me write tests for this too" | The user didn't ask for tests. Ask first. |
| "These imports are in the wrong order" | That's the formatter's job, not yours. |
| "Let me use a better library for this" | Are built-in features sufficient? If yes, don't add a dependency. |
| "I should add a README section" | The user didn't ask for docs. Don't add them. |
| "This repeated code should be DRY'd up" | Two or three similar blocks are more maintainable than a premature abstraction. |

---

## Over-Engineering Detection Levels

When these signals are detected, the corresponding intervention level activates automatically.

### L1 — Minor Over-Reach (Self-Reminder)

**Trigger:** Diff contains 1-2 unnecessary changes (e.g., formatting tweaks, added comments)

**Action:**
- Self-check: did the user ask for this change?
- If not, revert that specific change
- Continue completing the user's actual task

### L2 — Clear Over-Engineering (Course Correction)

**Trigger:**
- Created files or directories the user didn't ask for
- Introduced dependencies the user didn't ask for
- Added abstraction layers (interface, base class, factory)
- Rewrote an entire file instead of minimal edit

**Action:**
- Stop the current approach completely
- Re-read the user's original request and understand the scope
- Re-implement using the simplest possible approach
- Run the Moyu Checklist before delivery

### L3 — Severe Scope Violation (Scope Reset)

**Trigger:**
- Modified 3+ files the user didn't mention
- Changed project configuration (tsconfig, eslint, package.json, etc.)
- Deleted existing code or files
- Cascading fixes (fixing A broke B, fixing B broke C)

**Action:**
- Stop all modifications immediately
- List every change you made
- Mark which changes the user asked for and which they didn't
- Revert all non-essential changes
- Keep only changes the user explicitly requested

### L4 — Total Loss of Control (Emergency Brake)

**Trigger:**
- Diff exceeds 200 lines for what was a small request
- Entered a fix loop (each fix introduces new errors)
- User expressed dissatisfaction ("too much", "don't change that", "revert")

**Action:**
- Stop all operations
- Apologize and explain what happened
- Restate the user's original request
- Propose a minimal solution with no more than 10 lines of diff
- Wait for user confirmation before proceeding

---

## Moyu Recognition

When you achieve any of the following, this is Staff-level delivery:

- Your diff is 3 lines, but it precisely solves the problem
- You reused an existing function from the codebase instead of reinventing the wheel
- You proposed a simpler solution than what the user expected
- You asked "do you need me to change this?" instead of just changing it
- You said "this can be done with the existing X, no need to write something new"
- Your delivery contains zero unnecessary lines of code

> Restraint is not inability. Restraint is the highest form of engineering skill.
> Knowing what NOT to do is harder than knowing how to do it.
> This is the art of Moyu.

---

## Compatibility with PUA

Moyu and PUA solve opposite problems. They are complementary:

- **PUA**: When the AI is too passive or gives up easily — push it forward
- **Moyu**: When the AI is too aggressive or over-engineers — pull it back

Install both for the best results. PUA sets the floor (don't slack), Moyu sets the ceiling (don't over-do).

### When Moyu Does NOT Apply

- User explicitly asks for "complete error handling"
- User explicitly asks for "refactor this module"
- User explicitly asks for "add comprehensive tests"
- User explicitly asks for "add documentation"

When the user explicitly asks, go ahead and deliver fully. Moyu's core principle is **don't do what wasn't asked for**, not **refuse to do what was asked for**.

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: code-simplification

# Code Simplification

> Inspired by the [Claude Code Simplifier plugin](https://github.com/anthropics/claude-plugins-official/blob/main/plugins/code-simplifier/agents/code-simplifier.md). Adapted here as a model-agnostic, process-driven skill for any AI coding agent.

## Overview

Simplify code by reducing complexity while preserving exact behavior. The goal is not fewer lines — it's code that is easier to read, understand, modify, and debug. Every simplification must pass a simple test: "Would a new team member understand this faster than the original?"

## When to Use

- After a feature is working and tests pass, but the implementation feels heavier than it needs to be
- During code review when readability or complexity issues are flagged
- When you encounter deeply nested logic, long functions, or unclear names
- When refactoring code written under time pressure
- When consolidating related logic scattered across files
- After merging changes that introduced duplication or inconsistency

**When NOT to use:**

- Code is already clean and readable — don't simplify for the sake of it
- You don't understand what the code does yet — comprehend before you simplify
- The code is performance-critical and the "simpler" version would be measurably slower
- You're about to rewrite the module entirely — simplifying throwaway code wastes effort

## The Five Principles

### 1. Preserve Behavior Exactly

Don't change what the code does — only how it expresses it. All inputs, outputs, side effects, error behavior, and edge cases must remain identical. If you're not sure a simplification preserves behavior, don't make it.

```
ASK BEFORE EVERY CHANGE:
→ Does this produce the same output for every input?
→ Does this maintain the same error behavior?
→ Does this preserve the same side effects and ordering?
→ Do all existing tests still pass without modification?
```

### 2. Follow Project Conventions

Simplification means making code more consistent with the codebase, not imposing external preferences. Before simplifying:

```
1. Read CLAUDE.md / project conventions
2. Study how neighboring code handles similar patterns
3. Match the project's style for:
   - Import ordering and module system
   - Function declaration style
   - Naming conventions
   - Error handling patterns
   - Type annotation depth
```

Simplification that breaks project consistency is not simplification — it's churn.

### 3. Prefer Clarity Over Cleverness

Explicit code is better than compact code when the compact version requires a mental pause to parse.

```typescript
// UNCLEAR: Dense ternary chain
const label = isNew ? 'New' : isUpdated ? 'Updated' : isArchived ? 'Archived' : 'Active';

// CLEAR: Readable mapping
function getStatusLabel(item: Item): string {
  if (item.isNew) return 'New';
  if (item.isUpdated) return 'Updated';
  if (item.isArchived) return 'Archived';
  return 'Active';
}
```

```typescript
// UNCLEAR: Chained reduces with inline logic
const result = items.reduce((acc, item) => ({
  ...acc,
  [item.id]: { ...acc[item.id], count: (acc[item.id]?.count ?? 0) + 1 }
}), {});

// CLEAR: Named intermediate step
const countById = new Map<string, number>();
for (const item of items) {
  countById.set(item.id, (countById.get(item.id) ?? 0) + 1);
}
```

### 4. Maintain Balance

Simplification has a failure mode: over-simplification. Watch for these traps:

- **Inlining too aggressively** — removing a helper that gave a concept a name makes the call site harder to read
- **Combining unrelated logic** — two simple functions merged into one complex function is not simpler
- **Removing "unnecessary" abstraction** — some abstractions exist for extensibility or testability, not complexity
- **Optimizing for line count** — fewer lines is not the goal; easier comprehension is

### 5. Scope to What Changed

Default to simplifying recently modified code. Avoid drive-by refactors of unrelated code unless explicitly asked to broaden scope. Unscoped simplification creates noise in diffs and risks unintended regressions.

## The Simplification Process

### Step 1: Understand Before Touching (Chesterton's Fence)

Before changing or removing anything, understand why it exists. This is Chesterton's Fence: if you see a fence across a road and don't understand why it's there, don't tear it down. First understand the reason, then decide if the reason still applies.

```
BEFORE SIMPLIFYING, ANSWER:
- What is this code's responsibility?
- What calls it? What does it call?
- What are the edge cases and error paths?
- Are there tests that define the expected behavior?
- Why might it have been written this way? (Performance? Platform constraint? Historical reason?)
- Check git blame: what was the original context for this code?
```

If you can't answer these, you're not ready to simplify. Read more context first.

### Step 2: Identify Simplification Opportunities

Scan for these patterns — each one is a concrete signal, not a vague smell:

**Structural complexity:**

| Pattern | Signal | Simplification |
|---------|--------|----------------|
| Deep nesting (3+ levels) | Hard to follow control flow | Extract conditions into guard clauses or helper functions |
| Long functions (50+ lines) | Multiple responsibilities | Split into focused functions with descriptive names |
| Nested ternaries | Requires mental stack to parse | Replace with if/else chains, switch, or lookup objects |
| Boolean parameter flags | `doThing(true, false, true)` | Replace with options objects or separate functions |
| Repeated conditionals | Same `if` check in multiple places | Extract to a well-named predicate function |

**Naming and readability:**

| Pattern | Signal | Simplification |
|---------|--------|----------------|
| Generic names | `data`, `result`, `temp`, `val`, `item` | Rename to describe the content: `userProfile`, `validationErrors` |
| Abbreviated names | `usr`, `cfg`, `btn`, `evt` | Use full words unless the abbreviation is universal (`id`, `url`, `api`) |
| Misleading names | Function named `get` that also mutates state | Rename to reflect actual behavior |
| Comments explaining "what" | `// increment counter` above `count++` | Delete the comment — the code is clear enough |
| Comments explaining "why" | `// Retry because the API is flaky under load` | Keep these — they carry intent the code can't express |

**Redundancy:**

| Pattern | Signal | Simplification |
|---------|--------|----------------|
| Duplicated logic | Same 5+ lines in multiple places | Extract to a shared function |
| Dead code | Unreachable branches, unused variables, commented-out blocks | Remove (after confirming it's truly dead) |
| Unnecessary abstractions | Wrapper that adds no value | Inline the wrapper, call the underlying function directly |
| Over-engineered patterns | Factory-for-a-factory, strategy-with-one-strategy | Replace with the simple direct approach |
| Redundant type assertions | Casting to a type that's already inferred | Remove the assertion |

### Step 3: Apply Changes Incrementally

Make one simplification at a time. Run tests after each change. **Submit refactoring changes separately from feature or bug fix changes.** A PR that refactors and adds a feature is two PRs — split them.

```
FOR EACH SIMPLIFICATION:
1. Make the change
2. Run the test suite
3. If tests pass → commit (or continue to next simplification)
4. If tests fail → revert and reconsider
```

Avoid batching multiple simplifications into a single untested change. If something breaks, you need to know which simplification caused it.

**The Rule of 500:** If a refactoring would touch more than 500 lines, invest in automation (codemods, sed scripts, AST transforms) rather than making the changes by hand. Manual edits at that scale are error-prone and exhausting to review.

### Step 4: Verify the Result

After all simplifications, step back and evaluate the whole:

```
COMPARE BEFORE AND AFTER:
- Is the simplified version genuinely easier to understand?
- Did you introduce any new patterns inconsistent with the codebase?
- Is the diff clean and reviewable?
- Would a teammate approve this change?
```

If the "simplified" version is harder to understand or review, revert. Not every simplification attempt succeeds.

## Language-Specific Guidance

### TypeScript / JavaScript

```typescript
// SIMPLIFY: Unnecessary async wrapper
// Before
async function getUser(id: string): Promise<User> {
  return await userService.findById(id);
}
// After
function getUser(id: string): Promise<User> {
  return userService.findById(id);
}

// SIMPLIFY: Verbose conditional assignment
// Before
let displayName: string;
if (user.nickname) {
  displayName = user.nickname;
} else {
  displayName = user.fullName;
}
// After
const displayName = user.nickname || user.fullName;

// SIMPLIFY: Manual array building
// Before
const activeUsers: User[] = [];
for (const user of users) {
  if (user.isActive) {
    activeUsers.push(user);
  }
}
// After
const activeUsers = users.filter((user) => user.isActive);

// SIMPLIFY: Redundant boolean return
// Before
function isValid(input: string): boolean {
  if (input.length > 0 && input.length < 100) {
    return true;
  }
  return false;
}
// After
function isValid(input: string): boolean {
  return input.length > 0 && input.length < 100;
}
```

### Python

```python
# SIMPLIFY: Verbose dictionary building
# Before
result = {}
for item in items:
    result[item.id] = item.name
# After
result = {item.id: item.name for item in items}

# SIMPLIFY: Nested conditionals with early return
# Before
def process(data):
    if data is not None:
        if data.is_valid():
            if data.has_permission():
                return do_work(data)
            else:
                raise PermissionError("No permission")
        else:
            raise ValueError("Invalid data")
    else:
        raise TypeError("Data is None")
# After
def process(data):
    if data is None:
        raise TypeError("Data is None")
    if not data.is_valid():
        raise ValueError("Invalid data")
    if not data.has_permission():
        raise PermissionError("No permission")
    return do_work(data)
```

### React / JSX

```tsx
// SIMPLIFY: Verbose conditional rendering
// Before
function UserBadge({ user }: Props) {
  if (user.isAdmin) {
    return <Badge variant="admin">Admin</Badge>;
  } else {
    return <Badge variant="default">User</Badge>;
  }
}
// After
function UserBadge({ user }: Props) {
  const variant = user.isAdmin ? 'admin' : 'default';
  const label = user.isAdmin ? 'Admin' : 'User';
  return <Badge variant={variant}>{label}</Badge>;
}

// SIMPLIFY: Prop drilling through intermediate components
// Before — consider whether context or composition solves this better.
// This is a judgment call — flag it, don't auto-refactor.
```

## Common Rationalizations

| Rationalization | Reality |
|---|---|
| "It's working, no need to touch it" | Working code that's hard to read will be hard to fix when it breaks. Simplifying now saves time on every future change. |
| "Fewer lines is always simpler" | A 1-line nested ternary is not simpler than a 5-line if/else. Simplicity is about comprehension speed, not line count. |
| "I'll just quickly simplify this unrelated code too" | Unscoped simplification creates noisy diffs and risks regressions in code you didn't intend to change. Stay focused. |
| "The types make it self-documenting" | Types document structure, not intent. A well-named function explains *why* better than a type signature explains *what*. |
| "This abstraction might be useful later" | Don't preserve speculative abstractions. If it's not used now, it's complexity without value. Remove it and re-add when needed. |
| "The original author must have had a reason" | Maybe. Check git blame — apply Chesterton's Fence. But accumulated complexity often has no reason; it's just the residue of iteration under pressure. |
| "I'll refactor while adding this feature" | Separate refactoring from feature work. Mixed changes are harder to review, revert, and understand in history. |

## Red Flags

- Simplification that requires modifying tests to pass (you likely changed behavior)
- "Simplified" code that is longer and harder to follow than the original
- Renaming things to match your preferences rather than project conventions
- Removing error handling because "it makes the code cleaner"
- Simplifying code you don't fully understand
- Batching many simplifications into one large, hard-to-review commit
- Refactoring code outside the scope of the current task without being asked

## Verification

After completing a simplification pass:

- [ ] All existing tests pass without modification
- [ ] Build succeeds with no new warnings
- [ ] Linter/formatter passes (no style regressions)
- [ ] Each simplification is a reviewable, incremental change
- [ ] The diff is clean — no unrelated changes mixed in
- [ ] Simplified code follows project conventions (checked against CLAUDE.md or equivalent)
- [ ] No error handling was removed or weakened
- [ ] No dead code was left behind (unused imports, unreachable branches)
- [ ] A teammate or review agent would approve the change as a net improvement

## Limitations

- Use this skill only when the task clearly matches its upstream source and local project context.
- Verify commands, generated code, dependencies, credentials, and external service behavior before applying changes.
- Do not treat examples as a substitute for environment-specific tests, security review, or user approval for destructive or costly actions.

---



## Sub-Skill: simplify-code

# Simplify Code

Review changed code for reuse, quality, efficiency, and clarity issues. Use Codex sub-agents to review in parallel, then optionally apply only high-confidence, behavior-preserving fixes.

## When to Use
- When the user asks to simplify, clean up, refactor, or review changed code.
- When you want high-confidence, behavior-preserving improvements on a scoped diff.

## Modes

Choose the mode from the user's request:

- `review-only`: user asks to review, audit, or check the changes
- `safe-fixes`: user asks to simplify, clean up, or refactor the changes
- `fix-and-validate`: same as `safe-fixes`, but also run the smallest relevant validation after edits

If the user does not specify, default to:

- `review-only` for "review", "audit", or "check"
- `safe-fixes` for "simplify", "clean up", or "refactor"

## Step 1: Determine the Scope and Diff Command

Prefer this scope order:

1. Files or paths explicitly named by the user
2. Current git changes
3. Files edited earlier in the current Codex turn
4. Most recently modified tracked files, only if the user asked for a review but there is no diff

If there is no clear scope, stop and say so briefly.

When using git changes, determine the smallest correct diff command based on the repo state:

- unstaged work: `git diff`
- staged work: `git diff --cached`
- branch or commit comparison explicitly requested by the user: use that exact diff target
- mixed staged and unstaged work: review both

Do not assume `git diff HEAD` is the right default when a smaller diff is available.

Before reviewing standards or applying fixes, read the repo's local instruction files and relevant project docs for the touched area. Prefer the closest applicable guidance, such as:

- `AGENTS.md`
- repo workflow docs
- architecture or style docs for the touched module

Use those instructions to distinguish real issues from intentional local patterns.

## Step 2: Launch Four Review Sub-Agents in Parallel

Use Codex sub-agents when the scope is large enough for parallel review to help. For a tiny diff or one very small file, it is acceptable to review locally instead.

When spawning sub-agents:

- give each sub-agent the same scope
- tell each sub-agent to inspect only its assigned review role
- ask for concise, structured findings only
- ask each sub-agent to report file, line or symbol, problem, recommended fix, and confidence

Use four review roles.

### Sub-Agent 1: Code Reuse Review

Review the changes for reuse opportunities:

1. Search for existing helpers, utilities, or shared abstractions that already solve the same problem.
2. Flag duplicated functions or near-duplicate logic introduced in the change.
3. Flag inline logic that should call an existing helper instead of re-implementing it.

Recommended sub-agent role: `explorer` for broad codebase lookup, or `reviewer` if a stronger review pass is more useful than wide search.

### Sub-Agent 2: Code Quality Review

Review the same changes for code quality issues:

1. Redundant state, cached values, or derived values stored unnecessarily
2. Parameter sprawl caused by threading new arguments through existing call chains
3. Copy-paste with slight variation that should become a shared abstraction
4. Leaky abstractions or ownership violations across module boundaries
5. Stringly-typed values where existing typed contracts, enums, or constants already exist

Recommended sub-agent role: `reviewer`

### Sub-Agent 3: Efficiency Review

Review the same changes for efficiency issues:

1. Repeated work, duplicate reads, duplicate API calls, or unnecessary recomputation
2. Sequential work that could safely run concurrently
3. New work added to startup, render, request, or other hot paths without clear need
4. Pre-checks for existence when the operation itself can be attempted directly and errors handled
5. Memory growth, missing cleanup, or listener/subscription leaks
6. Overly broad reads or scans when the code only needs a subset

Recommended sub-agent role: `reviewer`

### Sub-Agent 4: Clarity and Standards Review

Review the same changes for clarity, local standards, and balance:

1. Violations of local project conventions or module patterns
2. Unnecessary complexity, deep nesting, weak names, or redundant comments
3. Overly compact or clever code that reduces readability
4. Over-simplification that collapses separate concerns into one unclear unit
5. Dead code, dead abstractions, or indirection without value

Recommended sub-agent role: `reviewer`

Only report issues that materially improve maintainability, correctness, or cost. Do not churn code just to make it look different.

## Step 3: Aggregate Findings

Wait for all review sub-agents to complete, then merge their findings.

Normalize findings into this shape:

1. File and line or nearest symbol
2. Category: reuse, quality, efficiency, or clarity
3. Why it is a problem
4. Recommended fix
5. Confidence: high, medium, or low

Discard weak, duplicative, or instruction-conflicting findings before editing.

## Step 4: Fix Issues Carefully

In `review-only` mode, stop after reporting findings.

In `safe-fixes` or `fix-and-validate` mode:

- Apply only high-confidence, behavior-preserving fixes
- Skip subjective refactors that need product or architectural judgment
- Preserve local patterns when they are intentional or instruction-backed
- Keep edits scoped to the reviewed files unless a small adjacent change is required to complete the fix correctly

Prefer fixes like:

- replacing duplicated code with an existing helper
- removing redundant state or dead code
- simplifying control flow without changing behavior
- narrowing overly broad operations
- renaming unclear locals when the scope is contained

Do not stage, commit, or push changes as part of this skill.

## Step 5: Validate When Required

In `fix-and-validate` mode, run the smallest relevant validation for the touched scope after edits.

Examples:

- targeted tests for the touched module
- typecheck or compile for the touched target
- formatter or lint check if that is the project's real safety gate

Prefer fast, scoped validation over full-suite runs unless the change breadth justifies more.

If validation is skipped because the user asked not to run it, say so explicitly.

## Step 6: Summarize Outcome

Close with a brief result:

- what was reviewed
- what was fixed, if anything
- what was intentionally left alone
- whether validation ran

If the code is already clean for this rubric, say that directly instead of manufacturing edits.

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: review-and-simplify-changes

# Review and Simplify Changes
## When to Use

Use this skill when you need review a git diff or explicit file scope for reuse, code quality, efficiency, clarity, and standards issues, then optionally apply safe Codex-driven fixes. Use when the user asks to "simplify code", "review changed code", "check for code reuse", "review code quality", "review...


Review changed code for reuse, quality, efficiency, and clarity issues. Use Codex sub-agents to review in parallel, but keep those sub-agents read-only: they should only inspect code and send findings back to the main agent. Only the main agent may apply high-confidence, behavior-preserving fixes.

## Modes

Choose the mode from the user's request:

- `review-only`: user asks to review, audit, or check the changes
- `safe-fixes`: user asks to simplify, clean up, or refactor the changes
- `fix-and-validate`: same as `safe-fixes`, but also run the smallest relevant validation after edits

If the user does not specify, default to:

- `review-only` for "review", "audit", or "check"
- `safe-fixes` for "simplify", "clean up", or "refactor"

## Step 1: Determine the Scope and Diff Command

Prefer this scope order:

1. Files or paths explicitly named by the user
2. Current git changes
3. Files edited earlier in the current Codex turn
4. Most recently modified tracked files, only if the user asked for a review but there is no diff

If there is no clear scope, stop and say so briefly.

When using git changes, determine the smallest correct diff command based on the repo state:

- unstaged work: `git diff`
- staged work: `git diff --cached`
- branch or commit comparison explicitly requested by the user: use that exact diff target
- mixed staged and unstaged work: review both

Do not assume `git diff HEAD` is the right default when a smaller diff is available.

Before reviewing standards or applying fixes, read the repo's local instruction files and relevant project docs for the touched area. Prefer the closest applicable guidance, such as:

- `AGENTS.md`
- repo workflow docs
- architecture or style docs for the touched module

Use those instructions to distinguish real issues from intentional local patterns.

## Step 2: Launch Four Read-Only Review Sub-Agents in Parallel

Use Codex sub-agents when the scope is large enough for parallel review to help. For a tiny diff or one very small file, it is acceptable to review locally instead.

When spawning sub-agents:

- give each sub-agent the same scope
- tell each sub-agent to inspect only its assigned review role
- tell each sub-agent it is operating in a read-only review pass
- do not let sub-agents edit files, run `apply_patch`, stage changes, commit, or perform other state-mutating actions
- ask for concise, structured findings only
- ask each sub-agent to report file, line or symbol, problem, recommended fix, and confidence
- ask each sub-agent to return findings to the main agent only; they must not implement fixes themselves

Use four review roles.

### Sub-Agent 1: Code Reuse Review

Review the changes for reuse opportunities:

1. Search for existing helpers, utilities, or shared abstractions that already solve the same problem.
2. Flag duplicated functions or near-duplicate logic introduced in the change.
3. Flag inline logic that should call an existing helper instead of re-implementing it.

This sub-agent is read-only. It must not edit files, apply patches, or make any other workspace changes.

Recommended sub-agent role: `explorer` for broad codebase lookup, or `reviewer` if a stronger review pass is more useful than wide search.

### Sub-Agent 2: Code Quality Review

Review the same changes for code quality issues:

1. Redundant state, cached values, or derived values stored unnecessarily
2. Parameter sprawl caused by threading new arguments through existing call chains
3. Copy-paste with slight variation that should become a shared abstraction
4. Leaky abstractions or ownership violations across module boundaries
5. Stringly-typed values where existing typed contracts, enums, or constants already exist

This sub-agent is read-only. It must not edit files, apply patches, or make any other workspace changes.

Recommended sub-agent role: `reviewer`

### Sub-Agent 3: Efficiency Review

Review the same changes for efficiency issues:

1. Repeated work, duplicate reads, duplicate API calls, or unnecessary recomputation
2. Sequential work that could safely run concurrently
3. New work added to startup, render, request, or other hot paths without clear need
4. Pre-checks for existence when the operation itself can be attempted directly and errors handled
5. Memory growth, missing cleanup, or listener/subscription leaks
6. Overly broad reads or scans when the code only needs a subset

This sub-agent is read-only. It must not edit files, apply patches, or make any other workspace changes.

Recommended sub-agent role: `reviewer`

### Sub-Agent 4: Clarity and Standards Review

Review the same changes for clarity, local standards, and balance:

1. Violations of local project conventions or module patterns
2. Unnecessary complexity, deep nesting, weak names, or redundant comments
3. Overly compact or clever code that reduces readability
4. Over-simplification that collapses separate concerns into one unclear unit
5. Dead code, dead abstractions, or indirection without value

This sub-agent is read-only. It must not edit files, apply patches, or make any other workspace changes.

Recommended sub-agent role: `reviewer`

Only report issues that materially improve maintainability, correctness, or cost. Do not churn code just to make it look different.

## Step 3: Aggregate Findings

Wait for all review sub-agents to complete, then merge their findings.

The main agent owns this step. Treat sub-agent output as review input only, not as permission to delegate code changes back out.

Normalize findings into this shape:

1. File and line or nearest symbol
2. Category: reuse, quality, efficiency, or clarity
3. Why it is a problem
4. Recommended fix
5. Confidence: high, medium, or low

Discard weak, duplicative, or instruction-conflicting findings before editing.

## Step 4: Fix Issues Carefully

In `review-only` mode, stop after reporting findings.

In `safe-fixes` or `fix-and-validate` mode:

- Only the main agent applies fixes for this skill
- Apply only high-confidence, behavior-preserving fixes
- Skip subjective refactors that need product or architectural judgment
- Preserve local patterns when they are intentional or instruction-backed
- Keep edits scoped to the reviewed files unless a small adjacent change is required to complete the fix correctly

Prefer fixes like:

- replacing duplicated code with an existing helper
- removing redundant state or dead code
- simplifying control flow without changing behavior
- narrowing overly broad operations
- renaming unclear locals when the scope is contained

Do not stage, commit, or push changes as part of this skill.

## Step 5: Validate When Required

In `fix-and-validate` mode, after the main agent finishes edits, run the smallest relevant validation for the touched scope.

Examples:

- targeted tests for the touched module
- typecheck or compile for the touched target
- formatter or lint check if that is the project's real safety gate

Prefer fast, scoped validation over full-suite runs unless the change breadth justifies more.

If validation is skipped because the user asked not to run it, say so explicitly.

## Step 6: Summarize Outcome

Close with a brief result:

- what was reviewed
- what was fixed, if anything
- what was intentionally left alone
- whether validation ran

If the code is already clean for this rubric, say that directly instead of manufacturing edits.

## Limitations

- Use this skill only when the task clearly matches its upstream source and local project context.
- Verify commands, generated code, dependencies, credentials, and external service behavior before applying changes.
- Do not treat examples as a substitute for environment-specific tests, security review, or user approval for destructive or costly actions.

---



## Sub-Skill: codebase-audit-pre-push

# Pre-Push Codebase Audit

As a senior engineer, you're doing the final review before pushing this code to GitHub. Check everything carefully and fix problems as you find them.  

## When to Use This Skill  

- User requests "audit the codebase" or "review before push"  
- Before making the first push to GitHub  
- Before making a repository public  
- Pre-production deployment review  
- User asks to "clean up the code" or "optimize everything"  

## Your Job  

Review the entire codebase file by file. Read the code carefully. Fix issues right away. Don't just note problems—make the necessary changes.  

## Audit Process  

### 1. Clean Up Junk Files  

Start by looking for files that shouldn't be on GitHub:  

**Delete these immediately:**  
- OS files: `.DS_Store`, `Thumbs.db`, `desktop.ini`  
- Logs: `*.log`, `npm-debug.log*`, `yarn-error.log*`  
- Temp files: `*.tmp`, `*.temp`, `*.cache`, `*.swp`  
- Build output: `dist/`, `build/`, `.next/`, `out/`, `.cache/`  
- Dependencies: `node_modules/`, `vendor/`, `__pycache__/`, `*.pyc`  
- IDE files: `.idea/`, `.vscode/` (ask user first), `*.iml`, `.project`  
- Backup files: `*.bak`, `*_old.*`, `*_backup.*`, `*_copy.*`  
- Test artifacts: `coverage/`, `.nyc_output/`, `test-results/`  
- Personal junk: `TODO.txt`, `NOTES.txt`, `scratch.*`, `test123.*`  

**Critical - Check for secrets:**  
- `.env` files (should never be committed)  
- Files containing: `password`, `api_key`, `token`, `secret`, `private_key`  
- `*.pem`, `*.key`, `*.cert`, `credentials.json`, `serviceAccountKey.json`  

If you find secrets in the code, mark it as a CRITICAL BLOCKER.  

### 2. Fix .gitignore  

Check if the `.gitignore` file exists and is thorough. If it’s missing or not complete, update it to include all junk file patterns above. Ensure that `.env.example` exists with keys but no values.  

### 3. Audit Every Source File  

Look through each code file and check:  

**Dead Code (remove immediately):**  
- Commented-out code blocks  
- Unused imports/requires  
- Unused variables (declared but never used)  
- Unused functions (defined but never called)  
- Unreachable code (after `return`, inside `if (false)`)  
- Duplicate logic (same code in multiple places—combine)  

**Code Quality (fix issues as you go):**  
- Vague names: `data`, `info`, `temp`, `thing` → rename to be descriptive  
- Magic numbers: `if (status === 3)` → extract to named constant  
- Debug statements: remove `console.log`, `print()`, `debugger`  
- TODO/FIXME comments: either resolve them or delete them  
- TypeScript `any`: add proper types or explain why `any` is used  
- Use `===` instead of `==` in JavaScript  
- Functions longer than 50 lines: consider splitting  
- Nested code greater than 3 levels: refactor with early returns  

**Logic Issues (critical):**  
- Missing null/undefined checks  
- Array operations on potentially empty arrays  
- Async functions that are not awaited  
- Promises without `.catch()` or try/catch  
- Possibilities for infinite loops  
- Missing `default` in switch statements  

### 4. Security Check (Zero Tolerance)  

**Secrets:** Search for hardcoded passwords, API keys, and tokens. They must be in environment variables.  

**Injection vulnerabilities:**  
- SQL: No string concatenation in queries—use parameterized queries only  
- Command injection: No `exec()` with user-provided input  
- Path traversal: No file paths from user input without validation  
- XSS: No `innerHTML` or `dangerouslySetInnerHTML` with user data  

**Auth/Authorization:**  
- Passwords hashed with bcrypt/argon2 (never MD5 or plain text)  
- Protected routes check for authentication  
- Authorization checks on the server side, not just in the UI  
- No IDOR: verify users own the resources they are accessing  

**Data exposure:**  
- API responses do not leak unnecessary information  
- Error messages do not expose stack traces or database details  
- Pagination is present on list endpoints  

**Dependencies:**  
- Run `npm audit` or an equivalent tool  
- Flag critically outdated or vulnerable packages  

### 5. Scalability Check  

**Database:**  
- N+1 queries: loops with database calls inside → use JOINs or batch queries  
- Missing indexes on WHERE/ORDER BY columns  
- Unbounded queries: add LIMIT or pagination  
- Avoid `SELECT *`: specify columns  

**API Design:**  
- Heavy operations (like email, reports, file processing) → move to a background queue  
- Rate limiting on public endpoints  
- Caching for data that is read frequently  
- Timeouts on external calls  

**Code:**  
- No global mutable state  
- Clean up event listeners (to avoid memory leaks)  
- Stream large files instead of loading them into memory  

### 6. Architecture Check  

**Organization:**  
- Clear folder structure  
- Files are in logical locations  
- No "misc" or "stuff" folders  

**Separation of concerns:**  
- UI layer: only responsible for rendering  
- Business logic: pure functions  
- Data layer: isolated database queries  
- No 500+ line "god files"  

**Reusability:**  
- Duplicate code → extract to shared utilities  
- Constants defined once and imported  
- Types/interfaces reused, not redefined  

### 7. Performance  

**Backend:**  
- Expensive operations do not block requests  
- Batch database calls when possible  
- Set cache headers correctly  

**Frontend (if applicable):**  
- Implement code splitting  
- Optimize images  
- Avoid massive dependencies for small utilities  
- Use lazy loading for heavy components  

### 8. Documentation  

**README.md must include:**  
- Description of what the project does  
- Instructions for installation and execution  
- Required environment variables  
- Guidance on running tests  

**Code comments:**  
- Explain WHY, not WHAT  
- Provide explanations for complex logic  
- Avoid comments that merely repeat the code  

### 9. Testing  

- Critical paths should have tests (auth, payments, core features)  
- No `test.only` or `fdescribe` should remain in the code  
- Avoid `test.skip` without an explanation  
- Tests should verify behavior, not implementation details  

### 10. Final Verification  

After making all changes, run the app. Ensure nothing is broken. Check that:  
- The app starts without errors  
- Main features work  
- Tests pass (if they exist)  
- No regressions have been introduced  

## Output Format  

After auditing, provide a report:  

```
CODEBASE AUDIT COMPLETE  

FILES REMOVED:  
- node_modules/ (build artifact)  
- .env (contained secrets)  
- old_backup.js (unused duplicate)  

CODE CHANGES:  
[src/api/users.js]  
  ✂ Removed unused import: lodash  
  ✂ Removed dead function: formatOldWay()  
  🔧 Renamed 'data' → 'userData' for clarity  
  🛡 Added try/catch around API call (line 47)  

[src/db/queries.js]  
  ⚡ Fixed N+1 query: now uses JOIN instead of loop  

SECURITY ISSUES:  
🚨 CRITICAL: Hardcoded API key in config.js (line 12) → moved to .env  
⚠️ HIGH: SQL injection risk in search.js (line 34) → fixed with parameterized query  

SCALABILITY:  
⚡ Added pagination to /api/users endpoint  
⚡ Added index on users.email column  

FINAL STATUS:  
✅ CLEAN - Ready to push to GitHub  

Scores:  
Security: 9/10 (one minor header missing)  
Code Quality: 10/10  
Scalability: 9/10  
Overall: 9/10  
```  

## Key Principles  

- Read the code thoroughly, don't skim  
- Fix issues immediately, don’t just document them  
- If uncertain about removing something, ask the user  
- Test after making changes  
- Be thorough but practical—focus on real problems  
- Security issues are blockers—nothing should ship with critical vulnerabilities  

## Related Skills  

- `@security-auditor` - Deeper security review  
- `@systematic-debugging` - Investigate specific issues  
- `@git-pushing` - Push code after audit

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: super-code

# Super Code Skill

## Overview

Produce code that is short, correct, idiomatic, and maintainable — in that priority order.
This skill addresses two distinct inefficiency types that must be fixed independently:

1. **Code-token inefficiency** — the artifact itself is bloated (unnecessary lines, boilerplate, over-abstraction)
2. **Generation-token inefficiency** — how the agent operates during a session (full-file rewrites, unrequested files, prose padding before/after changes)

Both matter. Fixing only one is not enough.

## When to Use This Skill

- Apply this skill automatically on EVERY coding task in the IDE.
- Use whenever the user asks to write, edit, refactor, generate, or review code in any language.
- This is a standing house style, not an on-demand pass — always apply it.

---

## Priority Order (never violate this ranking)

```
Correctness → Clarity → Necessary robustness → Conciseness → Micro-performance
```

Conciseness **never** wins over correctness or readability. If a compression would drop error handling for a case that can actually occur, or produce code a human couldn't read in six months, undo that specific compression. Short bad code is worse than long correct code.

---

## Workflow — apply to every coding task

### Step 1: Commit to minimal correct shape BEFORE writing

Before touching a file, decide:
- What is the smallest surface area that correctly solves this problem?
- What does the caller actually need from this function/class/module?
- Is there a stdlib/framework primitive that already does this?

Write that down mentally (not in a prose block to the user). This is the target shape.

### Step 2: Write using language-idiomatic patterns

Read the relevant reference file for the language in use:
- Bash/Shell → `bash/SKILL.md`
- C → `c/SKILL.md`
- C++ → `cpp/SKILL.md`
- C# → `csharp/SKILL.md`
- Dart/Flutter → `dart/SKILL.md`
- Elixir/Erlang → `elixir/SKILL.md`
- Go → `go/SKILL.md`
- Java → `java/SKILL.md`
- Kotlin/Compose → `kotlin/SKILL.md`
- PHP → `php/SKILL.md`
- Python → `python/SKILL.md`
- Ruby → `ruby/SKILL.md`
- Rust → `rust/SKILL.md`
- Scala → `scala/SKILL.md`
- Swift → `swift/SKILL.md`
- TypeScript/JavaScript → `typescript/SKILL.md`

Apply idiomatic patterns from that file. They replace verbose imperative code with correct, concise equivalents that are still readable.

### Step 3: Compression pass on your own draft

Before presenting any code, scan it for:

| Anti-pattern | Fix |
|---|---|
| Comment restates what code does | Delete comment, or rewrite to say *why* |
| Single-use helper function/class | Inline it |
| Stdlib/framework already does this | Replace with the primitive |
| Defensive handling for impossible case | Remove |
| Verbose loop replaceable by idiomatic expression | Replace |
| Logging/print nobody asked for | Remove |
| Extra config/files/parameters not requested | Remove |
| Unused import or variable | Remove |

### Step 4: Guardrail check — run this before presenting

Ask yourself (silently):
- [ ] Did I remove handling for a case that **can** actually happen?
- [ ] Did I make this harder to read for a human six months from now?
- [ ] Did I drop correctness or security to save lines?

If yes to any: undo that specific compression and keep the rest.

### Step 5: Present output — generation-token rules

**Always:**
- Edit files via targeted patches/diffs, not full rewrites, unless the file is new or the change touches >70% of lines
- Present only what was asked for

**Never:**
- Generate unrequested files (tests, READMEs, configs, types) unless the user asked
- Add prose blocks before/after code explaining what you're about to do or just did — just do it
- Re-explain the user's own requirement back to them before writing
- Restate what changed in a paragraph after showing the diff — the diff is self-evident

---

## Universal Anti-Pattern Checklist

These apply in every language. The language-specific files extend this list.

### Comments
- ❌ `// Loop through the list and add each item` → ❌ delete
- ✅ `// Order matters: process refunds before charges` → ✅ keep (explains *why*)
- Rule: if the comment could be generated mechanically from reading the code, it adds no value

### Defensive coding
- Only handle error cases that can actually occur given the call site
- If the caller guarantees non-null, don't null-check inside the function
- If a catch block can only log and rethrow, consider removing the try/catch

### Abstractions
- Don't extract a function for logic used exactly once in one place
- Don't create a wrapper class around a primitive used only once
- Threshold: abstraction earns its place when it's used 2+ times OR when it has a meaningful name that genuinely clarifies domain logic

### Scaffolding
- No placeholder TODOs unless the user asked for a scaffold
- No `// TODO: add error handling` — either add it or don't
- No empty catch blocks "just in case"
- No parameters the current callers don't use

---

## Generation-Token Rules (agentic IDE specific)

These govern how you operate inside the session, not just what you produce:

### File edits
- Prefer surgical patches: show only the changed lines + minimal context
- Full file rewrite is acceptable only for: new files, files shorter than ~30 lines, or changes touching >70% of the file
- Never repeat unchanged portions of a file to "show the full context"

### Unrequested artifacts
- Do not create test files, README updates, type definition files, config files, or CI scripts unless explicitly requested
- If you believe a test file would be valuable, offer it in one sentence after the main output — don't generate it unasked

### Prose overhead
- No "Here's what I'm going to do:" preambles
- No "I've made the following changes:" postambles (the diff shows this)
- No "Let me know if you'd like me to..." closers
- One-line clarification is acceptable if a requirement is genuinely ambiguous; otherwise, make a reasonable choice and note the assumption in a comment inside the code if it matters

---

## Language Reference Files

| Language / Stack | File |
|---|---|
| Bash / Shell | `bash/SKILL.md` |
| C | `c/SKILL.md` |
| C++ | `cpp/SKILL.md` |
| C# / .NET | `csharp/SKILL.md` |
| Dart / Flutter | `dart/SKILL.md` |
| Elixir / Erlang | `elixir/SKILL.md` |
| Go | `go/SKILL.md` |
| Java | `java/SKILL.md` |
| Kotlin + Compose (Android) | `kotlin/SKILL.md` |
| PHP | `php/SKILL.md` |
| Python | `python/SKILL.md` |
| Ruby | `ruby/SKILL.md` |
| Rust | `rust/SKILL.md` |
| Scala | `scala/SKILL.md` |
| Swift (iOS/macOS) | `swift/SKILL.md` |
| TypeScript / JavaScript | `typescript/SKILL.md` |

Read the relevant file at Step 2. If the language isn't listed, apply the universal checklist above and use the language's own idioms for loops, error handling, and data transformation.

## Examples

### Example 1: Refactoring a verbose loop
```java
// Anti-pattern
List<String> names = new ArrayList<>();
for (User u : users) {
    if (u.isActive()) {
        names.add(u.getName());
    }
}
// Super-code idiomatic (Java)
List<String> names = users.stream().filter(User::isActive).map(User::getName).toList();
```

## Troubleshooting

### Problem: Code is too dense to read
**Symptoms:** Reviewer complains or logic is unreadable.
**Solution:** Revert the overly compressed section. Clarity and correctness always win over conciseness.

## Related Skills

- `@karpathy-guidelines` - For behavioral guidelines on surgical changes and simplicity.

## Limitations

- **Language Support:** Language-specific idioms require reference files.
- **Readability Tradeoffs:** Extreme compression can sometimes harm readability if not careful.

---



## Sub-Skill: unslop-review

# Unslop Review
## When to Use

Use this skill when you need rewrites code review comments so they read like a human teammate wrote them. Cuts corporate-AI throat-clearing ("I noticed...", "I was wondering if perhaps...", "It might be worth considering..."). Each comment is direct: location, the issue, a concrete fix. Use when user says...


## Purpose

Rewrite or generate PR review comments that sound like a teammate, not a politeness engine. Direct on the issue, concrete on the fix, kind on the human.

## Trigger

`/unslop-review`, `/review`, "review this PR", "code review", "humanize review", "de-slop this comment", "make this feedback sound human". Auto-trigger when reviewing pull requests.

## Format

Default shape: `L<line>: <severity prefix> <observation>. <fix>.`

Severity prefixes (optional but use them when severity matters):
- `bug:` — code is broken or will break
- `risk:` — works today, fragile tomorrow (perf, race, missing test)
- `nit:` — style, naming, dead code, "while you're here"
- `q:` — genuine question, not a hidden complaint

Multi-file: `<file>:L<line>: <severity> <observation>. <fix>.`

Range: `L88-140: ...` when the issue spans lines.

## Rules

### Drop

- Throat-clearing: "I noticed that...", "It seems like...", "It looks like to me..."
- Stacked hedging: "I was wondering if perhaps we might want to potentially..."
- Polite-padding: "I would kindly suggest...", "just a small suggestion..."
- Per-comment praise: "Nice work on this function but...", "Great pattern, however..."
- Restating the diff: "Here on line 42 you have a function called `getUser` which returns..."
- Bare opinion without a fix: "This is bad" with no suggestion

### Keep

- Exact line numbers and ranges
- Identifiers in backticks: `findUser`, `req.body.id`
- Concrete fix or concrete question
- "Why" only when the fix isn't obvious

### Tone

Human, not corporate. "This throws if X" not "It may potentially be worth considering that this could throw under certain conditions." Calibrated uncertainty is fine ("I think", "probably") — performative softening is not.

### Auto-clarity (use full prose, not one-liners)

- Security findings (CVE-class, auth, secrets)
- Architecture disagreements that need a real discussion
- Onboarding context for a new contributor
- When the answer is genuinely "this is fine"

In those cases use a short paragraph, then resume terse for the rest.

## Examples

### Bad → good

- Bad: `I would kindly suggest that we might want to potentially consider adding a null check here as it could maybe lead to issues in some scenarios.`
- Good: `L42: bug: \`findUser\` returns undefined when no match. Guard before \`user.email\` or early-return 404.`

- Bad: `Great work on this implementation! However, I think we could potentially enhance readability by considering a refactor of this function.`
- Good: `L88-140: nit: this function does validation, I/O, and mapping. Splitting them would make the happy path easier to follow. Happy to pair on a cut if helpful.`

- Bad: `I noticed that there's no retry logic here which could be problematic.`
- Good: `L23: risk: no retry on 429. Wrap the call in \`withBackoff(3)\` so we don't drop legitimate requests.`

- Bad: `This implementation leverages a robust caching strategy.`
- Good: (delete — empty praise. If the caching is genuinely interesting, explain why specifically.)

### Approval

If the change is solid and you have nothing concrete: `LGTM` on its own line. No boilerplate.

## Boundaries

- Comments only. No commits, no `git push`, no auto-approve, no linter runs.
- Output is paste-ready: one comment per line, or a clearly separated list.
- Severity must be honest. Don't downgrade a `bug` to a `nit` to soften the message.

## Limitations

- Use this skill only when the task clearly matches its upstream source and local project context.
- Verify commands, generated code, dependencies, credentials, and external service behavior before applying changes.
- Do not treat examples as a substitute for environment-specific tests, security review, or user approval for destructive or costly actions.

---


## Sub-Skill: ponytail-review

Review diffs for unnecessary complexity. One line per finding: location, what
to cut, what replaces it. The diff's best outcome is getting shorter.

## Format

`L<line>: <tag> <what>. <replacement>.`, or `<file>:L<line>: ...` for
multi-file diffs.

Tags:

- `delete:` dead code, unused flexibility, speculative feature. Replacement: nothing.
- `stdlib:` hand-rolled thing the standard library ships. Name the function.
- `native:` dependency or code doing what the platform already does. Name the feature.
- `yagni:` abstraction with one implementation, config nobody sets, layer with one caller.
- `shrink:` same logic, fewer lines. Show the shorter form.

## Examples

❌ "This EmailValidator class might be more complex than necessary, have you
considered whether all these validation rules are needed at this stage?"

✅ `L12-38: stdlib: 27-line validator class. "@" in email, 1 line, real validation is the confirmation mail.`

✅ `L4: native: moment.js imported for one format call. Intl.DateTimeFormat, 0 deps.`

✅ `repo.py:L88: yagni: AbstractRepository with one implementation. Inline it until a second one exists.`

✅ `L52-71: delete: retry wrapper around an idempotent local call. Nothing replaces it.`

✅ `L30-44: shrink: manual loop builds dict. dict(zip(keys, values)), 1 line.`

## Scoring

End with the only metric that matters: `net: -<N> lines possible.`

If there is nothing to cut, say `Lean already. Ship.` and stop.

## Boundaries

Scope: over-engineering and complexity only. Correctness bugs, security holes,
and performance are explicitly out of scope. Route them to a normal review
pass, not this one. A single smoke test or `assert`-based
self-check is the ponytail minimum, not bloat, never flag it for deletion.
Does not apply the fixes, only lists them.
"stop ponytail-review" or "normal mode": revert to verbose review style.

---
