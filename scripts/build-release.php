<?php
/**
 * Build and validate a deterministic runtime-only archive for the RAN plugin starter.
 *
 * Usage: php scripts/build-release.php [--check] [--output=/absolute/path/plugin.zip]
 *
 * @package RanStarterPlugin
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec, WordPress.WP.AlternativeFunctions.unlink_unlink -- This standalone CLI archive tool runs without WordPress or WP_Filesystem.

declare(strict_types = 1);

const RAN_STARTER_PLUGIN_RELEASE_SLUG  = 'ran-starter-plugin';
const RAN_STARTER_PLUGIN_RELEASE_MTIME = 946684800;

/**
 * Stop the archive build with an error.
 *
 * @param string $message Error message.
 */
function ran_starter_plugin_release_fail( string $message ): void {
	fwrite( STDERR, $message . PHP_EOL );
	exit( 1 );
}

/**
 * Remove a temporary archive path.
 *
 * @param string $path File system path.
 */
function ran_starter_plugin_release_remove( string $path ): void {
	if ( ! file_exists( $path ) && ! is_link( $path ) ) {
		return;
	}

	if ( is_file( $path ) || is_link( $path ) ) {
		unlink( $path );
		return;
	}

	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
	foreach ( $iterator as $child ) {
		$child->isDir() && ! $child->isLink() ? rmdir( $child->getPathname() ) : unlink( $child->getPathname() );
	}
	rmdir( $path );
}

/**
 * Copy one allowlisted path into the archive stage.
 *
 * @param string $source Source path.
 * @param string $target Destination path.
 */
function ran_starter_plugin_release_copy( string $source, string $target ): void {
	$source = rtrim( $source, DIRECTORY_SEPARATOR );
	$target = rtrim( $target, DIRECTORY_SEPARATOR );

	if ( is_file( $source ) ) {
		if ( ! is_dir( dirname( $target ) ) && ! mkdir( dirname( $target ), 0755, true ) && ! is_dir( dirname( $target ) ) ) {
			ran_starter_plugin_release_fail( 'Could not create an archive staging directory.' );
		}
		if ( ! copy( $source, $target ) ) {
			ran_starter_plugin_release_fail( 'Could not stage ' . basename( $source ) . '.' );
		}
		return;
	}

	if ( ! is_dir( $source ) ) {
		ran_starter_plugin_release_fail( 'Release allowlist entry is missing: ' . $source );
	}
	if ( ! is_dir( $target ) && ! mkdir( $target, 0755, true ) && ! is_dir( $target ) ) {
		ran_starter_plugin_release_fail( 'Could not create an archive staging directory.' );
	}

	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
	foreach ( $iterator as $child ) {
		if ( $child->isLink() ) {
			continue;
		}
		$destination = $target . DIRECTORY_SEPARATOR . substr( $child->getPathname(), strlen( $source ) + 1 );
		if ( $child->isDir() ) {
			if ( ! is_dir( $destination ) && ! mkdir( $destination, 0755, true ) && ! is_dir( $destination ) ) {
				ran_starter_plugin_release_fail( 'Could not create an archive staging directory.' );
			}
		} elseif ( ! copy( $child->getPathname(), $destination ) ) {
			ran_starter_plugin_release_fail( 'Could not stage ' . $child->getFilename() . '.' );
		}
	}
}

/**
 * Read the plugin header version.
 *
 * @param string $root Plugin root.
 * @return string Header version.
 */
function ran_starter_plugin_release_version( string $root ): string {
	$plugin = file_get_contents( $root . '/' . RAN_STARTER_PLUGIN_RELEASE_SLUG . '.php' );
	if ( false === $plugin || ! preg_match( '/^ \\* Version:\\s*([^\\r\\n]+)$/m', $plugin, $matches ) ) {
		ran_starter_plugin_release_fail( 'Could not read the plugin header version.' );
	}
	return trim( $matches[1] );
}

