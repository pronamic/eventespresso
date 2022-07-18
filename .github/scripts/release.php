<?php

/**
 * Functions.
 */
function line( $text = '' ) {
	echo $text, PHP_EOL;
}

function run( $command, &$result_code = null ) {
	line( $command );

	$last_line = system( $command, $result_code );

	line();

	return $last_line;
}

/**
 * Event Espresso license key.
 */
$event_espresso_license_key    = getenv( 'EVENT_ESPRESSO_LICENSE_KEY' );
$event_espresso_license_domain = getenv( 'EVENT_ESPRESSO_LICENSE_DOMAIN' );

if ( empty( $event_espresso_license_key ) ) {
	echo 'Event Espresso license key not defined in `EVENT_ESPRESSO_LICENSE_KEY` environment variable.';

	exit( 1 );
}

if ( empty( $event_espresso_license_domain ) ) {
	echo 'MemberPress license domain not defined in `EVENT_ESPRESSO_LICENSE_DOMAIN` environment variable.';

	exit( 1 );
}

/**
 * Request info.
 */
line( '::group::Check Event Espresso' );

$url = 'https://eventespresso.com?pu_request_plugin=event-espresso-core-reg&pu_checking_for_updates=1';

$data = run(
	sprintf(
		'curl %s',
		escapeshellarg( $url )
	)
);

$result = json_decode( $data );

if ( ! is_object( $result ) ) {
	throw new Exception(
		sprintf(
			'Unknow response from: %s.',
			$url 
		)
	);

	exit( 1 );
}

$version = $result->version;

$url = 'https://eventespresso.com/?' . http_build_query(
	[
		'pu_request_plugin' => 'event-espresso-core-reg',
		'pu_get_download'   => '1',
		'site_domain'       => $event_espresso_license_domain,
		'pu_plugin_api'     => $event_espresso_license_key,
	],
	'',
	'&'
);

line( 'Version: ' . $version );

line( '::endgroup::' );

/**
 * Files.
 */
$work_dir = tempnam( sys_get_temp_dir(), '' );

unlink( $work_dir );

mkdir( $work_dir );

$archives_dir = $work_dir . '/archives';
$plugins_dir  = $work_dir . '/plugins';

mkdir( $archives_dir );
mkdir( $plugins_dir );

$plugin_dir = $plugins_dir . '/event-espresso-core-reg';

$zip_file = $archives_dir . '/event-espresso-core-reg-' . $version . '.zip';

/**
 * Download ZIP.
 */
line( '::group::Download Event Espresso' );

run(
	sprintf(
		'curl %s --output %s',
		escapeshellarg( $url ),
		$zip_file
	)
);

line( '::endgroup::' );

/**
 * Unzip.
 */
line( '::group::Unzip Event Espresso' );

run(
	sprintf(
		'unzip %s -d %s',
		escapeshellarg( $zip_file ),
		escapeshellarg( $plugins_dir )
	)
);

line( '::endgroup::' );

/**
 * Synchronize.
 * 
 * @link http://stackoverflow.com/a/14789400
 * @link http://askubuntu.com/a/476048
 */
line( '::group::Synchronize Event Espresso' );

run(
	sprintf(
		'rsync --archive --delete-before --exclude=%s --exclude=%s --exclude=%s --verbose %s %s',
		escapeshellarg( '.git' ),
		escapeshellarg( '.github' ),
		escapeshellarg( 'composer.json' ),
		escapeshellarg( $plugin_dir . '/' ),
		escapeshellarg( '.' )
	)
);

line( '::endgroup::' );

/**
 * Git user.
 * 
 * @link https://github.com/roots/wordpress/blob/13ba8c17c80f5c832f29cf4c2960b11489949d5f/bin/update-repo.php#L62-L67
 */
run(
	sprintf(
		'git config user.email %s',
		escapeshellarg( 'info@eventespresso.com' )
	)
);

run(
	sprintf(
		'git config user.name %s',
		escapeshellarg( 'Event Espresso' )
	)
);

/**
 * Git commit.
 * 
 * @link https://git-scm.com/docs/git-commit
 */
run( 'git add --all' );

run(
	sprintf(
		'git commit --all -m %s',
		escapeshellarg(
			sprintf(
				'Updates to %s',
				$version
			)
		)
	)
);

run( 'git config --unset user.email' );
run( 'git config --unset user.name' );

run( 'gh auth status' );

run( 'git push origin main' );

/**
 * GitHub release view.
 */
run(
	sprintf(
		'gh release view %s',
		$version
	),
	$result_code
);

$release_not_found = ( 1 === $result_code );

/**
 * Notes.
 */
$notes = '';

/**
 * GitHub release.
 * 
 * @todo https://memberpress.com/wp-json/wp/v2/pages?slug=change-log
 * @link https://cli.github.com/manual/gh_release_create
 */
if ( $release_not_found ) {
	run(
		sprintf(
			'gh release create %s %s --notes %s',
			$version,
			$zip_file,
			escapeshellarg( $notes )
		)
	);
}

/**
 * Cleanup.
 */
run(
	sprintf(
		'rm -f -R %s',
		escapeshellarg( $work_dir )
	)
);
