/*
 * Relies on there being a variable named
 * ee_job_response localized
 * @var array $ee_job_response {
 *	@type string $job_id
 * }
 * @var array eei18n {
 *	@type string ajax_url
 * }
 */


jQuery(document).ready(function() {

    var runner = new EE_BatchRunner(
        // continue_url
        eei18n.ajax_url,
        // continue_data
        {
            'action' : 'espresso_batch_continue',
            'ee_admin_ajax' : eei18n.is_admin,
			'ee_front_ajax' : ! eei18n.is_admin
        },
        // continue_callback
        ee_support_download_file,
        // cleanup_url
        eei18n.ajax_url,
        // cleanup_data
        {
            'action' : 'espresso_batch_cleanup',
            'ee_admin_ajax' : eei18n.is_admin,
			'ee_front_ajax' : ! eei18n.is_admin
        },
        // cleanup_callback
		function( response, data, xhr ) {
                //redirect them as if this page didn't exist
                //(so clicking "back" won't get them here)
                window.location.replace( ee_job_i18n.return_url );
			}
    );
    runner.set_job_id( ee_job_response.job_id );
	runner.set_progress_bar_div( 'batch-progress' );
	runner.set_progress_area( 'progress-area', 'append' );
	runner.handle_continue_response( { 'data' : ee_job_response } );



	/**
	 * Checks for once the download is complete,
	 * then gets the user to download the temp file
	 * then cleans up the job
	 * @param response array
	 * @param status string
	 * @param {type} xhr
	 * @returns void
	 */
	function ee_support_download_file( response, status, xhr ) {
		//once we're all done, download the file
		if( response.data.status == 'complete' && response.data.file_url != '' ) {
			jQuery('#message-area').html( ee_job_i18n.download_and_redirecting );
            //tell the browser to download the file. But because the file gets downloaded,
            //the user doesn't actually leave. So we don't need to clean up JUST yet
            //first, let's just download the file
			window.onbeforeunload = null;
			window.location.href=response.data.file_url;
			//ok, once it's started downloading, we can restore the onbeforeunload callback
            //it's possible someone might navigate away before the automatic redirect
            setTimeout(function() {
                runner.setup_clean_up_on_page_exit();
            }, 500 );
			//wait a few seconds for the file to download then start
            //cleaning up and redirecting the user
			setTimeout(function() {
				runner.cleanup_job();
			}, 2000 );
		}
	}



});