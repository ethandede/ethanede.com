# Local by Flywheel WP-CLI Access for VS Code

## Method A: Working Solution - Manual Environment Setup

### Step 1: Set Local PHP and WP-CLI Paths
```bash
export PATH="/Users/edede/Library/Application Support/Local/lightning-services/php-8.1.29+0/bin/darwin-arm64/bin:/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/posix:$PATH"
```

### Step 2: Test WP-CLI
```bash
wp core version
```
Should return: `6.8.2`

### Step 3: Run WP-CLI Commands
All subsequent WP-CLI commands will work in the same terminal session:
```bash
wp term list project_category --fields=name,slug
wp post list --post_type=post --s='Strategy' --fields=ID,title,post_name
wp post term list 50 project_category
```

## Method B: Local Shell Environment Script

### Step 1: Use Local's Shell Script
```bash
source /Users/edede/Library/Application\ Support/Local/ssh-entry/18tcPIiBl.sh
```

### Step 2: Combine with PATH Export
The shell script sets up the environment, but you still need to export the PATH:
```bash
source /Users/edede/Library/Application\ Support/Local/ssh-entry/18tcPIiBl.sh
export PATH="/Users/edede/Library/Application Support/Local/lightning-services/php-8.1.29+0/bin/darwin-arm64/bin:/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/posix:$PATH"
wp core version
```

## Method C: Configuration Files (Backup Method)

Configuration files are created but not needed with Method A:
- `/Users/edede/Local Sites/ethanede/wp-cli.local.yml`
- `/Users/edede/Local Sites/ethanede/wp-cli.local.php` (MySQL socket: `/Users/edede/Library/Application Support/Local/run/18tcPIiBl/mysql/mysqld.sock`)

### What to Look For

**Success indicators:**
- `wp core version` returns WordPress version number
- No PHP library errors
- Commands execute without "command not found" errors

**Common issues:**
- **Error**: `dyld: Library not loaded: /usr/local/opt/icu4c/lib/libicuio.74.dylib`
  **Fix**: This indicates a Homebrew PHP/ICU library mismatch. Use Method B (Local Shell Environment) instead

- **Error**: `wp: command not found` 
  **Fix**: Set the PATH or source the Local environment script

## Current Status - WORKING ✅

**Method A - Working Solution**:
- ✅ Local PHP path: `/Users/edede/Library/Application Support/Local/lightning-services/php-8.1.29+0/bin/darwin-arm64/bin`
- ✅ WP-CLI path: `/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/posix`
- ✅ WordPress 6.8.2 accessible
- ✅ All WP-CLI commands functional

**Verified Commands**:
```bash
wp core version                    # Returns: 6.8.2
wp term list project_category      # Shows all categories including "Strategy &amp; Vision"
wp post list --post_type=post      # Lists blog posts
wp post term list [ID] project_category  # Shows category assignments
```

**ICU Library Issue Resolved**: Using Local's managed PHP (8.1.29) bypasses the system PHP ICU library conflict.

## Essential WP-CLI Commands for This Project

```bash
# Check project categories
wp term list project_category --fields=name,slug

# Find Strategy & Vision posts  
wp post list --post_type=post --s="Strategy" --fields=ID,title

# Check project category assignments
wp post term list [POST_ID] project_category

# Database queries for debugging
wp db query "SELECT name, slug FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'project_category')"
```

## Database Access

- **Host**: `localhost`
- **Database**: `local` 
- **Username**: `root`
- **Password**: `root`
- **Port**: Check Local app (varies by site)

## Debug Log Location

`/wp-content/debug.log` - Check for PHP errors and warnings