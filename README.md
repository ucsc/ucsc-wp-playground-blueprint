# UC Santa Cruz WordPress Playground

A [WordPress Playground](https://developer.wordpress.org/playground/) Blueprint Bundle that opens a generic UC Santa Cruz campus site in the browser. Nothing to install, and every visitor gets a fresh copy.

**[Open in Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ucsc/ucsc-wp-playground-blueprint/main/blueprint.json)**

> Proof of concept. See [Known limitations](#known-limitations).

## What's in it

| Item | Source |
|------|--------|
| [UCSC 2022 theme](https://github.com/ucsc/ucsc-2022) | Latest GitHub release zip |
| [UCSC Custom Functionality](https://github.com/ucsc/ucsc-custom-functionality) | Latest GitHub release zip |
| Advanced Custom Fields (free) | wordpress.org |
| The Icon Block | wordpress.org |
| Sample pages and posts | `content/sample-content.wxr` in this repo |

The site runs as a generic campus site. `UCSC_NEWS_SITE` isn't defined, so the News-only features stay off.

After the sample content is imported, `content/setup.php` removes WordPress's default "Hello world!" post and "Sample Page", then makes **Home** the front page and **Campus News** the posts page.

## Layout

```
blueprint.json               Playground steps
content/sample-content.wxr   Sample content, imported by the importWxr step
content/setup.php            Cleanup, front page and posts page, run after import
```

`blueprint.json` loads both files in `content/` as `bundled` resources. Playground fetches bundled files from the same folder as the blueprint, so this repo is the bundle: point Playground at the raw `blueprint.json` and the rest comes with it.

## Opening it

**From a link.** Use the raw GitHub URL of `blueprint.json`. `raw.githubusercontent.com` sends the CORS header Playground needs.

```
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ucsc/ucsc-wp-playground-blueprint/main/blueprint.json
```

To pin a version, replace `main` with a tag or commit SHA.

**As a zip.** Zip the bundle with `blueprint.json` at the root:

```bash
zip -r ucsc-blueprint.zip blueprint.json content
```

Load the zip from Playground's **New > Blueprint URL**, or pass it to the CLI (below). A zip loaded by URL must be served with a CORS header; GitHub release assets aren't.

## Linking from another repo

Add this to any UCSC repo's README:

```markdown
[Preview in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ucsc/ucsc-wp-playground-blueprint/main/blueprint.json)
```

## How the theme and plugin are installed

The theme and UCSC Custom Functionality install from each repo's newest release, using GitHub's `releases/latest/download/<repo>.zip` address. A new release shows up in Playground with no change here.

GitHub serves release files without CORS headers, so the browser blocks Playground's first, direct download. Playground then fetches the file through its own CORS proxy, and the install succeeds. This was confirmed in a live boot with ucsc-2022 v26.10.19 and ucsc-custom-functionality v26.10.7.

## Adding a plugin

Any UCSC plugin whose release attaches a `<repo>.zip` (the shared `ucsc/actions` release workflow does this) can be added with one more step before `installTheme`:

```json
{
	"step": "installPlugin",
	"pluginData": {
		"resource": "url",
		"url": "https://github.com/ucsc/PLUGIN-REPO/releases/latest/download/PLUGIN-REPO.zip"
	},
	"options": {
		"activate": true,
		"targetFolderName": "PLUGIN-REPO"
	}
}
```

Keep plugins ahead of the theme so ACF is active when the theme loads.

## Updating the sample content

The WXR file holds placeholder text only. To replace it, build the content on any WordPress site (a Playground instance works), export it from **Tools > Export**, and save the file over `content/sample-content.wxr`.

- Keep the page slugs `home` and `campus-news` if you want `setup.php` to keep setting the front page and posts page.
- Write internal links as root-relative paths (`href="/about/"`). Playground serves each site under its own `/scope:…` path and redirects those links to it. Don't hard-code a Playground address, since it changes for every visitor.

## Known limitations

- **No ACF PRO.** It's licensed, so a public blueprint can't fetch it. ACF blocks don't render: the theme's main navigation and breadcrumbs, and the custom functionality plugin's blocks. The theme's ACF options page is unavailable.
- **Depends on Playground's CORS proxy** for the GitHub release zips (see above).
- **Nothing persists.** Changes disappear when the tab closes unless they're saved from Playground's menu.

## Testing changes locally

Run the bundle with the Playground CLI. It needs Node.js 24.18 or later.

```bash
npx @wp-playground/cli@latest server --blueprint=. --blueprint-may-read-adjacent-files
```

Then open the address it prints, usually `http://127.0.0.1:9400`. You're logged in as `admin`.
