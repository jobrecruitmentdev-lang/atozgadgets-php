---
name: advanced-seo-master-skill
description: Master skill combining Omnichannel SEO, AEO (Answer Engine Optimization), GEO, Core Web Vitals, and Production readiness. Used for making websites rank highly on Google, ChatGPT, Perplexity, and Claude while ensuring perfect technical health.
---

# ADVANCED SEO & WEB PRODUCTION MASTER SKILL



## Sub-Skill: ai-seo

# AI SEO

You are an expert in AI search optimization — the practice of making content discoverable, extractable, and citable by AI systems including Google AI Overviews, ChatGPT, Perplexity, Claude, Gemini, and Copilot. Your goal is to help users get their content cited as a source in AI-generated answers.

## When to Use
- Use when optimizing content to be cited by LLMs and AI search systems.
- Use when the user asks about AI SEO, AEO, GEO, LLM visibility, or AI citations.
- Use when traditional SEO alone is not the full question and AI-specific discoverability matters.

## Before Starting

**Check for product marketing context first:**
If `.agents/product-marketing-context.md` exists (or `.claude/product-marketing-context.md` in older setups), read it before asking questions. Use that context and only ask for information not already covered or specific to this task.

Gather this context (ask if not provided):

### 1. Current AI Visibility
- Do you know if your brand appears in AI-generated answers today?
- Have you checked ChatGPT, Perplexity, or Google AI Overviews for your key queries?
- What queries matter most to your business?

### 2. Content & Domain
- What type of content do you produce? (Blog, docs, comparisons, product pages)
- What's your domain authority / traditional SEO strength?
- Do you have existing structured data (schema markup)?

### 3. Goals
- Get cited as a source in AI answers?
- Appear in Google AI Overviews for specific queries?
- Compete with specific brands already getting cited?
- Optimize existing content or create new AI-optimized content?

### 4. Competitive Landscape
- Who are your top competitors in AI search results?
- Are they being cited where you're not?

---

## How AI Search Works

### The AI Search Landscape

| Platform | How It Works | Source Selection |
|----------|-------------|----------------|
| **Google AI Overviews** | Summarizes top-ranking pages | Strong correlation with traditional rankings |
| **ChatGPT (with search)** | Searches web, cites sources | Draws from wider range, not just top-ranked |
| **Perplexity** | Always cites sources with links | Favors authoritative, recent, well-structured content |
| **Gemini** | Google's AI assistant | Pulls from Google index + Knowledge Graph |
| **Copilot** | Bing-powered AI search | Bing index + authoritative sources |
| **Claude** | Brave Search (when enabled) | Training data + Brave search results |

For a deep dive on how each platform selects sources and what to optimize per platform, see [references/platform-ranking-factors.md](references/platform-ranking-factors.md).

### Key Difference from Traditional SEO

Traditional SEO gets you ranked. AI SEO gets you **cited**.

In traditional search, you need to rank on page 1. In AI search, a well-structured page can get cited even if it ranks on page 2 or 3 — AI systems select sources based on content quality, structure, and relevance, not just rank position.

**Critical stats:**
- AI Overviews appear in ~45% of Google searches
- AI Overviews reduce clicks to websites by up to 58%
- Brands are 6.5x more likely to be cited via third-party sources than their own domains
- Optimized content gets cited 3x more often than non-optimized
- Statistics and citations boost visibility by 40%+ across queries

---

## AI Visibility Audit

Before optimizing, assess your current AI search presence.

### Step 1: Check AI Answers for Your Key Queries

Test 10-20 of your most important queries across platforms:

| Query | Google AI Overview | ChatGPT | Perplexity | You Cited? | Competitors Cited? |
|-------|:-----------------:|:-------:|:----------:|:----------:|:-----------------:|
| [query 1] | Yes/No | Yes/No | Yes/No | Yes/No | [who] |
| [query 2] | Yes/No | Yes/No | Yes/No | Yes/No | [who] |

**Query types to test:**
- "What is [your product category]?"
- "Best [product category] for [use case]"
- "[Your brand] vs [competitor]"
- "How to [problem your product solves]"
- "[Your product category] pricing"

### Step 2: Analyze Citation Patterns

When your competitors get cited and you don't, examine:
- **Content structure** — Is their content more extractable?
- **Authority signals** — Do they have more citations, stats, expert quotes?
- **Freshness** — Is their content more recently updated?
- **Schema markup** — Do they have structured data you're missing?
- **Third-party presence** — Are they cited via Wikipedia, Reddit, review sites?

### Step 3: Content Extractability Check

For each priority page, verify:

| Check | Pass/Fail |
|-------|-----------|
| Clear definition in first paragraph? | |
| Self-contained answer blocks (work without surrounding context)? | |
| Statistics with sources cited? | |
| Comparison tables for "[X] vs [Y]" queries? | |
| FAQ section with natural-language questions? | |
| Schema markup (FAQ, HowTo, Article, Product)? | |
| Expert attribution (author name, credentials)? | |
| Recently updated (within 6 months)? | |
| Heading structure matches query patterns? | |
| AI bots allowed in robots.txt? | |

### Step 4: AI Bot Access Check

Verify your robots.txt allows AI crawlers. Each AI platform has its own bot, and blocking it means that platform can't cite you:

- **GPTBot** and **ChatGPT-User** — OpenAI (ChatGPT)
- **PerplexityBot** — Perplexity
- **ClaudeBot** and **anthropic-ai** — Anthropic (Claude)
- **Google-Extended** — Google Gemini and AI Overviews
- **Bingbot** — Microsoft Copilot (via Bing)

Check your robots.txt for `Disallow` rules targeting any of these. If you find them blocked, you have a business decision to make: blocking prevents AI training on your content but also prevents citation. One middle ground is blocking training-only crawlers (like **CCBot** from Common Crawl) while allowing the search bots listed above.

See [references/platform-ranking-factors.md](references/platform-ranking-factors.md) for the full robots.txt configuration.

---

## Optimization Strategy

### The Three Pillars

```
1. Structure (make it extractable)
2. Authority (make it citable)
3. Presence (be where AI looks)
```

### Pillar 1: Structure — Make Content Extractable

AI systems extract passages, not pages. Every key claim should work as a standalone statement.

**Content block patterns:**
- **Definition blocks** for "What is X?" queries
- **Step-by-step blocks** for "How to X" queries
- **Comparison tables** for "X vs Y" queries
- **Pros/cons blocks** for evaluation queries
- **FAQ blocks** for common questions
- **Statistic blocks** with cited sources

For detailed templates for each block type, see [references/content-patterns.md](references/content-patterns.md).

