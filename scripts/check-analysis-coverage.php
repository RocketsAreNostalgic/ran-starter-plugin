<?php
/**
 * Verify actual maintained PHP selection and analyze executable PHP snippets.
 *
 * @package RanPlugin
 */

declare(strict_types = 1);
$ran_starter_plugin_root = dirname( __DIR__ );
try {
	require_once $ran_starter_plugin_root . '/vendor/autoload.php';
	$ran_starter_plugin_container = ( new \PHPStan\DependencyInjection\ContainerFactory( $ran_starter_plugin_root ) )->create( $ran_starter_plugin_root . '/vendor/ran-starter-analysis-cache/coverage', array( $ran_starter_plugin_root . '/phpstan.neon' ), array() );
	if ( 8 !== (int) $ran_starter_plugin_container->getParameter( 'level' ) || false !== $ran_starter_plugin_container->getParameter( 'treatPhpDocTypesAsCertain' ) || 80400 !== $ran_starter_plugin_container->getParameter( 'phpVersion' ) ) {
		throw new RuntimeException( 'Maintained PHP requires Level 8, unchanged PHPDoc trust and PHP 8.4.' );
	}
	// Compare extension-provided stubs and runtime bootstraps with the unchanged locked extension, rather than admitting new executable configuration.
	$ran_starter_plugin_defaults = ( new \PHPStan\DependencyInjection\ContainerFactory( $ran_starter_plugin_root ) )->create( $ran_starter_plugin_root . '/vendor/ran-starter-analysis-cache/defaults', array( $ran_starter_plugin_root . '/vendor/szepeviktor/phpstan-wordpress/extension.neon' ), array() );
	foreach ( array( 'ignoreErrors', 'stubFiles', 'excludePaths', 'bootstrapFiles' ) as $ran_starter_plugin_parameter ) {
		if ( $ran_starter_plugin_defaults->getParameter( $ran_starter_plugin_parameter ) !== $ran_starter_plugin_container->getParameter( $ran_starter_plugin_parameter ) ) {
			throw new RuntimeException( 'Analysis exclusions, stubs, suppressions or executable bootstraps require review.' );
		}
	}
	$ran_starter_plugin_selected        = $ran_starter_plugin_container->getService( 'fileFinderAnalyse' )->findFiles( $ran_starter_plugin_container->getParameter( 'paths' ) )->getFiles();
	$ran_starter_plugin_directory       = new RecursiveDirectoryIterator( $ran_starter_plugin_root, FilesystemIterator::SKIP_DOTS );
	$ran_starter_plugin_filter          = new RecursiveCallbackFilterIterator(
		$ran_starter_plugin_directory,
		static function ( SplFileInfo $file ) use ( $ran_starter_plugin_root ): bool {
			$relative = substr( $file->getPathname(), strlen( $ran_starter_plugin_root ) + 1 );
			if ( in_array( $relative, array( '.git', '.dex', 'vendor', 'node_modules' ), true ) ) {
				return false;
			}
			if ( $file->isLink() ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- This is a standalone CLI source path diagnostic, not rendered HTML.
				throw new RuntimeException( 'Maintained source link requires review: ' . $relative );
			}
			return true;
		}
	);
	$ran_starter_plugin_count           = 0;
	$ran_starter_plugin_snippets        = array();
	$ran_starter_plugin_xml_declaration = '~\A(?:\xEF\xBB\xBF)?<\?xml[ \t\r\n]+version[ \t\r\n]*=[ \t\r\n]*(?:"1\.[01]"|\'1\.[01]\')(?:[ \t\r\n]+encoding[ \t\r\n]*=[ \t\r\n]*(?:"[A-Za-z][A-Za-z0-9._-]*"|\'[A-Za-z][A-Za-z0-9._-]*\'))?(?:[ \t\r\n]+standalone[ \t\r\n]*=[ \t\r\n]*(?:"(?:yes|no)"|\'(?:yes|no)\'))?[ \t\r\n]*\?>~';
	// Native PHP tokens distinguish executed code from strings and XML data without executing source.
	$ran_starter_plugin_classify = static function ( string $source, string $origin ) use ( $ran_starter_plugin_xml_declaration ): bool {
		$inspection = preg_replace( $ran_starter_plugin_xml_declaration, '', $source );
		if ( null === $inspection ) {
			throw new RuntimeException( 'Cannot classify maintained source.' );
		}
		$has_php = false;
		foreach ( token_get_all( $inspection ) as $token ) {
			if ( ! is_array( $token ) ) {
				continue;
			}
			if ( ( T_INLINE_HTML === $token[0] && str_contains( $token[1], '<?' ) ) || ( T_OPEN_TAG === $token[0] && ! str_starts_with( strtolower( $token[1] ), '<?php' ) ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Standalone CLI diagnostic includes the actual maintained source origin, not HTML.
				throw new RuntimeException( 'Unsupported PHP opening tag requires review: ' . $origin );
			}
			if ( T_OPEN_TAG === $token[0] || T_OPEN_TAG_WITH_ECHO === $token[0] ) {
				$has_php = true;
			}
		}
		return $has_php;
	};
	foreach ( new RecursiveIteratorIterator( $ran_starter_plugin_filter ) as $ran_starter_plugin_file ) {
		if ( ! $ran_starter_plugin_file->isFile() ) {
			continue;
		}
		$ran_starter_plugin_path      = $ran_starter_plugin_file->getPathname();
		$ran_starter_plugin_relative  = substr( $ran_starter_plugin_path, strlen( $ran_starter_plugin_root ) + 1 );
		$ran_starter_plugin_extension = strtolower( $ran_starter_plugin_file->getExtension() );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Inspect local maintained source bytes without executing them or loading synthetic WordPress globals.
		$ran_starter_plugin_source = file_get_contents( $ran_starter_plugin_path );
		if ( false === $ran_starter_plugin_source ) {
			throw new RuntimeException( 'Cannot inspect maintained source: ' . $ran_starter_plugin_relative );
		}
		if ( in_array( $ran_starter_plugin_extension, array( 'sh', 'yml', 'yaml' ), true ) || preg_match( '/\A#![^\n]*(?:bash|sh)\b/', $ran_starter_plugin_source ) ) {
			$ran_starter_plugin_remaining = preg_replace_callback(
				"/php -r '([^']*)'/s",
				static function ( array $match ) use ( &$ran_starter_plugin_snippets, $ran_starter_plugin_relative ): string {
					$ran_starter_plugin_snippets[] = array(
						'source' => '<?php' . "\n" . $match[1],
						'origin' => $ran_starter_plugin_relative,
					);
					return '';
				},
				$ran_starter_plugin_source
			);
			if ( null === $ran_starter_plugin_remaining ) {
				throw new RuntimeException( 'Cannot inspect inline PHP.' );
			}
			$ran_starter_plugin_remaining = preg_replace_callback(
				"/<<'([A-Z_]+)'\\R(.*?)\\R\\1(?:\\R|$)/s",
				static function ( array $match ) use ( &$ran_starter_plugin_snippets, $ran_starter_plugin_relative, $ran_starter_plugin_classify ): string {
					if ( $ran_starter_plugin_classify( $match[2], $ran_starter_plugin_relative ) ) {
						$ran_starter_plugin_snippets[] = array(
							'source' => $match[2],
							'origin' => $ran_starter_plugin_relative,
						);
						return '';
					}
					return $match[2];
				},
				$ran_starter_plugin_remaining
			);
			if ( null === $ran_starter_plugin_remaining ) {
				throw new RuntimeException( 'Cannot inspect PHP heredocs.' );
			}
			// The existing parser regression deliberately writes invalid PHP and must still fail composer lint:syntax.
			if ( 'tests/php-quality-contract.sh' === $ran_starter_plugin_relative ) {
				$ran_starter_plugin_remaining = str_replace( "printf '<?php function broken( {\\n' > \"\$fixture\"", '', $ran_starter_plugin_remaining );
			}
			if ( preg_match( '/(?:^|\R|[;&|])\s*(?:run:\s*|if\s+|then\s+)?php\b[^\n]*(?:\s-[A-Za-z]*[rBRE]\b|\s--(?:run|process-begin|process-code|process-end)\b)|<\?/', $ran_starter_plugin_remaining ) ) {
				throw new RuntimeException( 'Embedded PHP form requires review: ' . $ran_starter_plugin_relative );
			}
			continue;
		}
		$ran_starter_plugin_has_php = $ran_starter_plugin_classify( $ran_starter_plugin_source, $ran_starter_plugin_relative );
		if ( in_array( $ran_starter_plugin_extension, array( 'php', 'phtml', 'inc' ), true ) || $ran_starter_plugin_has_php ) {
			if ( ! in_array( $ran_starter_plugin_path, $ran_starter_plugin_selected, true ) ) {
				throw new RuntimeException( 'PHPStan does not directly select maintained PHP: ' . $ran_starter_plugin_relative );
			}
			++$ran_starter_plugin_count;
		}
	}
	if ( 0 === $ran_starter_plugin_count || array() === $ran_starter_plugin_snippets ) {
		throw new RuntimeException( 'Maintained PHP or embedded PHP discovery was empty.' );
	}
	$ran_starter_plugin_temporary = sys_get_temp_dir() . '/ran-starter-analysis-' . bin2hex( random_bytes( 8 ) );
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir -- Create the private owned temporary analysis layout; it is outside distributed source and removed after the child exits.
	if ( ! mkdir( $ran_starter_plugin_temporary, 0700 ) ) {
		throw new RuntimeException( 'Cannot create embedded analysis directory.' );
	}
	try {
		$ran_starter_plugin_paths = array();
		foreach ( $ran_starter_plugin_snippets as $ran_starter_plugin_index => $ran_starter_plugin_snippet ) {
			$ran_starter_plugin_extracted = $ran_starter_plugin_temporary . '/snippet-' . $ran_starter_plugin_index . '.php';
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Analyze the exact extracted executable snippet bytes; never execute their bodies or repair malformed inputs.
			if ( false === file_put_contents( $ran_starter_plugin_extracted, $ran_starter_plugin_snippet['source'] ) ) {
				throw new RuntimeException( 'Cannot write embedded analysis source.' );
			}
			$ran_starter_plugin_paths[] = $ran_starter_plugin_extracted;
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Emit CLI origin mapping for actual checker diagnostics, not HTML.
			echo 'Embedded PHP: ' . $ran_starter_plugin_snippet['origin'] . ' -> ' . basename( $ran_starter_plugin_extracted ) . PHP_EOL;
		}
		$ran_starter_plugin_status = 0;
		// Each snippet has its own runtime symbol world; one process cannot satisfy another process's missing declarations.
		foreach ( $ran_starter_plugin_paths as $ran_starter_plugin_path ) {
			$ran_starter_plugin_configuration  = "includes:\n\t- " . json_encode( $ran_starter_plugin_root . '/vendor/szepeviktor/phpstan-wordpress/extension.neon', JSON_THROW_ON_ERROR ) . "\nparameters:\n\tlevel: 8\n\tphpVersion: 80400\n\ttreatPhpDocTypesAsCertain: false\n\tpaths:\n";
			$ran_starter_plugin_configuration .= "\t\t- " . json_encode( $ran_starter_plugin_path, JSON_THROW_ON_ERROR ) . "\n";
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Write only the private checker configuration containing the unchanged locked WordPress extension and exact extracted paths.
			if ( false === file_put_contents( $ran_starter_plugin_temporary . '/phpstan.neon', $ran_starter_plugin_configuration ) ) {
				throw new RuntimeException( 'Cannot write embedded analysis configuration.' );
			}
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_proc_open -- Run the locked checker with an argument vector and propagate its real child exit status.
			$ran_starter_plugin_process = proc_open( array( PHP_BINARY, $ran_starter_plugin_root . '/vendor/bin/phpstan', 'analyse', '--configuration=' . $ran_starter_plugin_temporary . '/phpstan.neon', '--no-progress', '--memory-limit=1G' ), array( STDIN, STDOUT, STDERR ), $ran_starter_plugin_pipes );
			if ( ! is_resource( $ran_starter_plugin_process ) ) {
				throw new RuntimeException( 'Cannot start embedded PHP analysis.' );
			}
			if ( 0 !== proc_close( $ran_starter_plugin_process ) ) {
				$ran_starter_plugin_status = 1;
			}
		}
	} finally {
		foreach ( new DirectoryIterator( $ran_starter_plugin_temporary ) as $ran_starter_plugin_entry ) {
			if ( ! $ran_starter_plugin_entry->isDot() ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Remove only files in the owned private extraction directory after the checker child has exited.
				unlink( $ran_starter_plugin_entry->getPathname() );
			}
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- Remove the now-empty owned private extraction directory.
		rmdir( $ran_starter_plugin_temporary );
	}
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Emit CLI population evidence, not HTML.
	echo 'Maintained PHP direct analysis: ' . $ran_starter_plugin_count . ' files; embedded PHP: ' . count( $ran_starter_plugin_snippets ) . ' snippets.' . PHP_EOL;
	exit( 0 === $ran_starter_plugin_status ? 0 : 1 );
} catch ( Throwable $ran_starter_plugin_error ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Emit standalone checker diagnostics to STDERR, not rendered HTML.
	fwrite( STDERR, $ran_starter_plugin_error->getMessage() . PHP_EOL );
	exit( 1 );
}
