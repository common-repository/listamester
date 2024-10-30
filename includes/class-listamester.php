<?php
/**
 * Listamester class.
 *
 * @package listamester
 */
class Listamester {
	const LISTAMESTER_OUIBOUNCE_JS  = 'https://listamester.hu/static/js/ouibounce.js';
	const LISTAMESTER_GET_FORM_CODE = 'https://listamester.hu/restApi/FormBuilder/GetFormCode/';
	const LISTAMESTER_FORMS_FE      = 'http://listamester.hu/forms/fe/';
	const LISTAMESTER_STATIC        = 'http://static.listamester.hu/';

	private $listamester_scripts = array();

	/**
	 * initializing all the function and hook
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_shortcode( 'listamester', array( $this, 'shortcode' ) );
		add_action( 'wp_footer', array( $this, 'add_scripts_to_footer' ) );
	}

	/**
	 * Initialize
	 */
	function init() {
		load_plugin_textdomain( 'listamester', false, basename( dirname( __DIR__ ) ) . '/languages' );

		if ( ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		$action       = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
		$resourceType = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
		if ( ! ( $action === 'get_resource' && ( $resourceType === 'static' || $resourceType === 'form.html' || $resourceType === 'form.js' ) ) ) {
			return;
		}
		add_action( "wp_ajax_{$action}", array( $this, 'get_resource' ) );
		add_action( "wp_ajax_nopriv_{$action}", array( $this, 'get_resource' ) );
	}

	/**
	 * getting connected to the listamester
	 */
	public function shortcode( $atts ) {
		$id     = $atts['id'];
		$mode   = $atts['mode'];
		$output = '';

		switch ( $mode ) {
			/*case 'js':
			default:
				$args      = array(
					'action'  => 'get_resource',
					'type'    => 'form.js',
					'form_id' => $id,
				);
				$scriptUrl = add_query_arg( $args, admin_url( 'admin-ajax.php' ) );
				$scriptek   = '<script type="text/javascript" src="' . esc_url( $scriptUrl ) . '"></script>';
				//$scriptek = $this->get_all_data_with_curl( $scriptUrl );

				array_push($this->listamester_scripts, $scriptek);*/
			case 'js':
			default:
				$args      = array(
					'action'  => 'get_resource',
					'type'    => 'form.js',
					'form_id' => $id,
				);
				$scriptUrl = add_query_arg( $args, admin_url( 'admin-ajax.php' ) );
				$output   = '<script type="text/javascript" src="' . esc_url( $scriptUrl ) . '"></script>';
			case 'html':
				$data = get_transient( 'listamester_form_' . $id );
				if ( $data === false ) {
					$url  = self::LISTAMESTER_GET_FORM_CODE . $id;
					$resp = wp_remote_get( $url );
					if ( is_wp_error( $resp ) || 200 != wp_remote_retrieve_response_code( $resp ) ) {
        $output = 'Hiba történt az űrlap lekérése során. Kérjük, próbálkozzon újra később.';
        break;
} else {
					// check reponse
					$data = $resp['body'];
					// json-check data
					set_transient( 'listamester_form_' . $id, $data, 4 * HOUR_IN_SECONDS );
				}
				}

				$formdata = json_decode( $data, true );
				$output   = '<style scoped>' . $formdata['css'] . '</style>' . $formdata['html'] . $formdata['javascript'];
				break;
			case 'iframe':
				$args      = array(
					'action'  => 'get_resource',
					'type'    => 'form.html',
					'form_id' => $id,
				);
				$scriptUrl = add_query_arg( $args, admin_url( 'admin-ajax.php' ) );
				$width     = $this->normalizeSizeValue($atts['width']);
				$height    = $this->normalizeSizeValue($atts['height']);
				$stylePars  = 'style="border: 0;';
				if ($width) $stylePars .= 'width: ' . $width . ';';
				if ($height) $stylePars .= 'height: ' . $height . ';';
				$stylePars .= '"';
				$output    = '<iframe src="' . esc_url( $scriptUrl ) . '" ' . $stylePars . '></iframe>';
				break;
			case 'popup':
				$output = '<script type="text/javascript">var LMisPopup' . $id . '=true;</script>';
		}
		return $output;
	}

	public function add_scripts_to_footer() {
		if (is_array($this->listamester_scripts) && !empty($this->listamester_scripts)) {
			foreach ($this->listamester_scripts as $script) {
				echo $script;
			}
		}
	}

	/*public function get_all_data_with_curl( $script_url ) {
		$url = $script_url;
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_ENCODING, "UTF-8" );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 120 );
		curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 120 );
		curl_setopt($curl, CURLOPT_URL, $url);
		$data = curl_exec($curl);
		curl_close($curl);

		$data = $this->findAllMatching( $data );
		return $data;
	}
/*
	public function findAllMatching( $data )
	{
		$new_data = "<script type='text/javacript'>$data</script>";

		preg_match_all("!<script.*>.*</script>!Uis", $data, $scripts);

		if (isset($scripts) && !empty($scripts))
		{
			foreach ($scripts as $script)
			{
				$new_temp_data['scripts'] = $script;
			}

			if (isset($new_temp_data))
			{
				foreach ($new_temp_data['scripts'] as $js) {
					if (strstr($js, "src", true)) {
						$js_src_data[] = $js;
					} else {
						$js_replaced = str_replace('<script>', "", $js);
						$js = str_replace('</script>', "", $js_replaced);
						$js_data[] = $js;
					}
				}
				if (isset($js_src_data) && isset($js_data)) {
					$new_data['scripts']['js'] = $js_src_data;
					$implode_js = implode("", $js_data);
					array_push($new_data['scripts']['js'], '<script>' . $implode_js . '</script>');
				}
			}
		}*/ /*
		return $new_data;
	}*/

	private function normalizeSizeValue($x) {
		if (!$x) return '';
		$lastChar = substr($x, -1);
		if (is_numeric($lastChar)) return $x . 'px';
		return $x;
	}

	/**
	 * Get resource.
	 */
	public function get_resource() {
		$resourceType = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
		$resourcePath = '';
		switch ( $resourceType ) {
			case 'static':
				$resourcePath  = filter_input( INPUT_GET, 'resource_path', FILTER_SANITIZE_STRING );
				$url           = self::LISTAMESTER_STATIC . $resourcePath;
				$transientName = 'listamester_' . $resourcePath;
				break;
			case 'form.html':
			case 'form.js':
				$formId = (int) filter_input( INPUT_GET, 'form_id', FILTER_SANITIZE_NUMBER_INT );
				if ( $formId <= 0 ) {
					return;
				}
				$formType      = substr( $resourceType, 5 );
				$url           = $this->id2path( $formId, $formType );
				$transientName = 'listamester_' . $formType . '_' . $formId;
				break;
		}
		$data = get_transient( $transientName );
		if ( $data === false ) {
			$resp = wp_remote_get( $url );
			// check
			$data = $resp['body'];
			if ( $resourceType === 'form.js' ) {
				$args      = array(
					'action'        => 'get_resource',
					'type'          => 'static',
					'resource_path' => 'js%2Fouibounce.js',
				);
				$scriptUrl = add_query_arg( $args, admin_url( 'admin-ajax.php' ) );
				$data      = str_replace( self::LISTAMESTER_OUIBOUNCE_JS, $scriptUrl, $data );
			}
			set_transient( $transientName, $data, 4 * HOUR_IN_SECONDS );
		}

		if ( $resourceType === 'form.js' || substr( $resourcePath, -3 ) === '.js' ) {
			header( 'Content-Type: application/javascript; charset=UTF-8' );
			// set cache headers
		}
		echo $data;
		exit();
	}

	/**
	 * Convert ID to path.
	 *
	 * @param int $id
	 * @param string $extension
	 * @return string
	 */
	private function id2path( $id, $extension ) {
		$fname  = sprintf( '%d.%s', $id, $extension );
		$path   = self::LISTAMESTER_FORMS_FE;
		$length = strlen( $fname ) - ( strlen( $extension ) + 2 );
		for ( $i = 0; $i < $length; $i++ ) {
			$d     = substr( $fname, $i, 1 );
			$path .= $d . '/';
		}

		return $path . $fname;
	}
}