**Structural rules:**
- Lead every section with a direct answer (don't bury it)
- Keep key answer passages to 40-60 words (optimal for snippet extraction)
- Use H2/H3 headings that match how people phrase queries
- Tables beat prose for comparison content
- Numbered lists beat paragraphs for process content
- Each paragraph should convey one clear idea

### Pillar 2: Authority — Make Content Citable

AI systems prefer sources they can trust. Build citation-worthiness.

**The Princeton GEO research** (KDD 2024, studied across Perplexity.ai) ranked 9 optimization methods:

| Method | Visibility Boost | How to Apply |
|--------|:---------------:|--------------|
| **Cite sources** | +40% | Add authoritative references with links |
| **Add statistics** | +37% | Include specific numbers with sources |
| **Add quotations** | +30% | Expert quotes with name and title |
| **Authoritative tone** | +25% | Write with demonstrated expertise |
| **Improve clarity** | +20% | Simplify complex concepts |
| **Technical terms** | +18% | Use domain-specific terminology |
| **Unique vocabulary** | +15% | Increase word diversity |
| **Fluency optimization** | +15-30% | Improve readability and flow |
| ~~Keyword stuffing~~ | **-10%** | **Actively hurts AI visibility** |

**Best combination:** Fluency + Statistics = maximum boost. Low-ranking sites benefit even more — up to 115% visibility increase with citations.

**Statistics and data** (+37-40% citation boost)
- Include specific numbers with sources
- Cite original research, not summaries of research
- Add dates to all statistics
- Original data beats aggregated data

**Expert attribution** (+25-30% citation boost)
- Named authors with credentials
- Expert quotes with titles and organizations
- "According to [Source]" framing for claims
- Author bios with relevant expertise

**Freshness signals**
- "Last updated: [date]" prominently displayed
- Regular content refreshes (quarterly minimum for competitive topics)
- Current year references and recent statistics
- Remove or update outdated information

**E-E-A-T alignment**
- First-hand experience demonstrated
- Specific, detailed information (not generic)
- Transparent sourcing and methodology
- Clear author expertise for the topic

### Pillar 3: Presence — Be Where AI Looks

AI systems don't just cite your website — they cite where you appear.

**Third-party sources matter more than your own site:**
- Wikipedia mentions (7.8% of all ChatGPT citations)
- Reddit discussions (1.8% of ChatGPT citations)
- Industry publications and guest posts
- Review sites (G2, Capterra, TrustRadius for B2B SaaS)
- YouTube (frequently cited by Google AI Overviews)
- Quora answers

**Actions:**
- Ensure your Wikipedia page is accurate and current
- Participate authentically in Reddit communities
- Get featured in industry roundups and comparison articles
- Maintain updated profiles on relevant review platforms
- Create YouTube content for key how-to queries
- Answer relevant Quora questions with depth

### Schema Markup for AI

Structured data helps AI systems understand your content. Key schemas:

| Content Type | Schema | Why It Helps |
|-------------|--------|-------------|
| Articles/Blog posts | `Article`, `BlogPosting` | Author, date, topic identification |
| How-to content | `HowTo` | Step extraction for process queries |
| FAQs | `FAQPage` | Direct Q&A extraction |
| Products | `Product` | Pricing, features, reviews |
| Comparisons | `ItemList` | Structured comparison data |
| Reviews | `Review`, `AggregateRating` | Trust signals |
| Organization | `Organization` | Entity recognition |

Content with proper schema shows 30-40% higher AI visibility. For implementation, use the **schema-markup** skill.

---

## Content Types That Get Cited Most

Not all content is equally citable. Prioritize these formats:

| Content Type | Citation Share | Why AI Cites It |
|-------------|:------------:|----------------|
| **Comparison articles** | ~33% | Structured, balanced, high-intent |
| **Definitive guides** | ~15% | Comprehensive, authoritative |
| **Original research/data** | ~12% | Unique, citable statistics |
| **Best-of/listicles** | ~10% | Clear structure, entity-rich |
| **Product pages** | ~10% | Specific details AI can extract |
| **How-to guides** | ~8% | Step-by-step structure |
| **Opinion/analysis** | ~10% | Expert perspective, quotable |

**Underperformers for AI citation:**
- Generic blog posts without structure
- Thin product pages with marketing fluff
- Gated content (AI can't access it)
- Content without dates or author attribution
- PDF-only content (harder for AI to parse)

---

## Monitoring AI Visibility

### What to Track

| Metric | What It Measures | How to Check |
|--------|-----------------|-------------|
| AI Overview presence | Do AI Overviews appear for your queries? | Manual check or Semrush/Ahrefs |
| Brand citation rate | How often you're cited in AI answers | AI visibility tools (see below) |
| Share of AI voice | Your citations vs. competitors | Peec AI, Otterly, ZipTie |
| Citation sentiment | How AI describes your brand | Manual review + monitoring tools |
| Source attribution | Which of your pages get cited | Track referral traffic from AI sources |

### AI Visibility Monitoring Tools

| Tool | Coverage | Best For |
|------|----------|----------|
| **Otterly AI** | ChatGPT, Perplexity, Google AI Overviews | Share of AI voice tracking |
| **Peec AI** | ChatGPT, Gemini, Perplexity, Claude, Copilot+ | Multi-platform monitoring at scale |
| **ZipTie** | Google AI Overviews, ChatGPT, Perplexity | Brand mention + sentiment tracking |
| **LLMrefs** | ChatGPT, Perplexity, AI Overviews, Gemini | SEO keyword → AI visibility mapping |

### DIY Monitoring (No Tools)

Monthly manual check:
1. Pick your top 20 queries
2. Run each through ChatGPT, Perplexity, and Google
3. Record: Are you cited? Who is? What page?
4. Log in a spreadsheet, track month-over-month

---

## AI SEO for Different Content Types

### SaaS Product Pages

**Goal:** Get cited in "What is [category]?" and "Best [category]" queries.

**Optimize:**
- Clear product description in first paragraph (what it does, who it's for)
- Feature comparison tables (you vs. category, not just competitors)
- Specific metrics ("processes 10,000 transactions/sec" not "blazing fast")
- Customer count or social proof with numbers
- Pricing transparency (AI cites pages with visible pricing)
- FAQ section addressing common buyer questions

### Blog Content

**Goal:** Get cited as an authoritative source on topics in your space.

**Optimize:**
- One clear target query per post (match heading to query)
- Definition in first paragraph for "What is" queries
- Original data, research, or expert quotes
- "Last updated" date visible
- Author bio with relevant credentials
- Internal links to related product/feature pages

### Comparison/Alternative Pages

**Goal:** Get cited in "[X] vs [Y]" and "Best [X] alternatives" queries.

**Optimize:**
- Structured comparison tables (not just prose)
- Fair and balanced (AI penalizes obviously biased comparisons)
- Specific criteria with ratings or scores
- Updated pricing and feature data
- Cite the competitor-alternatives skill for building these pages

### Documentation / Help Content

**Goal:** Get cited in "How to [X] with [your product]" queries.

**Optimize:**
- Step-by-step format with numbered lists
- Code examples where relevant
- HowTo schema markup
- Screenshots with descriptive alt text
- Clear prerequisites and expected outcomes

---

## Common Mistakes

- **Ignoring AI search entirely** — ~45% of Google searches now show AI Overviews, and ChatGPT/Perplexity are growing fast
- **Treating AI SEO as separate from SEO** — Good traditional SEO is the foundation; AI SEO adds structure and authority on top
- **Writing for AI, not humans** — If content reads like it was written to game an algorithm, it won't get cited or convert
- **No freshness signals** — Undated content loses to dated content because AI systems weight recency heavily. Show when content was last updated
- **Gating all content** — AI can't access gated content. Keep your most authoritative content open
- **Ignoring third-party presence** — You may get more AI citations from a Wikipedia mention than from your own blog
- **No structured data** — Schema markup gives AI systems structured context about your content
- **Keyword stuffing** — Unlike traditional SEO where it's just ineffective, keyword stuffing actively reduces AI visibility by 10% (Princeton GEO study)
- **Blocking AI bots** — If GPTBot, PerplexityBot, or ClaudeBot are blocked in robots.txt, those platforms can't cite you
- **Generic content without data** — "We're the best" won't get cited. "Our customers see 3x improvement in [metric]" will
- **Forgetting to monitor** — You can't improve what you don't measure. Check AI visibility monthly at minimum

---

## Tool Integrations

For implementation, use the SEO and monitoring tools available in the current environment.

| Tool | Use For |
|------|---------|
| `semrush` | AI Overview tracking, keyword research, content gap analysis |
| `ahrefs` | Backlink analysis, content explorer, AI Overview data |
| `gsc` | Search Console performance data, query tracking |
| `ga4` | Referral traffic from AI sources |

---

## Task-Specific Questions

1. What are your top 10-20 most important queries?
2. Have you checked if AI answers exist for those queries today?
3. Do you have structured data (schema markup) on your site?
4. What content types do you publish? (Blog, docs, comparisons, etc.)
5. Are competitors being cited by AI where you're not?
6. Do you have a Wikipedia page or presence on review sites?

---

## Related Skills

- **seo-audit**: For traditional technical and on-page SEO audits
- **schema-markup**: For implementing structured data that helps AI understand your content
- **content-strategy**: For planning what content to create
- **competitor-alternatives**: For building comparison pages that get cited
- **programmatic-seo**: For building SEO pages at scale
- **copywriting**: For writing content that's both human-readable and AI-extractable

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: seo-aeo-blog-writer

# SEO-AEO Blog Writer

## Overview

Writes structured long-form blog posts (800–3000 words) that satisfy both SEO ranking signals and AEO citation requirements. Every post includes a TL;DR direct-answer block, a definition sentence, structured H2/H3 hierarchy, a comparison table where relevant, and exactly 5 FAQ entries written for AI extraction.

Part of the [SEO-AEO Engine](https://github.com/mrprewsh/seo-aeo-engine).

## When to Use This Skill

- Use when writing a cluster article from a content cluster map
- Use when creating a long-form guide to build topical authority
- Use when you need content that can be cited by AI engines like Perplexity or ChatGPT
- Use when you need a blog post that follows a consistent, auditable structure

## How It Works

### Step 1: Write the TL;DR Block First
Write a 2–3 sentence direct answer to the article's core question. Place it immediately after the H1 in a blockquote. This is the first block AI engines attempt to extract.

### Step 2: Build the Heading Skeleton
Set H1, H2s (4–6), and H3s before writing any body content. The first H2 must be a "What Is" section with a clean definition sentence as its opening line.

### Step 3: Write Body Sections
Follow the section order: What Is → Why It Matters → How It Works (with H3 sub-concepts) → Practical Steps → Common Mistakes → FAQ → Conclusion.

### Step 4: Write 5 FAQ Entries
Use long-tail and secondary keywords as questions. Each answer must be under 50 words and self-contained — readable without any surrounding context.

### Step 5: Run AEO and SEO Checklists
Verify TL;DR presence, definition sentence, FAQ count, keyword placement, and heading structure before outputting.

## Examples

### Example: TL;DR Block
How to Manage a Remote Engineering Team

TL;DR: Managing a remote engineering team requires async
communication tools, clear documentation standards, and
timezone-aware sprint planning. Teams that nail these three
areas ship consistently regardless of where members are located.


### Example: FAQ Section
Q: What is the biggest challenge of remote engineering teams?
A: Async communication. Without shared hours, decisions slow down
and context gets lost. Teams that document decisions in writing
and use structured standup tools close this gap fastest.
Q: How do you run a daily standup with a remote team?
A: Use async video or text standups posted at the start of each
member's day. Tools like Loom or Slack threads work well.
Avoid live calls across more than 2 timezones.

## Best Practices

- ✅ **Do:** Write the TL;DR block before writing anything else — it anchors the article
- ✅ **Do:** Make the "What Is" definition sentence extractable on its own — one clean sentence
- ✅ **Do:** Use secondary keywords as FAQ questions to capture long-tail traffic
- ❌ **Don't:** Write FAQ answers longer than 50 words — AI engines skip long answers
- ❌ **Don't:** Use duplicate H2 headings anywhere in the article
- ❌ **Don't:** Skip the comparison table if the topic involves comparing options

## Common Pitfalls

- **Problem:** TL;DR block is too vague to be extracted as a direct answer
  **Solution:** The TL;DR must answer the article's core question in 2–3 sentences. If it doesn't answer a specific question, rewrite it.

- **Problem:** FAQ answers reference "as mentioned above" or other context
  **Solution:** Every FAQ answer must stand completely alone — no references to other parts of the article.

## Related Skills

- `@seo-aeo-content-cluster` — provides the topic and keyword for this article
- `@seo-aeo-content-quality-auditor` — audits the completed post for SEO and AEO signals
- `@seo-aeo-internal-linking` — maps links between this post and related pages

## Additional Resources

- [SEO-AEO Engine Repository](https://github.com/mrprewsh/seo-aeo-engine)
- [Full Blog Writer SKILL.md](https://github.com/mrprewsh/seo-aeo-engine/blob/main/.agent/skills/blog-writer/SKILL.md)

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: seo-aeo-content-quality-auditor

# SEO-AEO Content Quality Auditor

## Overview

Runs a dual SEO + AEO audit on any landing page or blog post. Produces an overall score, SEO score, AEO score, and readability score — each out of 100 — with severity-ranked issue lists (Critical / Warning / Polish), exact fix instructions for every issue, and projected scores after all fixes are applied.

Part of the [SEO-AEO Engine](https://github.com/mrprewsh/seo-aeo-engine).

## When to Use This Skill

- Use when auditing a landing page or blog post before publishing
- Use after the blog-writer or landing-page-writer skill outputs content
- Use when diagnosing why existing content is underperforming in search
- Use when you need a scored, actionable SEO and AEO report

## How It Works

### Step 1: Run SEO Checks
Verify keyword density, H1/H2/H3 structure, meta elements, word count, sentence length, and paragraph density. Flag every issue with its severity.

### Step 2: Run AEO Checks
Check for TL;DR block, definition sentence, FAQ section (minimum 4 entries), bullet and numbered lists, comparison table, and extractable direct answers. Score each signal as found or missing.

### Step 3: Run Readability Checks
Check passive voice ratio, transition word presence, wall-of-text paragraphs, subheading frequency, and reading level.

### Step 4: Score and Prioritise
Calculate three scores out of 100. Sort all issues into Critical (fix before publishing), Important (fix soon), and Polish (optional improvements). Generate projected scores after all fixes are applied.

## Scoring System

| Score | Status | Label |
|-------|--------|-------|
| 85–100 | ✅ Pass | Strong |
| 70–84 | ⚠️ Warn | Acceptable |
| 50–69 | 🔶 Weak | Needs work |
| 0–49 | ❌ Fail | Do not publish |

## Examples

### Example: Audit Summary
Overall Score:    84/100  ⚠️ Acceptable
SEO Score:        88/100  ✅ Pass
AEO Score:        74/100  ⚠️ Acceptable
Readability:      91/100  ✅ Pass
Verdict: Strong SEO foundation. AEO needs a TL;DR block
and one more FAQ entry before publishing.
🔴 Critical (fix before publishing):

AEO: No TL;DR block found
Fix: Add a 2–3 sentence direct-answer block in a
blockquote immediately after the H1.

🟡 Important (fix soon):
2. AEO: FAQ has 3 entries — minimum is 4
Fix: Add one more FAQ entry using a secondary keyword
as the question.
Projected score after fixes: 93/100 ✅

## Best Practices

- ✅ **Do:** Fix all Critical issues before publishing — they block AEO extraction
- ✅ **Do:** Use the projected score to prioritise which fixes to make first
- ✅ **Do:** Run the audit on both the landing page and blog post in the same session
- ❌ **Don't:** Publish content scoring below 50/100 overall
- ❌ **Don't:** Ignore AEO warnings — they directly affect AI engine citation probability

## Common Pitfalls

- **Problem:** SEO score is high but AEO score is low
  **Solution:** Traditional SEO tools miss AEO signals entirely. Run the AEO checklist separately and treat it as equally important.

- **Problem:** Fix list is long and overwhelming
  **Solution:** Work through Critical issues only first, re-run the audit, then tackle Important issues.

## Related Skills

- `@seo-aeo-blog-writer` — produces the content this skill audits
- `@seo-aeo-landing-page-writer` — produces landing pages this skill audits
- `@seo-aeo-schema-generator` — uses audit output to determine schema priorities

## Additional Resources

- [SEO-AEO Engine Repository](https://github.com/mrprewsh/seo-aeo-engine)
- [Full Content Quality Auditor SKILL.md](https://github.com/mrprewsh/seo-aeo-engine/blob/main/.agent/skills/content-quality-auditor/SKILL.md)

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: seo-aeo-keyword-research

# SEO-AEO Keyword Research

## Overview

Identifies high-value SEO keywords and AEO question-based queries for a topic. Produces keyword tiers (easy wins to long-term goals), search intent classification, cannibalization checks, and a content production map — all from a single topic input.

Part of the [SEO-AEO Engine](https://github.com/mrprewsh/seo-aeo-engine) — an open-source AI-powered content growth system.

## When to Use This Skill

- Use when you need to build a keyword strategy for a new topic or niche
- Use when you want to find AEO question queries for AI engine citation
- Use when you need to prioritise which keywords to target first
- Use when you want to check for keyword cannibalization before writing content

## How It Works

### Step 1: Extract Seed Keywords
Identify 3–5 core terms that anchor the topic's search territory. Go beyond the obvious head term to include adjacent terms the audience actually uses.

### Step 2: Expand Into Tiers
Sort all keywords into three tiers:
- **Tier 1** — Low-to-moderate difficulty. Target first.
- **Tier 2** — Medium difficulty. Build toward after Tier 1 content is live.
- **Tier 3** — High difficulty. Long-term goals only.

### Step 3: Generate AEO Keywords
Produce question-based keywords that AI engines surface in direct answers and People Also Ask boxes. For each AEO keyword, specify the answer format to use (definition sentence, numbered steps, comparison table, direct number).

### Step 4: Run Cannibalization Check
Flag any two keywords similar enough to split traffic if targeted on separate pages. Recommend which page should own which term.

### Step 5: Build Content Map
Recommend content type and production order for all Tier 1 and Tier 2 keywords.

## Examples

### Example 1: SaaS Product
Input: topic = "remote project management software"
audience = "engineering managers and startup founders"
goal = "convert"
Output:
Tier 1 Keywords:

"remote project management software" | Medium volume | Difficulty: 38
"project management tool remote teams" | Low volume | Difficulty: 29

AEO Keywords:

"What is the best project management software for remote teams?"
→ Answer format: Comparison table
"How does remote project management work?"
→ Answer format: Numbered steps

Content Map:

Landing page → "remote project management software"
Pillar blog → "complete guide to remote project management"
Cluster article → "how to manage remote engineering teams"


### Example 2: Fintech App
Input: topic = "automated budgeting app"
audience = "millennials managing personal finances"
goal = "all"
Output:
Tier 1 Keywords:

"automated budgeting app" | Medium volume | Difficulty: 33
"automatic savings app" | Low volume | Difficulty: 24

AEO Keywords:

"What is the best budgeting app for millennials?"
→ Answer format: Comparison table
"How does automated budgeting work?"
→ Answer format: Numbered steps


## Best Practices

- ✅ **Do:** Target Tier 1 keywords first — build authority before going after competitive terms
- ✅ **Do:** Use AEO keywords in FAQ sections and definition blocks for AI engine citation
- ✅ **Do:** Validate estimated volume and difficulty with a live tool (Ahrefs, SEMrush) before committing
- ❌ **Don't:** Target two keywords on the same page if cannibalization is flagged
- ❌ **Don't:** Use volume as the only prioritisation signal — difficulty and intent matter more

## Common Pitfalls

- **Problem:** High-volume keyword chosen but impossible to rank for early on
  **Solution:** Always cross-check volume with difficulty. Tier 1 should have difficulty under 45.

- **Problem:** AEO keywords ignored in favour of traditional search terms
  **Solution:** AEO keywords drive AI engine citation — include at least 5 in every research run.

## Related Skills

- `@seo-aeo-content-cluster` — uses keyword research output to build topic cluster
- `@seo-aeo-landing-page-writer` — consumes primary keyword to generate landing page
- `@seo-aeo-blog-writer` — uses secondary keywords for cluster article targeting

## Additional Resources

- [SEO-AEO Engine Repository](https://github.com/mrprewsh/seo-aeo-engine)
- [Full Keyword Research SKILL.md](https://github.com/mrprewsh/seo-aeo-engine/blob/main/.agent/skills/keyword-research/SKILL.md)

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: geo-fundamentals

# GEO Fundamentals

> Optimization for AI-powered search engines.

---

## 1. What is GEO?

**GEO** = Generative Engine Optimization

| Goal | Platform |
|------|----------|
| Be cited in AI responses | ChatGPT, Claude, Perplexity, Gemini |

### SEO vs GEO

| Aspect | SEO | GEO |
|--------|-----|-----|
| Goal | #1 ranking | AI citations |
| Platform | Google | AI engines |
| Metrics | Rankings, CTR | Citation rate |
| Focus | Keywords | Entities, data |

---

## 2. AI Engine Landscape

| Engine | Citation Style | Opportunity |
|--------|----------------|-------------|
| **Perplexity** | Numbered [1][2] | Highest citation rate |
| **ChatGPT** | Inline/footnotes | Custom GPTs |
| **Claude** | Contextual | Long-form content |
| **Gemini** | Sources section | SEO crossover |

---

## 3. RAG Retrieval Factors

How AI engines select content to cite:

| Factor | Weight |
|--------|--------|
| Semantic relevance | ~40% |
| Keyword match | ~20% |
| Authority signals | ~15% |
| Freshness | ~10% |
| Source diversity | ~15% |

---

## 4. Content That Gets Cited

| Element | Why It Works |
|---------|--------------|
| **Original statistics** | Unique, citable data |
| **Expert quotes** | Authority transfer |
| **Clear definitions** | Easy to extract |
| **Step-by-step guides** | Actionable value |
| **Comparison tables** | Structured info |
| **FAQ sections** | Direct answers |

---

## 5. GEO Content Checklist

### Content Elements

- [ ] Question-based titles
- [ ] Summary/TL;DR at top
- [ ] Original data with sources
- [ ] Expert quotes (name, title)
- [ ] FAQ section (3-5 Q&A)
- [ ] Clear definitions
- [ ] "Last updated" timestamp
- [ ] Author with credentials

### Technical Elements

- [ ] Article schema with dates
- [ ] Person schema for author
- [ ] FAQPage schema
- [ ] Fast loading (< 2.5s)
- [ ] Clean HTML structure

---

## 6. Entity Building

| Action | Purpose |
|--------|---------|
| Google Knowledge Panel | Entity recognition |
| Wikipedia (if notable) | Authority source |
| Consistent info across web | Entity consolidation |
| Industry mentions | Authority signals |

---

## 7. AI Crawler Access

### Key AI User-Agents

| Crawler | Engine |
|---------|--------|
| GPTBot | ChatGPT/OpenAI |
| Claude-Web | Claude |
| PerplexityBot | Perplexity |
| Googlebot | Gemini (shared) |

### Access Decision

| Strategy | When |
|----------|------|
| Allow all | Want AI citations |
| Block GPTBot | Don't want OpenAI training |
| Selective | Allow some, block others |

---

## 8. Measurement

| Metric | How to Track |
|--------|--------------|
| AI citations | Manual monitoring |
| "According to [Brand]" mentions | Search in AI |
| Competitor citations | Compare share |
| AI-referred traffic | UTM parameters |

---

## 9. Anti-Patterns

| ❌ Don't | ✅ Do |
|----------|-------|
| Publish without dates | Add timestamps |
| Vague attributions | Name sources |
| Skip author info | Show credentials |
| Thin content | Comprehensive coverage |

---

> **Remember:** AI cites content that's clear, authoritative, and easy to extract. Be the best answer.

---

## Script

| Script | Purpose | Command |
|--------|---------|---------|
| `scripts/geo_checker.py` | GEO audit (AI citation readiness) | `python scripts/geo_checker.py <project_path>` |

## When to Use
This skill is applicable to execute the workflow or actions described in the overview.

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: seo

# SEO: Universal SEO Analysis Skill

Comprehensive SEO analysis across all industries (SaaS, local services,
e-commerce, publishers, agencies). Orchestrates 12 specialized sub-skills and 7 subagents
(+ optional extension sub-skills like seo-dataforseo).

## When to Use
- Use when the user asks for a full SEO audit or broad SEO strategy.
- Use as the umbrella entry point when multiple SEO dimensions are in scope.
- Use when the task spans technical SEO, content, schema, sitemaps, and AI search readiness together.

## Quick Reference

| Command | What it does |
|---------|-------------|
| `/seo audit <url>` | Full website audit with parallel subagent delegation |
| `/seo page <url>` | Deep single-page analysis |
| `/seo sitemap <url or generate>` | Analyze or generate XML sitemaps |
| `/seo schema <url>` | Detect, validate, and generate Schema.org markup |
| `/seo images <url>` | Image optimization analysis |
| `/seo technical <url>` | Technical SEO audit (9 categories) |
| `/seo content <url>` | E-E-A-T and content quality analysis |
| `/seo geo <url>` | AI Overviews / Generative Engine Optimization |
| `/seo plan <business-type>` | Strategic SEO planning |
| `/seo programmatic [url\|plan]` | Programmatic SEO analysis and planning |
| `/seo competitor-pages [url\|generate]` | Competitor comparison page generation |
| `/seo hreflang [url]` | Hreflang/i18n SEO audit and generation |
| `/seo dataforseo [command]` | Live SEO data via DataForSEO (extension) |
| `/seo image-gen [use-case] <description>` | AI image generation for SEO assets (extension) |

## Orchestration Logic

When the user invokes `/seo audit`, delegate to subagents in parallel:
1. Detect business type (SaaS, local, ecommerce, publisher, agency, other)
2. Spawn subagents: seo-technical, seo-content, seo-schema, seo-sitemap, seo-performance, seo-visual, seo-geo
3. Collect results and generate unified report with SEO Health Score (0-100)
4. Create prioritized action plan (Critical -> High -> Medium -> Low)

For individual commands, load the relevant sub-skill directly.

## Industry Detection

Detect business type from homepage signals:
- **SaaS**: pricing page, /features, /integrations, /docs, "free trial", "sign up"
- **Local Service**: phone number, address, service area, "serving [city]", Google Maps embed
- **E-commerce**: /products, /collections, /cart, "add to cart", product schema
- **Publisher**: /blog, /articles, /topics, article schema, author pages, publication dates
- **Agency**: /case-studies, /portfolio, /industries, "our work", client logos

## Quality Gates

Read `references/quality-gates.md` for thin content thresholds per page type.
Hard rules:
- WARNING at 30+ location pages (enforce 60%+ unique content)
- HARD STOP at 50+ location pages (require user justification)
- Never recommend HowTo schema (deprecated Sept 2023)
- FAQ schema for Google rich results: only government and healthcare sites (Aug 2023 restriction); existing FAQPage on commercial sites -> flag Info priority (not Critical), noting AI/LLM citation benefit; adding new FAQPage -> not recommended for Google benefit
- All Core Web Vitals references use INP, never FID

## Reference Files

Load these on-demand as needed (do NOT load all at startup):
- `references/cwv-thresholds.md`: Current Core Web Vitals thresholds and measurement details
- `references/schema-types.md`: All supported schema types with deprecation status
- `references/eeat-framework.md`: E-E-A-T evaluation criteria (Sept 2025 QRG update)
- `references/quality-gates.md`: Content length minimums, uniqueness thresholds

## Scoring Methodology

### SEO Health Score (0-100)
Weighted aggregate of all categories:

| Category | Weight |
|----------|--------|
| Technical SEO | 22% |
| Content Quality | 23% |
| On-Page SEO | 20% |
| Schema / Structured Data | 10% |
| Performance (CWV) | 10% |
| AI Search Readiness | 10% |
| Images | 5% |

### Priority Levels
- **Critical**: Blocks indexing or causes penalties (immediate fix required)
- **High**: Significantly impacts rankings (fix within 1 week)
- **Medium**: Optimization opportunity (fix within 1 month)
- **Low**: Nice to have (backlog)

## Sub-Skills

This skill orchestrates 12 specialized sub-skills (+ 2 extensions):

1. **seo-audit** -- Full website audit with parallel delegation
2. **seo-page** -- Deep single-page analysis
3. **seo-technical** -- Technical SEO (9 categories)
4. **seo-content** -- E-E-A-T and content quality
5. **seo-schema** -- Schema markup detection and generation
6. **seo-images** -- Image optimization
7. **seo-sitemap** -- Sitemap analysis and generation
8. **seo-geo** -- AI Overviews / GEO optimization
9. **seo-plan** -- Strategic planning with templates
10. **seo-programmatic** -- Programmatic SEO analysis and planning
11. **seo-competitor-pages** -- Competitor comparison page generation
12. **seo-hreflang** -- Hreflang/i18n SEO audit and generation
13. **seo-dataforseo** -- Live SEO data via DataForSEO MCP (extension)
14. **seo-image-gen** -- AI image generation for SEO assets via Gemini (extension)

## Subagents

For parallel analysis during audits:
- `seo-technical` -- Crawlability, indexability, security, CWV
- `seo-content` -- E-E-A-T, readability, thin content
- `seo-schema` -- Detection, validation, generation
- `seo-sitemap` -- Structure, coverage, quality gates
- `seo-performance` -- Core Web Vitals measurement
- `seo-visual` -- Screenshots, mobile testing, above-fold
- `seo-geo` -- AI crawler access, llms.txt, citability, brand mention signals
- `seo-dataforseo` -- Live SERP, keyword, backlink, local SEO data (extension, optional)
- `seo-image-gen` -- SEO image audit and generation plan (extension, optional)

## Error Handling

| Scenario | Action |
|----------|--------|
| Unrecognized command | List available commands from the Quick Reference table. Suggest the closest matching command. |
| URL unreachable | Report the error and suggest the user verify the URL. Do not attempt to guess site content. |
| Sub-skill fails during audit | Report partial results from successful sub-skills. Clearly note which sub-skill failed and why. Suggest re-running the failed sub-skill individually. |
| Ambiguous business type detection | Present the top two detected types with supporting signals. Ask the user to confirm before proceeding with industry-specific recommendations. |

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: fixing-metadata

## When to Use

Use this skill when you need audit and fix HTML metadata including page titles, meta descriptions, canonical URLs, Open Graph tags, Twitter cards, favicons, JSON-LD structured data, and robots directives. Use when adding SEO metadata, fixing social share previews, reviewing Open Graph tags, setting up canonical...

## Workflow

1. Identify pages with missing or incorrect metadata (titles, descriptions, canonical, OG tags)
2. Audit against the priority rules below — fix critical issues (duplicates, indexing) first
3. Ensure title, description, canonical, and og:url all agree with each other
4. Verify social cards render correctly on a real URL, not localhost
5. Keep diffs minimal and scoped to metadata only — do not refactor unrelated code
## when to apply

Reference these guidelines when:
- adding or changing page titles, descriptions, canonical, robots
- implementing Open Graph or Twitter card metadata
- setting favicons, app icons, manifest, theme-color
- building shared SEO components or layout metadata defaults
- adding structured data (JSON-LD)
- changing locale, alternate languages, or canonical routing
- shipping new pages, marketing pages, or shareable links

## rule categories by priority

| priority | category | impact |
|----------|----------|--------|
| 1 | correctness and duplication | critical |
| 2 | title and description | high |
| 3 | canonical and indexing | high |
| 4 | social cards | high |
| 5 | icons and manifest | medium |
| 6 | structured data | medium |
| 7 | locale and alternates | low-medium |
| 8 | tool boundaries | critical |

## quick reference

### 1. correctness and duplication (critical)

- define metadata in one place per page, avoid competing systems
- do not emit duplicate title, description, canonical, or robots tags
- metadata must be deterministic, no random or unstable values
- escape and sanitize any user-generated or dynamic strings
- every page must have safe defaults for title and description

### 2. title and description (high)

- every page must have a title
- use a consistent title format across the site
- keep titles short and readable, avoid stuffing
- shareable or searchable pages should have a meta description
- descriptions must be plain text, no markdown or quote spam

### 3. canonical and indexing (high)

- canonical must point to the preferred URL for the page
- use noindex only for private, duplicate, or non-public pages
- robots meta must match actual access intent
- previews or staging pages should be noindex by default when possible
- paginated pages must have correct canonical behavior

### 4. social cards (high)

- shareable pages must set Open Graph title, description, and image
- Open Graph and Twitter images must use absolute URLs
- prefer correct image dimensions and stable aspect ratios
- og:url must match the canonical URL
- use a sensible og:type, usually website or article
- set twitter:card appropriately, summary_large_image by default

### 5. icons and manifest (medium)

- include at least one favicon that works across browsers
- include apple-touch-icon when relevant
- manifest must be valid and referenced when used
- set theme-color intentionally to avoid mismatched UI chrome
- icon paths should be stable and cacheable

### 6. structured data (medium)

- do not add JSON-LD unless it clearly maps to real page content
- JSON-LD must be valid and reflect what is actually rendered
- do not invent ratings, reviews, prices, or organization details
- prefer one structured data block per page unless required

### 7. locale and alternates (low-medium)

- set the html lang attribute correctly
- set og:locale when localization exists
- add hreflang alternates only when pages truly exist
- localized pages must canonicalize correctly per locale

### 8. tool boundaries (critical)

- prefer minimal changes, do not refactor unrelated code
- do not migrate frameworks or SEO libraries unless requested
- follow the project's existing metadata pattern (Next.js metadata API, react-helmet, manual head, etc.)

## review guidance

- fix critical issues first (duplicates, canonical, indexing)
- ensure title, description, canonical, and og:url agree
- verify social cards on a real URL, not localhost
- prefer stable, boring metadata over clever or dynamic
- keep diffs minimal and scoped to metadata only

## Limitations

- Use this skill only when the task clearly matches its upstream source and local project context.
- Verify commands, generated code, dependencies, credentials, and external service behavior before applying changes.
- Do not treat examples as a substitute for environment-specific tests, security review, or user approval for destructive or costly actions.

---



## Sub-Skill: developer-seo

# Developer SEO
## When to Use

Use this skill when you need sEO strategy for technical queries and developer audiences. Covers keyword research for "how to X in language" queries, error message SEO, Stack Overflow-style content, technical long-tail keywords, and competing with official documentation sites. Use when asked about: - SEO for...


## Overview

Developer SEO differs fundamentally from traditional SEO. Developers search with precise technical intent—error messages, API questions, "how to X in Y language" queries. They bounce immediately from thin content and respect sites that actually solve problems. Your competition isn't other marketing sites; it's Stack Overflow, official docs, and GitHub issues.

This skill covers SEO strategies that work for technical audiences without compromising on substance.

## Understanding Developer Search Behavior

### How Developers Search

Developers search differently than general audiences:

**Query patterns:**
- Error messages (often copy-pasted verbatim)
- "How to [action] in [language/framework]"
- "[Tool A] vs [Tool B]"
- "[Concept] tutorial"
- "[Library] [specific function] example"

**Behavioral signals:**
- High bounce rates on superficial content
- Long dwell time on genuinely helpful pages
- Multiple tabs open comparing solutions
- Quick scroll to code examples
- Immediate exit if content doesn't match query intent

### Search Intent Categories

1. **Troubleshooting**: Developer has an error, needs a fix
2. **Learning**: Developer wants to understand a concept
3. **Evaluating**: Developer comparing tools or approaches
4. **Implementing**: Developer needs working code examples
5. **Reference**: Developer needs quick syntax or API lookup

## Keyword Research for Developers

### Finding Technical Long-Tail Keywords

Technical long-tail keywords have lower volume but extremely high intent. A developer searching "axios interceptor refresh token react" knows exactly what they need.

**Research approaches:**

1. **Mine your support channels**
   - Extract questions from support tickets
   - Review Discord/Slack community questions
   - Analyze GitHub issues for common problems

2. **Stack Overflow mining**
   - Search for questions mentioning your tool category
   - Look at related questions on popular threads
   - Note the exact phrasing developers use

3. **Google Search Console analysis**
   - Find queries you rank positions 5-20 for
   - Identify question-based queries
   - Spot error message searches hitting your site

4. **Competitor content gaps**
   - What questions do competitors' docs not answer?
   - Where are forum threads unsatisfied with existing answers?

### Error Message SEO

Error messages are SEO gold—developers copy-paste them directly into search.

**Strategy:**
1. Create dedicated pages for common errors
2. Use exact error text in titles and H1s
3. Include the full error message early in content
4. Provide the actual fix, not generic troubleshooting
5. Add related errors users might also encounter

**Content structure for error pages:**
```
Title: [Exact Error Message] - How to Fix

## The Error
[Full error message and where it appears]

## Quick Fix
[The solution that works in most cases]

## Why This Happens
[Brief technical explanation]

## Other Solutions
[Alternative fixes for edge cases]

## Related Errors
[Links to similar issues]
```

### Competing with Official Documentation

Official docs have domain authority advantages but often have weaknesses:

**Where docs often fail:**
- No "why" explanations, just "what"
- Missing real-world examples
- No troubleshooting guides
- Outdated content
- No comparative context

**Your opportunities:**
- "Getting started with X" tutorials that hold your hand
- "X vs Y" comparison content (docs never compare)
- Migration guides between versions or tools
- Real-world implementation examples
- Common gotchas and how to avoid them

## Content Formats That Rank

### How-To Guides

Structure for technical how-to content:

```markdown
# How to [Action] in [Technology]

## Prerequisites
- What you need before starting
- Required versions/dependencies

## Quick Version (TL;DR)
- Code snippet that works for common case

## Step-by-Step
1. Step with explanation
2. Step with code example
3. Step with expected output

## Complete Example
[Full working code]

## Common Issues
- Problem 1: Solution
- Problem 2: Solution

## Next Steps
[What to learn next]
```

### Comparison Content

Developers actively search "[Tool A] vs [Tool B]" when evaluating options.

**Guidelines:**
- Be genuinely objective (developers will check)
- Include actual code comparisons
- Cover specific use cases where each wins
- Mention your tool's limitations honestly
- Update when tools change significantly

### Tutorial Series

In-depth tutorials build topical authority and capture multiple related queries.

**Planning approach:**
1. Identify a topic cluster (e.g., "authentication in Node.js")
2. Create pillar content covering the broad topic
3. Build supporting content for specific subtopics
4. Interlink strategically

## Technical SEO for Developer Sites

### Code Snippet Optimization

Google can read and understand code. Optimize for it:

- Use semantic HTML (`<code>`, `<pre>`)
- Add language hints for syntax highlighting
- Ensure code is actual text, not images
- Test that code actually works (broken examples hurt credibility)

### Page Speed for Developer Sites

Developers expect fast sites. They also often use ad blockers and privacy tools.

**Priorities:**
- Minimize JavaScript for documentation pages
- Ensure content loads without JS when possible
- Optimize for low-bandwidth scenarios (conference Wi-Fi)
- Test with developer-typical browser extensions enabled

### Documentation Site Architecture

Good IA helps both users and search engines:

- Clear hierarchy (Guides > Category > Specific Topic)
- Breadcrumbs for navigation
- Consistent URL structures
- Proper use of canonical tags for versioned docs
- XML sitemaps for large doc sites

## Building Authority

### Technical Backlinks

High-quality technical backlinks matter more than quantity.

**Sources that work:**
- GitHub repository READMEs
- Technical blog posts citing your content
- Stack Overflow answers linking to your guides
- Developer newsletter mentions
- Conference talk resource lists

**What doesn't work:**
- Generic guest posting
- Link exchanges
- Directory spam
- Forum signature links

### Content Freshness

Developer content becomes outdated quickly:

- Review and update major guides quarterly
- Add "last updated" dates (developers check these)
- Create processes for updating when dependencies change
- Remove or redirect genuinely obsolete content

## Measuring Developer SEO

### Metrics That Matter

- Organic traffic to documentation and guides
- Rankings for target technical queries
- Time on page for tutorial content
- Search Console impressions for error message queries
- GitHub referrals from technical content

### Metrics to Interpret Carefully

- Bounce rate (developers often find answer and leave—that's success)
- Pages per session (for reference content, one page is fine)
- Conversion rate (long attribution windows for developer tools)

## Budget and Resources

### Minimum Viable Approach
- **Time investment**: 5-10 hours/week for content creation
- **Tools needed**: Google Search Console (free), basic keyword research tool
- **Timeline**: 3-6 months to see meaningful organic growth

### Scaled Approach
- Dedicated technical content writer
- SEO tools subscription (Ahrefs, Semrush)
- Content management system optimized for docs
- Regular content audits and updates

## Tools

- **Google Search Console**: Track rankings and discover query opportunities
- **Ahrefs/Semrush**: Keyword research and competitor analysis
- **Screaming Frog**: Technical SEO audits for documentation sites
- **Algolia**: Search analytics revealing what developers look for
- **Octolens**: Monitor developer discussions to find content opportunities and questions your content should answer

## Common Mistakes

1. **Writing for search engines, not developers**: Keyword-stuffed content that doesn't actually help
2. **Ignoring search intent**: Ranking for queries but not matching what developers actually need
3. **Thin content**: Short posts that don't provide real value
4. **Outdated examples**: Code that no longer works in current versions
5. **No unique value**: Rehashing what official docs already cover

## Related Skills

- **developer-content-strategy**: Overall content planning for developer audiences
- **dev-tool-directory-listings**: Building domain authority through directory presence
- **developer-lead-gen**: Converting organic traffic into leads

## Limitations

- Use this skill only when the task clearly matches its upstream source and local project context.
- Verify commands, generated code, dependencies, credentials, and external service behavior before applying changes.
- Do not treat examples as a substitute for environment-specific tests, security review, or user approval for destructive or costly actions.

---



## Sub-Skill: frontend-lighthouse

# Frontend Lighthouse (portable performance gate)

> Portable skill — readable by Claude Code, OpenCode, Codex, Cursor, Windsurf, and others.
> This skill describes a **CI performance gate** — a Lighthouse CI config plus a workflow — not a
> component library or a visual style. It pairs with the **frontend-seo** and
> **frontend-architecture** skills: SEO writes the metadata, Lighthouse proves it ships fast.

The goal: every pull request is **blocked unless the production build meets explicit Core Web
Vitals budgets and category score floors**. Budgets live in **one** `lighthouserc.cjs`, runs are
**median-of-N** so the gate doesn't flake, and the same config runs locally and in CI.

## When to Use This Skill

- Use when adding a Lighthouse CI performance gate to a web app.
- Use when setting Core Web Vitals budgets for LCP, CLS, and TBT as the lab proxy for INP.
- Use when configuring category score floors for performance, SEO, accessibility, and best practices.
- Use when debugging flaky Lighthouse runs or making reports visible as CI artifacts.

---

## 0. The five core ideas

1. **One config, one source of truth.** All budgets and assertions live in a single `lighthouserc.cjs`. Named constants for each budget — no magic numbers buried in assertion objects.
2. **Gate the production build, never dev.** Lighthouse runs against `build` + `start` (the real, optimized output). Dev-server numbers are meaningless for a budget.
3. **Median-of-N kills flakiness.** Run 3+ times and assert on the median run, so per-run jitter (cold caches, CI noise) never red-flags a healthy build.
4. **Budgets encode Google's "good" thresholds.** LCP ≤ 2500 ms, INP ≤ 200 ms (gated via the TBT lab proxy), CLS ≤ 0.1 — the values that earn green scores, not "needs improvement".
5. **Blocking in CI, visible as artifacts.** A GitHub Action runs the gate on every PR touching the app and uploads the HTML/JSON reports so failures are debuggable.

---

## 1. Files this skill adds

```
apps/web/                          (or your app root)
├── lighthouserc.cjs               ← the gate: budgets + assertions + collect settings
├── package.json                   ← "lhci": "lhci autorun --config=./lighthouserc.cjs"
└── .github/workflows/lighthouse.yml  ← PR-blocking CI job (build → start → lhci → upload)
```

Plus a dev dependency: `@lhci/cli`.

```bash
pnpm add -D @lhci/cli        # or npm i -D / yarn add -D
```

---

## 2. The config (`lighthouserc.cjs`)

`.cjs` (CommonJS) so it loads without ESM/TS transpilation. Every budget is a **named constant**
with a comment explaining the threshold — never a bare number inside an assertion.

```js
/**
 * Lighthouse CI configuration — Core Web Vitals budgets for the marketing surface.
 *
 * Enforces Google's mobile "good" CWV thresholds:
 *   - Largest Contentful Paint (LCP) ≤ 2500 ms
 *   - Cumulative Layout Shift (CLS)  ≤ 0.1
 *   - Interaction to Next Paint (INP) ≤ 200 ms
 *
 * INP is a *field* metric with no direct lab audit, so in the lab we gate on
 * Total Blocking Time (TBT) — Lighthouse's recommended lab proxy — at the same
 * budget, and assert the experimental INP audit directly as a warning where the
 * build exposes it.
 *
 * Collection runs against the *production* server (build + start) on Lighthouse's
 * default mobile (Moto G4 / slow 4G) emulation.
 */

/** The fixed port the production server is started on for the audit. */
const PORT = 3100;
const BASE_URL = `http://localhost:${PORT}`;

/** Pages whose budgets are enforced in CI. */
const MARKETING_URLS = [`${BASE_URL}/`];

/**
 * Core Web Vitals budgets on mobile — Google's "good" thresholds.
 * These are the values that earn the best Lighthouse scores.
 */
const LCP_BUDGET_MS = 2500; // good
const INP_BUDGET_MS = 200; // good (TBT lab proxy)
const CLS_BUDGET = 0.1; // good

module.exports = {
  ci: {
    collect: {
      // Build is run separately in CI; here we only serve the production output.
      startServerCommand: `pnpm start --port ${PORT}`,
      startServerReadyPattern: "Ready in", // framework's "server ready" log line
      startServerReadyTimeout: 120000,
      url: MARKETING_URLS,
      // Median of multiple runs keeps the gate stable against per-run jitter.
      numberOfRuns: 3,
      settings: {
        // Default mobile emulation; opt into desktop via env for a second run.
        preset:
          process.env.LHCI_FORM_FACTOR === "desktop" ? "desktop" : undefined,
        // Only gate the categories we care about; skip PWA category noise.
        onlyCategories: [
          "performance",
          "seo",
          "accessibility",
          "best-practices",
        ],
      },
    },
    assert: {
      // Median across runs is the value compared against each budget.
      aggregationMethod: "median-run",
      assertions: {
        // --- Core Web Vitals budgets (the contract) ---------------------
        "largest-contentful-paint": [
          "error",
          { maxNumericValue: LCP_BUDGET_MS },
        ],
        "cumulative-layout-shift": ["error", { maxNumericValue: CLS_BUDGET }],
        "total-blocking-time": ["error", { maxNumericValue: INP_BUDGET_MS }],
        // Direct INP audit where the Lighthouse build exposes it (else ignored).
        "interaction-to-next-paint": [
          "warn",
          { maxNumericValue: INP_BUDGET_MS },
        ],

        // --- Category floors (target top Lighthouse scores) -------------
        "categories:performance": ["error", { minScore: 0.9 }],
        "categories:seo": ["error", { minScore: 0.95 }],
        "categories:accessibility": ["error", { minScore: 0.95 }],
        "categories:best-practices": ["error", { minScore: 0.9 }],
      },
    },
    upload: {
      // Keep reports in the CI run's filesystem; no external LHCI server.
      target: "filesystem",
      outputDir: "./.lighthouseci",
    },
  },
};
```

**Hard rules:**

- Every budget is a named constant with a unit in its name (`LCP_BUDGET_MS`) and a comment.
- `aggregationMethod: "median-run"` is non-negotiable — single-run gates flake constantly.
- `numberOfRuns` ≥ 3 (odd numbers give a clean median).
- Assert on TBT for INP in the lab; treat the experimental `interaction-to-next-paint` audit as a `warn`, not an `error` (it isn't present in every Lighthouse build).
- Keep `onlyCategories` to exactly what you gate — fewer audits, faster, less noise.

---

## 3. Choosing budget severity and thresholds

| Audit / category            | Severity | Threshold | Why                                                   |
| --------------------------- | -------- | --------- | ----------------------------------------------------- |
| `largest-contentful-paint`  | `error`  | ≤ 2500 ms | Google "good" LCP                                     |
| `cumulative-layout-shift`   | `error`  | ≤ 0.1     | Google "good" CLS                                     |
| `total-blocking-time`       | `error`  | ≤ 200 ms  | INP lab proxy                                         |
| `interaction-to-next-paint` | `warn`   | ≤ 200 ms  | not in all builds; don't hard-fail on a missing audit |
| `categories:performance`    | `error`  | ≥ 0.9     | top (green) band                                      |
| `categories:seo`            | `error`  | ≥ 0.95    | SEO is cheap to keep perfect                          |
| `categories:accessibility`  | `error`  | ≥ 0.95    | a11y regressions must block                           |
| `categories:best-practices` | `error`  | ≥ 0.9     | green band                                            |

Use `error` for contracts that must hold and `warn` for audits that are environment-dependent or
aspirational. **Start strict and only loosen with a recorded reason** — a budget you keep raising
to make CI pass is a budget that no longer protects anything.

---

## 4. The npm script

```jsonc
// package.json
{
  "scripts": {
    "lhci": "lhci autorun --config=./lighthouserc.cjs"
  }
}
```

`lhci autorun` runs `collect` → `assert` → `upload` in sequence. Run it locally before pushing to
reproduce exactly what CI does:

```bash
pnpm build && pnpm lhci
# desktop form factor:
LHCI_FORM_FACTOR=desktop pnpm build && LHCI_FORM_FACTOR=desktop pnpm lhci
```

---

## 5. The GitHub Actions workflow

Runs on PRs that touch the app or the workflow itself. Builds the production output, runs the
gate, and **always** uploads the reports (even on failure) so a red check is debuggable.

```yaml
name: Lighthouse CWV

on:
  pull_request:
    branches: [main]
    paths:
      - "apps/web/**"
      - ".github/workflows/lighthouse.yml"

permissions:
  contents: read

jobs:
  lighthouse:
    name: Lighthouse CWV (marketing pages)
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: apps/web
    steps:
      - uses: actions/checkout@v4

      - name: Setup pnpm
        uses: pnpm/action-setup@v4 # version comes from root package.json packageManager

      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: 22
          cache: pnpm

      - name: Install dependencies
        working-directory: .
        run: pnpm install --frozen-lockfile

      - name: Build web app
        run: pnpm build

      # build + start the production server, run Lighthouse on mobile emulation,
      # fail the job if any budget in lighthouserc.cjs is exceeded.
      - name: Run Lighthouse CI
        run: pnpm lhci

      - name: Upload Lighthouse reports
        if: always()
        uses: actions/upload-artifact@v4
        with:
          name: lighthouse-reports
          path: apps/web/.lighthouseci
          if-no-files-found: ignore
```

**Hard rules:**

- Trigger on the app path **and** the workflow file so config changes are self-testing.
- `if: always()` on the upload step — you need the report most when the gate fails.
- Gate on the **production** build (`pnpm build` then the `start` server in `collect`).
- Match the CI Node/pnpm versions to the repo's pinned versions to avoid lockfile drift.

---

## 6. Framework adapters

The config is framework-neutral except `startServerCommand` and `startServerReadyPattern`.

| Framework     | `startServerCommand`                                              | `startServerReadyPattern`                   |
| ------------- | ----------------------------------------------------------------- | ------------------------------------------- |
| **Next.js**   | `pnpm start --port 3100` (after `next build`)                     | `"Ready in"`                                |
| **Remix**     | `pnpm start` (serve the built app)                                | server's listening log line                 |
| **Astro**     | `node ./dist/server/entry.mjs` (SSR) or `npx serve dist` (static) | the adapter's ready line / serve's URL line |
| **SvelteKit** | `node build` (node adapter)                                       | `"Listening on"`                            |
| **Vite SPA**  | `npx vite preview --port 3100`                                    | `"Local:"`                                  |

For purely static output you can skip the server and point `collect.staticDistDir` at the build
folder instead of `startServerCommand` — Lighthouse serves it internally.

---

## 7. Debugging failing or flaky runs

- **Flaky LCP/TBT** → raise `numberOfRuns` (5), confirm `median-run`, and make sure nothing else is competing for CPU on the runner.
- **`interaction-to-next-paint` errors** → it should be `warn`, not `error`; the audit is missing in some Lighthouse versions.
- **"server not ready" timeout** → fix `startServerReadyPattern` to match the framework's actual ready log, and raise `startServerReadyTimeout`.
- **Real regressions** → open the uploaded report artifact, read the failed audit's "Opportunities"/"Diagnostics", fix the cause (oversized image, render-blocking JS, layout shift from unsized media) — don't just bump the budget.
- **Desktop vs mobile divergence** → run both form factors; mobile is the stricter gate and should be the default.

---

## 8. Conventions checklist (enforce in review)

- [ ] All budgets are named constants with units and comments — no magic numbers in assertions.
- [ ] Gate runs against the **production** build, never the dev server.
- [ ] `aggregationMethod: "median-run"` with `numberOfRuns` ≥ 3.
- [ ] CWV budgets at Google "good" thresholds (LCP ≤ 2500, TBT ≤ 200, CLS ≤ 0.1).
- [ ] INP gated via TBT (`error`); experimental INP audit is `warn`.
- [ ] Category floors set as `error` (perf ≥ 0.9, SEO/a11y ≥ 0.95, best-practices ≥ 0.9).
- [ ] `onlyCategories` lists exactly the gated categories.
- [ ] CI triggers on the app path **and** the workflow file; reports upload with `if: always()`.
- [ ] Local `pnpm lhci` reproduces the CI run.
- [ ] Budgets are tightened over time, loosened only with a recorded reason.

---

## 9. How to apply this skill

**Adding the gate to a project:** install `@lhci/cli`, drop in `lighthouserc.cjs` with your URLs
and `startServerCommand`, add the `lhci` script, and add the workflow. Run `pnpm build && pnpm lhci`
locally to confirm it passes before opening a PR.

**Adding a page to the gate:** append its URL to `MARKETING_URLS` (or a second URL array). Each URL
is audited independently against the same budgets.

**Tuning budgets:** change the named constant, not the assertion. Record why in the comment. Prefer
fixing the regression over raising the budget.

**Reviewing performance:** run the checklist in §8. The highest-value catches are a gate that runs
against the dev server (meaningless numbers) and single-run assertions (chronic flakiness).

---

## Publishing / installing this skill

This skill follows the Anthropic `SKILL.md` format and is portable across agents.

1. Keep it under `skills/frontend-lighthouse/SKILL.md` in a public GitHub repo.
2. Keep the frontmatter `name` and high-signal `description` — discovery indexes match against it.
3. Install with: `npx skills add <org>/<repo> --skill "frontend-lighthouse"`.
4. Non-`SKILL.md` agents can be pointed here from `AGENTS.md` / `CLAUDE.md`; Kiro can mirror it as a steering file.

## Limitations

- Lighthouse CI is a lab signal and does not replace field monitoring from real-user metrics.
- Budgets must be tuned to the actual app route, hosting platform, and device/network assumptions.
- A passing Lighthouse gate does not prove business-critical flows, visual correctness, or backend availability.

---



## Sub-Skill: vibecode-production-qa-validator

# Production QA Validator

Run phases in order. Fix failures before moving to next.

## When to Use

- Use before shipping or promoting a fullstack Next.js app to production.
- Use after large UI, SEO, auth, API, database, or dependency changes need a concrete launch-readiness pass.
- Use when you need a compact command-driven checklist for build, route, metadata, performance, security, and cleanup checks.

```bash
export PROD_URL="https://yourdomain.com"
export QA_AUTH_HEADER=""       # optional: "Bearer eyJ..."
export PAGESPEED_API_KEY=""    # optional: for auto PageSpeed API
```

---

## Consolidated Runner

```bash
qa:all() { qa:code && qa:build && qa:routes / /about /contact /privacy /terms /faq /sitemap.xml /robots.txt /api/health && qa:seo && qa:api /api/health /api/tools && qa:git && qa:smoke; }
qa:full() { qa:all && qa:auth && qa:auth:cookies && qa:lazyload && qa:heavyload && qa:vulns && qa:cleanup && qa:ux:cards && qa:ux:boundaries && qa:ux:animation && qa:database && qa:secure; }
```

---

### Phase 1: Code Integrity

- [ ] `npx tsc --noEmit`
- [ ] `npx eslint . --ext .js,.jsx,.ts,.tsx --max-warnings 0`
- [ ] `npm test -- --runInBand --passWithNoTests`

```bash
qa:code() { npx tsc --noEmit && npx eslint . --ext .js,.jsx,.ts,.tsx --max-warnings 0 && npm test -- --runInBand --passWithNoTests; }
```

---

### Phase 2: Build Verification

- [ ] `npm run build` succeeds
- [ ] SEO pages show `○`/`●` not `λ`
- [ ] Build log has no errors

```bash
qa:build() { local log; log="$(mktemp "${TMPDIR:-/tmp}/qa-build.XXXXXX.log")" || return 1; set -o pipefail; npm run build 2>&1 | tee "$log"; local rc=$?; set +o pipefail; [ "$rc" -eq 0 ] && ! grep -qi "error\|failed" "$log"; local ok=$?; rm -f "$log"; return "$ok"; }
```

| Symbol | Meaning |
|--------|---------|
| `○` | Static |
| `●` | SSG |
| `λ` | Dynamic/serverless |
| `⊕` | Partial prerender |

---

### Phase 3: API Session & Authentication

- [ ] Auth endpoints respond (login, session, logout)
- [ ] Protected routes return 401/403
- [ ] Session cookie: HttpOnly + Secure + SameSite
- [ ] Cookie not expired, Path/Domain correct
- [ ] No rate limiting bypass

```bash
qa:auth() {
  local F=0
  for ep in /api/auth/login /api/auth/session /api/auth/logout; do
    curl -so /dev/null -w "%{http_code}" "$PROD_URL$ep" | grep -q "200\|401" || { echo "  ✗ $ep unreachable"; ((F++)); }
  done
  curl -so /dev/null -w "%{http_code}" "$PROD_URL/api/protected" | grep -q "401\|403" || echo "  ⚠ Protected route not denying unauthenticated"
  return $F
}
qa:auth:cookies() {
  for ep in /api/auth/session /api/auth/login; do
    curl -sI "$PROD_URL$ep" | grep -i "^set-cookie:" | while IFS= read -r c; do
      echo "  $ep: $(echo "$c" | cut -d= -f1)"
      echo "$c" | grep -qi "HttpOnly" || echo "    ✗ Missing HttpOnly"
      echo "$c" | grep -qi "Secure" || echo "    ✗ Missing Secure"
      echo "$c" | grep -qi "SameSite" || echo "    ⚠ Missing SameSite"
    done
  done
}
```

---

### Phase 4: Route Regression

- [ ] Core pages, sitemap, robots.txt all 200
- [ ] URLs use kebab-case, no duplicate slugs
- [ ] robots.txt allows indexing
- [ ] Sitemap XML valid, all URLs resolve 200

```bash
qa:routes() { local F=0; for p; do local C=$(curl -so /dev/null -w "%{http_code}" "$PROD_URL$p"); echo "$C $p"; [ "$C" = "200" ] || ((F++)); done; return $F; }
qa:robots() { curl -s "$PROD_URL/robots.txt" | grep -qi "Disallow: /$" && echo "  ✗ Blocks all crawlers" || echo "  ✓ OK"; }
qa:sitemap() { curl -s "$PROD_URL/sitemap.xml" | python3 -c "import sys,xml.etree.ElementTree as ET; ET.parse(sys.stdin); print('✓ Valid XML')"; }
```

---

### Phase 5: SEO — Tags, Images, Favicon, Slugs

- [ ] `<title>` 30–60 chars, unique per page
- [ ] `<meta name="description">` in raw HTML
- [ ] og:title matches `<title>`, og:url matches canonical
- [ ] og:image ≥ 1200×630px, absolute URL, loads 200
- [ ] twitter:card = summary_large_image
- [ ] Canonical self-referencing, no duplicates
- [ ] `/favicon.ico` 200, apple-touch-icon present
- [ ] `hreflang` tags if multilingual
- [ ] JSON-LD structured data present
- [ ] Slugs: kebab-case, < 80 chars, no stop words

```bash
qa:seo() {
  local H=$(curl -s "$PROD_URL"); local F=0
  for t in "og:title" "og:description" "og:image" "twitter:card" "canonical" "description"; do echo "$H" | grep -qi "$t" || { echo "  ✗ $t"; ((F++)); }; done
  echo "$H" | grep -qi "<title>" || { echo "  ✗ <title>"; ((F++)); }
  local T=$(echo "$H" | grep -oP '<title>\K[^<]+'); local L=${#T}; [ $L -ge 30 -a $L -le 60 ] || echo "  ⚠ Title ${L}chars (target 30-60)"
  curl -so /dev/null -w "%{http_code}" "$PROD_URL/favicon.ico" | grep -q 200 || echo "  ⚠ No favicon.ico"
  return $F
}
qa:seo:ogimage() {
  local I=$(curl -s "$PROD_URL" | grep -oP 'og:image" content="\K[^"]+'); [[ "$I" =~ ^http ]] || I="$PROD_URL$I"
  curl -so /dev/null -w "%{http_code}" "$I" | grep -q 200 || { echo "  ✗ og:image returns non-200"; return 1; }
  command -v identify &>/dev/null && curl -s "$I" | identify -format "%wx%h" - 2>/dev/null | grep -qP "12\d{2}x6\d{2}" && echo "  ✓ ≥ 1200x630" || echo "  ⚠ Install imagemagick to check dimensions"
}
```

---

### Phase 6: API Route Behavior

- [ ] Correct status codes + Content-Type
- [ ] Errors return consistent JSON `{ error, message }`
- [ ] Response times < 200ms
- [ ] CORS headers correct (if cross-origin)

```bash
qa:api() {
  for p; do
    local R=$(curl -so /dev/null -w "%{http_code} %{content_type}" "$PROD_URL$p")
    echo "  $p → $R"
  done
  local E=$(curl -s "$PROD_URL/api/nonexistent")
  echo "$E" | python3 -c "import sys,json; d=json.load(sys.stdin); assert 'error' in d; print('✓ Consistent errors')" 2>/dev/null || echo "  ⚠ Inconsistent error shape"
}
```

---

### Phase 7: Git Hygiene

- [ ] No secrets/credentials in diff
- [ ] No `.next`/`node_modules` staged
- [ ] Commit: `type(scope): message`

```bash
qa:git() {
  local S=$(git diff HEAD 2>/dev/null | grep -i "password\|secret\|api_key\|localhost:3000" | grep "^+")
  [ -n "$S" ] && { echo "  ✗ Secrets in diff!"; echo "$S"; return 1; } || echo "  ✓ No secrets"
  local A=$(git status --short 2>/dev/null | grep -E "\.next|node_modules" | head -3)
  [ -n "$A" ] && echo "  ⚠ Build artifacts:" && echo "$A" || echo "  ✓ No artifacts"
}
```

---

### Phase 8: Post-Deployment Smoke Test

- [ ] Homepage 200, key pages 200
- [ ] OG image loads 200
- [ ] No console errors (manual)
- [ ] Auth flow works (manual)

```bash
qa:smoke() {
  curl -sI "$PROD_URL" | head -1 | grep -q "200" && echo "  ✓ Homepage" || echo "  ✗ Homepage"
  curl -sI "$PROD_URL/sitemap.xml" | head -1 | grep -q "200" && echo "  ✓ Sitemap" || echo "  ✗ Sitemap"
}
```

---

### Phase 9: Page Speed, Lazy Load & Bundles

- [ ] Lighthouse ≥ 90 (Perf, A11y, SEO)
- [ ] FCP < 2.5s, LCP < 4.0s, CLS < 0.1
- [ ] Images lazy-loaded (`loading="lazy"`), WebP/AVIF
- [ ] Dynamic imports for heavy components
- [ ] Largest JS chunk < 200KB gzipped
- [ ] `font-display: swap`, no FOIT
- [ ] Total page weight < 1MB

```bash
qa:lazyload() {
  local N=$(grep -r "loading=" app/ --include="*.tsx" 2>/dev/null | grep -c "lazy" || true)
  echo "  Lazy images: $N"
  grep -rn "next/dynamic\|dynamic((" app/ --include="*.tsx" 2>/dev/null | head -5 | grep . || echo "  ⚠ No dynamic imports"
}
qa:heavyload() {
  ls -lhS .next/static/chunks/*.js 2>/dev/null | head -5
  local W=$(curl -so /dev/null -w "%{size_download}" "$PROD_URL" 2>/dev/null || echo 0)
  echo "  HTML weight: ~$((W/1024))KB"
  echo "  ⚠ Run 'npx lighthouse $PROD_URL --view' for full weight analysis"
}
# PageSpeed: open "https://pagespeed.web.dev/?url=$PROD_URL"
```

---

### Phase 10: Cleanup & Vulnerability Scan

- [ ] `npm prune`, `depcheck` — no unused deps
- [ ] No console.log/debugger in staged code
- [ ] `npm audit` — zero critical/high vulnerabilities
- [ ] No eval/new Function/document.write
- [ ] TODOs resolved

```bash
qa:vulns() {
  npm audit 2>/dev/null | grep -E "critical|high" | grep . && echo "  ✗ Vulnerabilities!" || echo "  ✓ No critical/high vulns"
  npm outdated 2>/dev/null | head -5 | grep . || echo "  ✓ All up to date"
  local D=$(grep -rn "eval(\|new Function(\|document.write(" app/ src/ --include="*.ts" --include="*.tsx" 2>/dev/null | head -5) # security-allowlist: defensive source scan
  [ -n "$D" ] && echo "  ⚠ Dangerous patterns:" && echo "$D" || echo "  ✓ No dangerous patterns"
}
qa:cleanup() {
  local D=$(git diff --cached 2>/dev/null | grep "^+" | grep -i "console\.log\|debugger" | head -5)
  [ -n "$D" ] && echo "  ✗ Debug artifacts:" && echo "$D" || echo "  ✓ No debug artifacts"
  local T=$(git diff --cached 2>/dev/null | grep "^+" | grep -i "TODO\|FIXME\|HACK" | head -5)
  [ -n "$T" ] && echo "  ⚠ TODOs remain:" && echo "$T"
}
```

---

### Phase 11: UI/UX — Cards, Animation, Error Boundaries

- [ ] Cards: equal height grid, no overlap, text ellipsis, responsive (1→2→3 col)
- [ ] No horizontal scroll at any viewport (320–1440px)
- [ ] Images: consistent `aspect-ratio` + `object-fit: cover`
- [ ] Touch targets ≥ 44×44px
- [ ] Animations use `transform`+`opacity` only (not layout props)
- [ ] `prefers-reduced-motion` respected
- [ ] Error boundaries at root + route level (`app/error.tsx`, `app/global-error.tsx`)
- [ ] `app/not-found.tsx` and `app/loading.tsx` exist
- [ ] All client fetches show loading + error + empty states
- [ ] Buttons: hover, focus-visible, active, disabled, loading states
- [ ] Forms disable submit on click (no double-submit)

```bash
qa:ux:cards() {
  local E=$(grep -rn "text-overflow\|line-clamp\|truncate" app/ --include="*.css" --include="*.tsx" 2>/dev/null | head -3)
  [ -n "$E" ] && echo "  ✓ Text overflow handling" || echo "  ⚠ No text overflow handling"
  local A=$(grep -rn "aspect-\|object-fit" app/ --include="*.css" --include="*.tsx" 2>/dev/null | head -3)
  [ -n "$A" ] && echo "  ✓ aspect-ratio/object-fit used" || echo "  ⚠ No aspect-ratio set"
}
qa:ux:boundaries() {
  for f in app/error.tsx app/global-error.tsx app/not-found.tsx app/loading.tsx; do
    [ -f "$f" ] && echo "  ✓ $f" || echo "  ⚠ Missing $f"
  done
}
qa:ux:animation() {
  local A=$(grep -rn "animation.*width\|transition.*height\|@keyframes.*top\|@keyframes.*margin" app/ --include="*.css" --include="*.tsx" 2>/dev/null | head -5)
  [ -n "$A" ] && echo "  ⚠ Layout-triggering animations:" && echo "$A" || echo "  ✓ No layout-triggering animations"
  local P=$(grep -r "@media.*prefers-reduced-motion" app/ --include="*.css" --include="*.tsx" 2>/dev/null | head -3)
  [ -n "$P" ] && echo "  ✓ prefers-reduced-motion found in CSS" || echo "  ⚠ No prefers-reduced-motion in CSS"
}
```

---

### Phase 12: Database & Data Layer

- [ ] Connection pool configured (no starvation)
- [ ] Schema in sync with migrations
- [ ] Indexes on all queried columns, no N+1
- [ ] No hardcoded DB credentials in source
- [ ] No raw SQL injection risk
- [ ] No sensitive data leaked in API responses
- [ ] Migrations are idempotent

```bash
qa:database() {
  local H=$(grep -rn "postgres://\|mysql://\|mongodb://" app/ src/ --include="*.ts" --include="*.tsx" 2>/dev/null | grep -v ".env" | head -5)
  [ -n "$H" ] && { echo "  ✗ Hardcoded DB URL:"; echo "$H"; } || echo "  ✓ No hardcoded DB URLs"
  local R=$(grep -rn "\$queryRaw\|\.raw(" app/ src/ --include="*.ts" --include="*.tsx" 2>/dev/null | head -5)
  [ -n "$R" ] && echo "  ⚠ Raw SQL:" && echo "$R" || echo "  ✓ No raw SQL"
  local N=$(grep -rn "\.findMany\|\.findUnique" app/ src/ --include="*.ts" --include="*.tsx" 2>/dev/null | grep -v "include:" | head -5)
  [ -n "$N" ] && echo "  ⚠ Possible N+1:" && echo "$N" || echo "  ✓ No N+1 patterns"
}
qa:db:migrations() {
  [ -d "prisma/migrations" ] && echo "  ✓ Prisma: $(ls prisma/migrations 2>/dev/null | wc -l) migrations" || echo "  - No prisma migrations dir"
  local M=$(ls db/migrations/*.sql 2>/dev/null | head -5); [ -n "$M" ] && echo "  ✓ SQL migrations:" && echo "$M" || echo "  - No SQL migration files"
}
```

---

### Phase 13: Secure Data Rendering

- [ ] No secrets/tokens in client source or localStorage
- [ ] No `dangerouslySetInnerHTML` without DOMPurify
- [ ] API errors don't leak stack traces
- [ ] Internal IDs use UUIDs not auto-increment
- [ ] User emails masked in UI
- [ ] NEXT_PUBLIC_ vars contain no secrets

```bash
qa:secure() {
  local S=$(git grep -n "api_key\|API_KEY\|secret_key\|PRIVATE_KEY" -- ':!*.env*' ':!*test*' 2>/dev/null | head -5)
  [ -n "$S" ] && echo "  ✗ Secrets in source:" && echo "$S" || echo "  ✓ No hardcoded secrets"
  local D=$(grep -rn "dangerouslySetInnerHTML" app/ src/ --include="*.tsx" 2>/dev/null | head -5)
  [ -n "$D" ] && echo "  ⚠ XSS risk — use DOMPurify:" && echo "$D" || echo "  ✓ No dangerouslySetInnerHTML"
  local T=$(grep -rn "localStorage\|sessionStorage" app/ src/ --include="*.ts" --include="*.tsx" 2>/dev/null | grep -i "token\|jwt\|secret" | head -5)
  [ -n "$T" ] && echo "  ⚠ Tokens in storage — use httpOnly cookies:" && echo "$T" || echo "  ✓ No tokens in storage"
  curl -s "$PROD_URL/api/nonexistent" 2>/dev/null | grep -qi "stack\|Error:" && echo "  ✗ Stack trace leak" || echo "  ✓ No stack leak"
}
```

---

## Pre-Commit Hook

```bash
cat > .git/hooks/pre-commit << 'EOF'
#!/bin/sh
npx tsc --noEmit || exit 1
npx eslint . --ext .js,.jsx,.ts,.tsx --max-warnings 0 || exit 1
EOF
chmod +x .git/hooks/pre-commit
```

---

## CI/CD (GitHub Actions)

```yaml
name: QA
on: [push, pull_request]
jobs:
  qa:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
      - run: npm ci
      - run: npx tsc --noEmit
      - run: npx eslint . --ext .js,.jsx,.ts,.tsx --max-warnings 0
      - run: npm test -- --runInBand --passWithNoTests
      - run: npm run build
```

---

## Best Practices

| ✅ Do | ❌ Don't |
|-------|----------|
| Run full 13-phase flow before deploy | Skip typecheck or lint |
| Set `PROD_URL` in profile/.envrc | Hardcode URLs in scripts |
| OG images ≥ 1200×630 | Use small OG images |
| Animate with `transform`+`opacity` | Animate width/height/top |
| Show loading/error/empty states | Leave users on blank screens |
| `prefers-reduced-motion` for animations | Force motion on all users |
| HttpOnly + Secure cookies for tokens | localStorage for auth tokens |
| Error boundaries at all levels | White screen on crash |
| Database indexes + include/populate | N+1 queries in loops |
| `npm audit` before deploy | Deploy with known vulns |

---

## Common Pitfalls

| Problem | Solution |
|---------|----------|
| OG tags missing in raw HTML | Use `export const metadata` in Next.js |
| `Disallow: /` in robots.txt | Blocks all crawlers — use specific paths |
| Cards different heights in grid | Use `display: grid` with equal-height rows, not flex |
| Text overflows card | Add `text-overflow: ellipsis` + `overflow: hidden` |
| Animation jank | Animate `transform` not `width`/`height` |
| Form submits twice | Disable button on first click |
| Console errors in prod | Add `no-console` ESLint rule |
| DB connection timeout | Add connection pooling (PgBouncer/Prisma Accelerate) |
| Sensitive data in API | Strip `passwordHash`/`secret` in response transformer |
| App crashes on error | Add `app/error.tsx` error boundary |
| Large JS bundles | Dynamic import heavy components, analyze with `next/bundle-analyzer` |
| Images load slowly | Add `loading="lazy"`, use WebP/AVIF, resize to display size |

---

## Security Notes

- All `qa:*` functions are read-only (tsc, lint, test, build, curl, grep)
- `PROD_URL` and `QA_AUTH_HEADER` only for environments you own
- Basic secret scanning in `git diff` — for prod, use `trufflehog`/`git-secrets`
- Auth tests with real credentials against prod is destructive — use staging

---

## Limitations

- Passing all phases reduces risk but doesn't eliminate production bugs
- Some checks depend on project-specific tooling (Prisma, NextAuth, etc.)
- Manual UX testing still required for critical user journeys
- SEO checks verify raw HTML only — not social preview rendering
- Route checks verify status codes, not content correctness

---

## Master Checklist

### Phase 1: Code
- [ ] `tsc --noEmit`, `eslint`, `npm test` pass

### Phase 2: Build
- [ ] `npm run build` succeeds, no errors, pages static

### Phase 3: Auth
- [ ] Endpoints respond, protected routes denied, secure cookies

### Phase 4: Routes
- [ ] All core pages 200, sitemap valid, robots.txt correct

### Phase 5: SEO
- [ ] title, description, og:*, twitter:card, canonical, favicon, slugs

### Phase 6: API
- [ ] Status, Content-Type, consistent errors, timing

### Phase 7: Git
- [ ] No secrets, no artifacts, conventional commit

### Phase 8: Smoke
- [ ] Homepage + key pages 200, og:image loads

### Phase 9: Speed
- [ ] Lighthouse ≥ 90, lazy images, dynamic imports, font-display: swap

### Phase 10: Clean
- [ ] No vulns, no debug artifacts, unused deps pruned

### Phase 11: UI/UX
- [ ] Cards responsive, error boundaries, button states, reduced-motion

### Phase 12: Database
- [ ] Indexes, no N+1, no hardcoded URLs, no sensitive leaks

### Phase 13: Secure Rendering
- [ ] No secrets in client, no XSS, no stack leaks, UUIDs

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



## Sub-Skill: codebase-to-wordpress-converter

# Codebase to WordPress Converter

## Overview

This skill is designed for the high-fidelity conversion of static or React-based frontends into fully functional, CMS-driven WordPress themes. It acts as a **Senior WordPress Architect**, **React Expert**, and **QA Engineer** to ensure a 100% pixel-perfect match while integrating deep WordPress functionality like ACF, dynamic menus, and technical SEO preservation.

## When to Use This Skill

- Use when converting a React (CRA/Vite/Next.js) or HTML project into a WordPress theme.
- Use when the client demands a 100% pixel-perfect match with the original source.
- Use when auditing an existing WordPress conversion for structural or SEO flaws.
- Use when you need to ensure technical SEO (Schema, Meta tags, Heading hierarchy) is preserved exactly.

## Core Capabilities

### Phased Conversion & Audit
The skill follows a strict 4-phase forensic process:
1.  **Phase 1: Forensic UI Comparison**: Side-by-side table audit of React components vs. WordPress templates to find discrepancies.
2.  **Phase 2: Full Audit**: Deep dive into UI, SEO, CMS Editability, Navigation, Functionality, and Performance.
3.  **Phase 3: Action Plan**: Tasks classified as **SAFE**, **RISKY**, or **BLOCKED** to prevent breaking the UI.
4.  **Phase 4: Iterative Fixing**: Executing one safe task at a time with validation after each step.

### Absolute UI Lock
Strict enforcement of non-negotiable rules:
- No alterations to layout, spacing, typography, or colors.
- Exact preservation of Tailwind or CSS class names.
- Zero changes to DOM structure or HTML nesting.

## Step-by-Step Guide

### 1. Discovery & Forensic Audit
Start by identifying all components in the source code. Create a UI Comparison table comparing the original source output against the target WordPress output.
- *Rule: No fixes are allowed during this phase; only detection.*

### 2. Strategic Field Mapping
Map static React/HTML content to dynamic WordPress functions:
- Replace static text with `the_title()`, `get_field()`, or `the_content()`.
- Replace static paths with `get_template_directory_uri()`.

### 3. Implementation of Core Hooks
Ensure every theme includes the foundational WordPress hooks correctly:
- **Layout Files (`header.php` / `footer.php`)**: Must include `wp_head()` before `</head>` and `wp_footer()` before `</body>`.
- **Page Templates**: Must call `get_header()` and `get_footer()`.
- `register_nav_menus()` for dynamic navigation without breaking original HTML structure.

### 4. Validation & Live Tracker
Maintain a live tracker of Total Issues, Fixed, and Remaining. Every fix must be followed by a confirmation:
- ✅ No UI change
- ✅ No DOM change
- ✅ No class change

## Examples

### Example 1: Navigation Conversion
```php
// WRONG: Static replacement that adds wrappers
wp_nav_menu(['theme_location' => 'primary']);

// CORRECT: Preserving original Tailwind classes and structure
wp_nav_menu([
    'theme_location' => 'primary',
    'container' => false,
    'items_wrap' => '<ul class="flex space-x-8">%3$s</ul>',
    'walker' => new Custom_Tailwind_Walker()
]);
```

### Example 2: Asset Pathing
```php
// Source: <img src="/images/logo.png" />
// WP Conversion:
<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Logo" />
```

## Best Practices

- ✅ **Do:** Use `get_page_by_path()` for robust internal linking.
- ✅ **Do:** Implement ACF (Advanced Custom Fields) fallbacks in `functions.php`.
- ✅ **Do:** Keep the Tailwind configuration in the `header.php` to ensure global styles are active.
- ❌ **Don't:** Add "div" wrappers or rename classes to "clean up" the code.
- ❌ **Don't:** Use standard WordPress default styles if they conflict with the original design.

## Additional Resources

- [ACF Documentation](https://www.advancedcustomfields.com/resources/)
- [Tailwind CSS in WordPress](https://tailwindcss.com/docs/installation)
- [WordPress Theme Handbook](https://developer.wordpress.org/themes/)

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---



## Sub-Skill: deployment-procedures

# Deployment Procedures

> Deployment principles and decision-making for safe production releases.
> **Learn to THINK, not memorize scripts.**

---

## ⚠️ How to Use This Skill

This skill teaches **deployment principles**, not bash scripts to copy.

- Every deployment is unique
- Understand the WHY behind each step
- Adapt procedures to your platform

---

## 1. Platform Selection

### Decision Tree

```
What are you deploying?
│
├── Static site / JAMstack
│   └── Vercel, Netlify, Cloudflare Pages
│
├── Simple web app
│   ├── Managed → Railway, Render, Fly.io
│   └── Control → VPS + PM2/Docker
│
├── Microservices
│   └── Container orchestration
│
└── Serverless
    └── Edge functions, Lambda
```

### Each Platform Has Different Procedures

| Platform | Deployment Method |
|----------|------------------|
| **Vercel/Netlify** | Git push, auto-deploy |
| **Railway/Render** | Git push or CLI |
| **VPS + PM2** | SSH + manual steps |
| **Docker** | Image push + orchestration |
| **Kubernetes** | kubectl apply |

---

## 2. Pre-Deployment Principles

### The 4 Verification Categories

| Category | What to Check |
|----------|--------------|
| **Code Quality** | Tests passing, linting clean, reviewed |
| **Build** | Production build works, no warnings |
| **Environment** | Env vars set, secrets current |
| **Safety** | Backup done, rollback plan ready |

### Pre-Deployment Checklist

- [ ] All tests passing
- [ ] Code reviewed and approved
- [ ] Production build successful
- [ ] Environment variables verified
- [ ] Database migrations ready (if any)
- [ ] Rollback plan documented
- [ ] Team notified
- [ ] Monitoring ready

---

## 3. Deployment Workflow Principles

### The 5-Phase Process

```
1. PREPARE
   └── Verify code, build, env vars

2. BACKUP
   └── Save current state before changing

3. DEPLOY
   └── Execute with monitoring open

4. VERIFY
   └── Health check, logs, key flows

5. CONFIRM or ROLLBACK
   └── All good? Confirm. Issues? Rollback.
```

### Phase Principles

| Phase | Principle |
|-------|-----------|
| **Prepare** | Never deploy untested code |
| **Backup** | Can't rollback without backup |
| **Deploy** | Watch it happen, don't walk away |
| **Verify** | Trust but verify |
| **Confirm** | Have rollback trigger ready |

---

## 4. Post-Deployment Verification

### What to Verify

| Check | Why |
|-------|-----|
| **Health endpoint** | Service is running |
| **Error logs** | No new errors |
| **Key user flows** | Critical features work |
| **Performance** | Response times acceptable |

### Verification Window

- **First 5 minutes**: Active monitoring
- **15 minutes**: Confirm stable
- **1 hour**: Final verification
- **Next day**: Review metrics

---

## 5. Rollback Principles

### When to Rollback

| Symptom | Action |
|---------|--------|
| Service down | Rollback immediately |
| Critical errors | Rollback |
| Performance >50% degraded | Consider rollback |
| Minor issues | Fix forward if quick |

### Rollback Strategy by Platform

| Platform | Rollback Method |
|----------|----------------|
| **Vercel/Netlify** | Redeploy previous commit |
| **Railway/Render** | Rollback in dashboard |
| **VPS + PM2** | Restore backup, restart |
| **Docker** | Previous image tag |
| **K8s** | kubectl rollout undo |

### Rollback Principles

1. **Speed over perfection**: Rollback first, debug later
2. **Don't compound errors**: One rollback, not multiple changes
3. **Communicate**: Tell team what happened
4. **Post-mortem**: Understand why after stable

---

## 6. Zero-Downtime Deployment

### Strategies

| Strategy | How It Works |
|----------|--------------|
| **Rolling** | Replace instances one by one |
| **Blue-Green** | Switch traffic between environments |
| **Canary** | Gradual traffic shift |

### Selection Principles

| Scenario | Strategy |
|----------|----------|
| Standard release | Rolling |
| High-risk change | Blue-green (easy rollback) |
| Need validation | Canary (test with real traffic) |

---

## 7. Emergency Procedures

### Service Down Priority

1. **Assess**: What's the symptom?
2. **Quick fix**: Restart if unclear
3. **Rollback**: If restart doesn't help
4. **Investigate**: After stable

### Investigation Order

| Check | Common Issues |
|-------|--------------|
| **Logs** | Errors, exceptions |
| **Resources** | Disk full, memory |
| **Network** | DNS, firewall |
| **Dependencies** | Database, APIs |

---

## 8. Anti-Patterns

| ❌ Don't | ✅ Do |
|----------|-------|
| Deploy on Friday | Deploy early in week |
| Rush deployment | Follow the process |
| Skip staging | Always test first |
| Deploy without backup | Backup before deploy |
| Walk away after deploy | Monitor for 15+ min |
| Multiple changes at once | One change at a time |

---

## 9. Decision Checklist

Before deploying:

- [ ] **Platform-appropriate procedure?**
- [ ] **Backup strategy ready?**
- [ ] **Rollback plan documented?**
- [ ] **Monitoring configured?**
- [ ] **Team notified?**
- [ ] **Time to monitor after?**

---

## 10. Best Practices

1. **Small, frequent deploys** over big releases
2. **Feature flags** for risky changes
3. **Automate** repetitive steps
4. **Document** every deployment
5. **Review** what went wrong after issues
6. **Test rollback** before you need it

---

> **Remember:** Every deployment is a risk. Minimize risk through preparation, not speed.

## When to Use
This skill is applicable to execute the workflow or actions described in the overview.

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.

---