/**
 * Verify the source version contract.
 *
 * @param string $root Plugin root.
 * @param string $version Expected version.
 */
function ran_starter_plugin_release_validate_versions( string $root, string $version ): void {
	$package  = json_decode( (string) file_get_contents( $root . '/package.json' ), true );
	$manifest = json_decode( (string) file_get_contents( $root . '/.release-please-manifest.json' ), true );
	if ( ! is_array( $package ) || ( $package['version'] ?? null ) !== $version || ! is_array( $manifest ) || ( $manifest['.'] ?? null ) !== $version ) {
		ran_starter_plugin_release_fail( 'Plugin header, package.json, and Release Please manifest versions must match.' );
	}
}

/**
 * Give every archived path a fixed timestamp.
 *
 * @param string $path Archive stage root.
 */
function ran_starter_plugin_release_normalize_times( string $path ): void {
	$paths    = array( $path );
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
	foreach ( $iterator as $child ) {
		if ( ! $child->isLink() ) {
			$paths[] = $child->getPathname();
		}
	}
	foreach ( $paths as $item ) {
		if ( ! touch( $item, RAN_STARTER_PLUGIN_RELEASE_MTIME ) ) {
			ran_starter_plugin_release_fail( 'Could not normalize archive timestamps.' );
		}
	}
}

/**
 * Write the staged plugin tree to a sorted ZIP archive.
 *
 * @param string $staging_root Stage root.
 * @param string $archive_path ZIP path.
 */
