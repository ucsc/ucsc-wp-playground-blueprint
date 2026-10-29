# UC Santa Cruz WordPress Playground

This repository is a [WordPress Playground](https://developer.wordpress.org/playground/) Blueprint Bundle. It opens a generic UC Santa Cruz campus site in the browser. You do not install anything, and each visitor gets a new copy of the site.

[Open in Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ucsc/wordpress-blueprints/main/blueprint.json)

> This is a proof of concept. Some features do not work. See [Known limitations](#known-limitations).

## What is in it

| Item | Source |
|------|--------|
| [UCSC 2022 theme](https://github.com/ucsc/ucsc-2022) | Latest GitHub release zip |
| [UCSC Custom Functionality](https://github.com/ucsc/ucsc-custom-functionality) | Latest GitHub release zip |
| [UCSC Blocks](https://github.com/ucsc/ucsc-blocks) | Latest GitHub release zip |
| Advanced Custom Fields (free) | wordpress.org |
| The Icon Block | wordpress.org |
| Sample pages and posts | `content/sample-content.wxr` in this repository |

The site runs as a generic campus site. The blueprint does not define `UCSC_NEWS_SITE`, so the News-only features stay off.

The blueprint imports the sample content first. Then `content/setup.php` deletes the default "Hello world!" post and the default "Sample Page". It makes Home the front page and Campus News the posts page.

## Layout

```
blueprint.json               Playground steps
content/sample-content.wxr   Sample content, imported by the importWxr step
content/setup.php            Cleanup, front page and posts page, run after import
```

`blueprint.json` loads both files in `content/` as `bundled` resources. A bundled resource is a file that sits next to the blueprint. Playground fetches bundled files from the folder that holds the blueprint. Thus this repository is the bundle. If you point Playground at the raw `blueprint.json`, it also gets the files in `content/`.

## Opening it

### From a link

Use the raw GitHub URL of `blueprint.json`. The `raw.githubusercontent.com` server sends the CORS header that Playground needs.

```
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ucsc/wordpress-blueprints/main/blueprint.json
```

To pin a version, replace `main` with a tag or a commit SHA.

### As a zip

Zip the bundle with `blueprint.json` at the root of the zip:

```bash
zip -r ucsc-blueprint.zip blueprint.json content
```

Load the zip from New > Blueprint URL in Playground, or give it to the CLI (see [Testing changes locally](#testing-changes-locally)). If you load the zip from a URL, the server must send a CORS header. GitHub does not send this header for release assets.

## Linking from another repository

Add this line to the README of any UCSC repository:

```markdown
[Preview in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ucsc/wordpress-blueprints/main/blueprint.json)
```

## How the theme and plugin are installed

The theme, UCSC Custom Functionality, and UCSC Blocks install from the newest release of each repository. The blueprint uses the GitHub address `releases/latest/download/<repo>.zip`. When a repository publishes a new release, Playground uses it. You do not change this repository.

GitHub sends release files without CORS headers, so the browser blocks the first, direct download from Playground. Playground then fetches the file through its own CORS proxy, and the install succeeds. A live boot with ucsc-2022 v26.10.19 and ucsc-custom-functionality v26.10.7 showed this behavior.

## Adding a plugin

If the release of a UCSC plugin includes a `<repo>.zip` file, you can add the plugin. The shared `ucsc/actions` release workflow attaches this file. Add one more step before `installTheme`, and replace `PLUGIN-REPO` with the name of the plugin repository:

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

Put each plugin step before the theme step. The theme needs ACF to be active at load time.

## Updating the sample content

The WXR file holds placeholder text only. To replace the content, do these steps:

1. Build the content on a WordPress site. A Playground site is sufficient.
2. Export the content from Tools > Export.
3. Save the export file over `content/sample-content.wxr`.

Follow these rules for the new content:

- If you want `setup.php` to set the front page and the posts page, keep the page slugs `home` and `campus-news`. Without a `home` page, the script sets neither page.
- Write internal links as root-relative paths, for example `href="/about/"`. Playground serves each site under its own `/scope:…` path and sends those links to that path.
- Do not write a Playground address into the content. The address changes for each visitor.

## Known limitations

- ACF PRO is not included. It is licensed, so a public blueprint cannot download it. ACF blocks do not render. This affects the main navigation and the breadcrumbs of the theme, and the blocks of the custom functionality plugin. The ACF options page of the theme is not available.
- The GitHub release zips download through the CORS proxy of Playground. See [How the theme and plugin are installed](#how-the-theme-and-plugin-are-installed).
- Changes do not persist. If you do not save the site from the Playground menu, your changes disappear when you close the tab.

## Testing changes locally

The Playground CLI needs Node.js 24.18 or later. Run this command from the repository root:

```bash
npx @wp-playground/cli@latest server --blueprint=. --blueprint-may-read-adjacent-files
```

The CLI does not read bundled files unless you add `--blueprint-may-read-adjacent-files`.

Open the address that the command prints, usually `http://127.0.0.1:9400`. You are logged in as `admin`.
