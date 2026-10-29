## What this repository is

This repository is a WordPress Playground Blueprint Bundle. A bundle is a `blueprint.json` file plus the files that it loads with `"resource": "bundled"`. Playground fetches bundled files from the same folder as the blueprint, so the repository root is the bundle. There is no build step, no package manager, no linter, and no test suite.

The bundle boots a generic UC Santa Cruz campus site. It installs the UCSC 2022 theme, the UCSC Custom Functionality plugin, the UCSC Blocks plugin, the free Advanced Custom Fields (ACF) plugin, and The Icon Block. It also installs the UCSC Gutenberg Blocks plugin but does not activate it. Then it imports sample content.

## Running it locally

Run this command from the repository root:

```bash
npx @wp-playground/cli@latest server --blueprint=. --blueprint-may-read-adjacent-files
```

Each boot runs the blueprint on a fresh site. To test a change, stop the server and start it again. The Node.js version, the login, and the zip build are in the "Testing changes locally" and "Opening it" sections of `README.md`.

## How the steps fit together

The order of the steps in `blueprint.json` matters:

1. `setSiteOptions` sets the site name and the `/%postname%/` permalink structure.
2. The plugin steps run before `installTheme`. The theme needs ACF to be active at load time. Put any new plugin step before `installTheme`.
3. `importWxr` imports `content/sample-content.wxr`.
4. `writeFile` copies `content/setup.php` to `/tmp/ucsc-setup.php`, and `runPHP` then requires it. The `runPHP` step takes inline code only, so this two-step pattern is how the bundle runs a PHP file from the repository.

`content/setup.php` deletes the default "Hello world!" post and "Sample Page". If it finds the page with slug `home`, it makes that page the front page. It then makes the page with slug `campus-news` the posts page. If there is no `home` page, the script does not set a posts page either. If you rename those slugs in the WXR file, update `setup.php` too.

The theme and the UCSC plugins install from `https://github.com/ucsc/<repo>/releases/latest/download/<repo>.zip`. These downloads go through the CORS proxy of Playground. The "How the theme and plugins are installed" section of `README.md` explains why. Each install step sets `targetFolderName` to the repository name, so the folder name stays the same for every release.

## Constraints

- `UCSC_NEWS_SITE` is not defined on purpose, so the News-only features of the theme stay off.
- The "Known limitations" section of `README.md` lists what does not work in Playground, for example ACF blocks.
- In the sample content, write internal links as root-relative paths, for example `href="/about/"`. Playground serves each site under its own `/scope:…` path, and that address changes for every visitor. Do not put a Playground address in the content.
- Files use tab indentation.

## Git Conventions

### All work in this repository

- Eliminate AI-speak; be direct, concise, and technically precise
- Always work on a branch
- If working on an issue, check out an issue branch

### Commits:

- Always use conventional commits
- Subject line <72 chars, imperative mood, no period at end
- Body explains WHY, not WHAT; wrap at 72 chars
- Never commit to main directly
- Use atomic commits -- one logical change per commit
- Before you commit a change to `blueprint.json` or `content/`, boot the bundle locally and make sure that the site loads
- If a ROADMAP.md file exists, update it to reflect work done on the branch

### Pull Requests (PRs)

- Only create Draft PRs

### PR Descriptions

Include:

- What changed (bullets, not prose)
- Why it changed
- How to test
- Breaking changes, if any
- Migration steps, if any

Audience: Reviewer who didn't see our chat