function ran_starter_plugin_release_write_archive( string $staging_root, string $archive_path ): void {
	$archive = new ZipArchive();
	if ( true !== $archive->open( $archive_path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
		ran_starter_plugin_release_fail( 'Could not create the release ZIP.' );
	}

	$entries  = array();
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $staging_root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
	foreach ( $iterator as $entry ) {
		if ( ! $entry->isLink() ) {
			$entries[] = $entry->getPathname();
		}
	}
	sort( $entries, SORT_STRING );
	$archive->addEmptyDir( RAN_STARTER_PLUGIN_RELEASE_SLUG );
	$archive->setMtimeName( RAN_STARTER_PLUGIN_RELEASE_SLUG, RAN_STARTER_PLUGIN_RELEASE_MTIME );
	foreach ( $entries as $entry ) {
		$name = RAN_STARTER_PLUGIN_RELEASE_SLUG . '/' . str_replace( DIRECTORY_SEPARATOR, '/', substr( $entry, strlen( $staging_root ) + 1 ) );
		if ( is_dir( $entry ) ) {
			$archive->addEmptyDir( $name );
		} else {
			$archive->addFile( $entry, $name );
		}
		$archive->setMtimeName( $name, RAN_STARTER_PLUGIN_RELEASE_MTIME );
	}
	$archive->close();
}

/**
 * Validate the ZIP's runtime contract.
 *
 * @param string $archive_path ZIP path.
 * @param string $version Expected version.
 */
function ran_starter_plugin_release_validate_archive( string $archive_path, string $version ): void {
	$archive = new ZipArchive();
	if ( true !== $archive->open( $archive_path ) ) {
		ran_starter_plugin_release_fail( 'Could not validate the release ZIP.' );
	}
	$root = RAN_STARTER_PLUGIN_RELEASE_SLUG . '/';
	foreach ( array( 'LICENSE', 'index.php', 'ran-starter-plugin.php', 'uninstall.php', 'inc/Base/Bootstrap.php', 'assets/dist/admin/js/admin.min.js', 'vendor/autoload.php', 'vendor/psr/log/src/LoggerInterface.php', 'vendor/ran/plugin-lib/inc/BootstrapInterface.php' ) as $required ) {
		if ( false === $archive->locateName( $root . $required ) ) {
			$archive->close();
			ran_starter_plugin_release_fail( 'Archive is missing required runtime file: ' . $required );
		}
	}
	$plugin = $archive->getFromName( $root . 'ran-starter-plugin.php' );
	if ( false === $plugin || ! preg_match( '/^ \\* Version:\\s*' . preg_quote( $version, '/' ) . '\\s*$/m', $plugin ) ) {
		$archive->close();
		ran_starter_plugin_release_fail( 'Archive plugin header version does not match the release version.' );
	}
	// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- ZipArchive exposes numFiles.
	for ( $index = 0; $index < $archive->numFiles; $index++ ) {
		$name = $archive->getNameIndex( $index );
		if ( ! str_starts_with( $name, $root ) || preg_match( '#^' . preg_quote( $root, '#' ) . '(?:node_modules|tests|\\.git|\\.github|scripts)/|^' . preg_quote( $root, '#' ) . 'vendor/bin/#', $name ) ) {
			$archive->close();
			ran_starter_plugin_release_fail( 'Archive contains an invalid development path: ' . $name );
		}
	}
	$archive->close();
}

$root    = dirname( __DIR__ );
$version = ran_starter_plugin_release_version( $root );
$check   = in_array( '--check', $argv, true );
$output  = null;
foreach ( $argv as $argument ) {
	if ( str_starts_with( $argument, '--output=' ) ) {
		$output = substr( $argument, 9 );
	}
}
if ( $check && null !== $output ) {
	ran_starter_plugin_release_fail( '--check cannot be combined with --output.' );
}
if ( ! class_exists( 'ZipArchive' ) ) {
	ran_starter_plugin_release_fail( 'The PHP ZipArchive extension is required to build a release archive.' );
}
ran_starter_plugin_release_validate_versions( $root, $version );
$staging      = sys_get_temp_dir() . '/ran-starter-plugin-release-' . bin2hex( random_bytes( 8 ) );
$staging_root = $staging . '/' . RAN_STARTER_PLUGIN_RELEASE_SLUG;
if ( ! mkdir( $staging_root, 0755, true ) && ! is_dir( $staging_root ) ) {
	ran_starter_plugin_release_fail( 'Could not create the release staging directory.' );
}
try {
	$allowlist = file( $root . '/release-contents.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	if ( false === $allowlist ) {
		ran_starter_plugin_release_fail( 'Could not read release-contents.txt.' );
	}
	foreach ( $allowlist as $entry ) {
		$entry = trim( $entry );
		if ( '' !== $entry && ! str_starts_with( $entry, '#' ) ) {
			ran_starter_plugin_release_copy( $root . '/' . $entry, $staging_root . '/' . $entry );
		}
	}
	exec( 'cd ' . escapeshellarg( $staging_root ) . ' && composer install --no-dev --no-interaction --prefer-dist --no-scripts --optimize-autoloader', $composer_output, $composer_status );
	if ( 0 !== $composer_status ) {
		ran_starter_plugin_release_fail( 'Could not install production Composer dependencies for the release archive.' );
	}
	unlink( $staging_root . '/composer.json' );
	unlink( $staging_root . '/composer.lock' );
	ran_starter_plugin_release_normalize_times( $staging_root );
	$archive_path = $output ?: ( $check ? $staging . '/' . RAN_STARTER_PLUGIN_RELEASE_SLUG . '-' . $version . '.zip' : $root . '/dist/' . RAN_STARTER_PLUGIN_RELEASE_SLUG . '-' . $version . '.zip' );
	if ( ! is_dir( dirname( $archive_path ) ) && ! mkdir( dirname( $archive_path ), 0755, true ) && ! is_dir( dirname( $archive_path ) ) ) {
		ran_starter_plugin_release_fail( 'Could not create the release output directory.' );
	}
	ran_starter_plugin_release_write_archive( $staging_root, $archive_path );
	ran_starter_plugin_release_validate_archive( $archive_path, $version );
	if ( $check ) {
		unlink( $archive_path );
		fwrite( STDOUT, "Release archive validation passed.\n" );
	} else {
		fwrite( STDOUT, 'Created ' . $archive_path . PHP_EOL );
	}
} finally {
	ran_starter_plugin_release_remove( $staging );
}
