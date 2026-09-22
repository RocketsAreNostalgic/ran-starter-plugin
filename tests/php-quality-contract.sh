#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."
fixture_dir=$(mktemp -d "$PWD/.quality-contract.XXXXXX")
trap 'rm -rf "$fixture_dir"' EXIT
fixture="$fixture_dir/quality.php"

cat > "$fixture" <<'PHP'
<?php
/**
 * Quality contract fixture.
 *
 * @package RanPlugin
 */

/**
 * Return example values.
 *
 * @return array Example values.
 */
function ran_starter_plugin_quality_contract() {
	$short = 'a';
	$longer_name = 'b';
	$values = array(
		'short' => $short,
		'longer_name' => $longer_name,
	);
	return $values;
}
PHP

# Both alignment rules must be blocking under the ordinary ruleset (-n included).
if php vendor/bin/phpcs --standard=.phpcs.xml -n -q --report=json "$fixture" > "$fixture_dir/report.json"; then
	echo 'Alignment violations unexpectedly passed PHPCS' >&2
	exit 1
fi
php -r '
$report = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$found = [];
foreach ($report["files"] as $file) {
    foreach ($file["messages"] as $message) {
        if ($message["type"] === "ERROR") { $found[$message["source"]] = true; }
    }
}
foreach (["Generic.Formatting.MultipleStatementAlignment.NotSame", "WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned"] as $source) {
    if (!isset($found[$source])) { fwrite(STDERR, "Missing blocking diagnostic: $source\n"); exit(1); }
}
' "$fixture_dir/report.json"

status=0
php vendor/bin/phpcbf --standard=.phpcs.xml -q "$fixture" || status=$?
# PHPCBF returns 1 when all fixable violations were corrected.
[[ "$status" -eq 1 ]]
php vendor/bin/phpcs --standard=.phpcs.xml -q "$fixture"
cp "$fixture" "$fixture_dir/first-pass.txt"
php vendor/bin/phpcbf --standard=.phpcs.xml -q "$fixture"
cmp "$fixture" "$fixture_dir/first-pass.txt"

# Prove the actual Composer parser aggregate succeeds before the negative case.
composer lint:syntax > "$fixture_dir/syntax-valid.log" 2>&1

# Exercise the actual Composer parser aggregate, not only a standalone php -l.
printf '<?php function broken( {\n' > "$fixture"
if composer lint:syntax > "$fixture_dir/syntax.log" 2>&1; then
	echo 'Invalid PHP unexpectedly passed lint:syntax' >&2
	exit 1
fi
echo 'PHP quality contract passed'
