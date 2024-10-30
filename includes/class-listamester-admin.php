<?php
/**
 * Listamester administration class.
 *
 * @package listamester
 */
class Listamester_Admin {

	//const LISTAMESTER_AUTH          = 'https://listamester.hu/restApi/pub/Authenticate';
	const LISTAMESTER_AUTH          = 'https://listamester.hu/restApi/pub/Ping';
const LISTAMESTER_GET_ALL_FORMS = 'https://listamester.hu/restApi/FormBuilder/GetAllForms';
	

	/**
	 * Initializing all the function and hook.
	 */
	public function __construct() {
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
	}

	/**
	 * Registering admin menu.
	 */
	public function admin_menu() {
		add_menu_page( __( 'Listamester beállítások', 'listamester' ), __( 'Listamester', 'listamester' ), 'manage_options_cap', 'lm-plugin', null, plugin_dir_url( __DIR__ ) . 'images/lm.png' );
		add_submenu_page( 'lm-plugin', __( 'Űrlapok', 'listamester' ), __( 'Űrlapok', 'listamester' ), 'manage_options', 'lm-forms', array( $this, 'forms' ) );
		add_submenu_page( 'lm-plugin', __( 'Beállítások', 'listamester' ), __( 'Beállítások', 'listamester' ), 'manage_options', 'lm-settings', array( $this, 'settings' ) );
		add_submenu_page( 'lm-plugin', __( 'Frissítés', 'listamester' ), __( 'Frissítés', 'listamester' ), 'manage_options', 'lm-flush', array( $this, 'flush_cache' ) );
	}

	public function settings() {
		?>
		<div class="wrap">
		<h1><?php esc_html_e( 'Listamester beállítások', 'listamester' ); ?></h1>
		<?php esc_html_e( 'Írd be a Felhasználói azonosítódat és az API kulcsot, amit a ListaMester fiókodban, a Beállítások - API (programozói interfész) menüpontban találsz!', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Ha beírtad, kattints a Mentés gombra!', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'A rendszer kiírja: sikeres', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'A kapcsolat létrejött.', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Ha az üzenet: "sikertelen", próbáld újra! Lehetséges, hogy elgépelted, vagy szóköz maradt az e-mail-cím vagy a jelszó előtt / mögött.', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Ezt a kapcsolatot csak egyszer kell beállítanod.', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Bővebb információ itt: https://www.listamester.hu/utmutato-listamesterhez/urlapok/feliratkozasi-urlap-beillesztese-wordpress-honlapba', 'listamester' ); ?>
		<?php
		if ( isset( $_POST['lm_user_id'] ) ) {
			/*
				* OLD
			// check lmid, lmpwd
			$auth_basic = 'Basic ' . base64_encode( $_POST['lmid'] . ':' . $_POST['lmpwd'] );
			$args       = array(
				'headers' => array( 'Authorization' => $auth_basic ),
			);
			*/
			$args       = array(
				'headers' => array( 'Authorization' =>"apikey ".$_POST['lm_user_id'].":".$_POST['lm_api_key']  ),
			);
			$resp       = wp_remote_get( self::LISTAMESTER_AUTH, $args );
									$success    = false;
			if ( ! empty( $resp['body'] ) ) {
				$data = json_decode( $resp['body'], true );
				if ( 'OK' === $data['status'] ) {
					/*
						* OLD
					update_option( 'listamester_lmid', $_POST['lmid'] );
					update_option( 'listamester_lmpwd', $data['pwdHash'] );
					*/
										update_option( 'lm_user_id', $_POST['lm_user_id'] );
					update_option( 'lm_api_key', $_POST["lm_api_key"] );
					$success = true;
				}
			}
			if ( $success ) {
				echo '<br/><span style="font-weight: bold; color: green;">'
					. esc_html__( 'Sikeres', 'listamester' ) . '</span>';
			} else {
				echo '<br/><span style="font-weight: bold; color: red;">'
					. esc_html__( 'Sikertelen azonosítás!', 'listamester' ) . '</span>';
			}
		}
		?>
		<form method="post">
		<table class="form-table">
		<tbody>
			<tr>
				<th><?php esc_html_e( 'Felhasználói azonosító', 'listamester' ); ?></th>
				<td><input type="text" class="regular-text" name="lm_user_id" value="<?php echo get_option( 'lm_user_id' ); ?>"/></td>
			</tr>
			<tr>
				<th>API kulcs</th>
				<td><input type="password" class="regular-text" name="lm_api_key" value="<?php echo get_option( 'lm_api_key' ); ?>"/></td>
			</tr>
		</tbody>
		</table>

		<input type="submit" class="button button-primary" value="Mentés" />
		</form>
		</div><!-- /.wrap -->
		<?php
	}

	function getAllForms() {
		/*
		$body = sprintf( '{"email":"%s","password":"%s"}', get_option( 'listamester_lmid' ), get_option( 'listamester_lmpwd' ) );
		$args = array(
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'body'    => $body,
		);
		$resp = wp_remote_post( self::LISTAMESTER_GET_ALL_FORMS, $args );
		*/
					$args       = array(
				'headers' => array( 'Authorization' =>"apikey ".get_option('lm_user_id').":".get_option('lm_api_key')  ),
			);
			$resp       = wp_remote_get( self::LISTAMESTER_GET_ALL_FORMS, $args );
					// Check response
		if ( is_wp_error( $resp ) ) {
			$forms = array();
		} else {
			$forms = json_decode( $resp['body'], true );
			if ( false === $forms ) {
				$forms = array();
			}
		}
		/*
		$resp = wp_remote_post( self::LISTAMESTER_GET_ALL_FORMS, $args );
		$forms = array();
		if ( ! empty( $resp['body'] ) ) {
			$forms = json_decode( $resp['body'], true );
				if ( ! is_array( $forms ) ) {
					$forms = array();
				}
		}
		*/
		return $forms;
	}

	public function flush_cache() {
		?>
		<div class="wrap">
		<h1><?php esc_html_e( 'Listamester űrlapok frissítése', 'listamester' ); ?></h1>
		<?php esc_html_e( 'A ListaMesterből lekért űrlapokat a WordPress átmeneti gyorsítótárban tárolja. Az egyes űrlapok 4 óránként frissülnek, ha a ListaMesterben valami változás van.', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Tehát amennyiben a ListaMesterben módosítod és publikálod az egyik űrlapodat, akkor az alap esetben NEM FOG a honlapodon is azonnal frissülni. Ott még egy ideig az űrlap előző változata lesz látható.', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Ha azt szeretnéd, hogy azonnal frissüljenek, akkor az alábbi FRISSÍTÉS feliratú gombra kattints! Ezzel kitakarítod a gyorsítótárat és a következő lekérésnél frissülnek az űrlapok. Tehát frissítés UTÁN az Űrlapok menüpontban le kell kérned valamilyen módban újra a shortcodeokat, és azt, ami ahhoz az űrlaphoz tartozik, amit módosítottál, azt újra be kell illesztened a weboldaladba.', 'listamester' ); ?>
		<br />
		<br />
		<?php
		if ( isset( $_POST['lmflush'] ) ) {
			$forms = $this->getAllForms();
			if ( ! empty( $forms ) ) {
				foreach ( $forms as $form ) {
					if ( empty( $form['id'] ) ) {
						continue;
					}
					delete_transient( 'listamester_form_' . $form['id'] );
					delete_transient( 'listamester_html_' . $form['id'] );
					delete_transient( 'listamester_js_' . $form['id'] );
				}
				echo '<span style="font-weight: bold; color: green;">'
					. esc_html__( 'Gyorsítótár kiürítve.', 'listamester' ) . '</span><br /><br />';
			}
		}
		?>
		<form method="post">
			<input type="submit" class="button button-primary" name="lmflush" value="Frissítés" />
		</form>
		</div><!-- /.wrap -->
		<?php
	}

	public function forms() {
		$forms = $this->getAllForms();
		?>
		<div class="wrap">
		<h1><?php esc_html_e( 'Űrlapok', 'listamester' ); ?></h1>
		<?php esc_html_e( 'Itt találod az összes, a ListaMester fiókodban az új űrlapvarázslóval létrehozott űrlapjaidat.', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'ID = Az űrlap száma', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Név: Az űrlap neve, ahogy elnevezted a ListaMester fiókodban, amikor létrehoztad', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Csoport: Annak a csoportnak a neve, amihez létrehoztad az űrlapot (Figyelem: Egy csoporthoz tartozhat több űrlap is!)', 'listamester' ); ?>
		<br>
		<?php esc_html_e( 'Shortcode: Kód az űrlap beillesztéséhez a honlapodba', 'listamester' ); ?>
		<p>
		<?php esc_html_e( 'A beillesztés módja 4 féle lehet.', 'listamester' ); ?>
		</p>
		<p>
		<?php esc_html_e( 'Ha kezdő WP felhasználó vagy, javasoljuk az iframe módot.', 'listamester' ); ?>
		</p>
		<ul>
			<li>
				<?php
				esc_html_e(
						'Az iframe a legbiztonságosabb módszer, ami egy
aloldalban tölti be az űrlapot. Ez bármilyen környezetben pontosan
ábrázolja az űrlapot, és garantáltan jól működik, mert a ListaMester
kódjait változatlan formában és üres CSS környezetben hozza be.
Hátránya, hogy a köszönő oldal is benne marad az űrlap dobozában
(nem lehet belőle kimenni). Emiatt sokan nem szeretik, de ezt
ajánljuk, ha a javascript beillesztéssel gondok lennének.',
						'listamester'
				);
				?>
			</li>
			<p>
				<?php esc_html_e( 'Ha már gyakorlott WP felhasználó vagy, a következő 3 beillesztési módot is használhatod:', 'listamester' ); ?>
			</p>
			<li>
				<?php
				esc_html_e(
					'A javascript beillesztési mód egyetlen sor kódot illeszt be,
ami a legtöbb esetben jól működik, és szabványos módon illeszti
be az űrlapot. Előfordulhat viszont, hogy bizonyos sablonok
erőszakos CSS szabályai felülírnak valamilyen formázási szabályokat
az űrlapban, és picit változik a külalakja. Ez az ajánlott
beillesztési mód. (az iframe mellett)', 'listamester'
				);
				?>
			</li>
			<li>
				<?php
				esc_html_e(
					'A html beillesztési mód html kódot illeszt az oldalba, annak CSS szabályaival együtt. Ez
nem teljesen szabványos, de jelenleg minden böngészőben működik. Akkor érdemes
használni, ha ragaszkodsz ahhoz, hogy a kódok ott legyenek az oldalban, mert pl. picit
módosítani is szeretnéd. Figyelem! Ez egy veszélyes módszer, mert a szövegszerkesztő
hajlamos a kódokat megváltoztatni. Csak haladó felhasználóknak vagy profi
webdizájnereknek ajánljuk ezt a beillesztési módot. (Ha van az űrlapban bármilyen
recaptcha kód, ez a beillesztési mód NEM használható!)',
					'listamester'
				);
				?>
			</li>
			<li>
				<?php esc_html_e( 'Exit popup. Az űrlap akkor ugrik fel, amikor az olvasó elhagyja az oldalt. Javascript alapú beillesztéssel működik.', 'listamester' ); ?>
			</li>
		</ul>
		<p>
			<?php esc_html_e( 'Ha a ListaMesterben elkészült az űrlapod, mentetted, és publikáltad is, akkor az űrlap
onnantól kezdve éles, és használható. Itt, a WP-ben, először a Frissítés menüpontban
kell a Frissítés gombbal kitakarítanod a WP gyorsítótárát, és utána valamilyen módban
lekérni a shortcodokat.', 'listamester' ); ?>
		</p>
		<p>
			<?php
			esc_html_e(
				'A kiválasztott űrlap beillesztéséhez másold ki az űrlap shortcode-ját
a szögletes zárójelekkel együtt, majd másold be a honlapodon a
tartalomba oda, ahova szeretnéd beilleszteni!',
				'listamester'
			);
			?>
		</p>
		<p>
			<?php esc_html_e( 'Bővebb leírást az űrlapok beillesztéséről WP weboldalakba itt találsz:', 'listamester' ); ?>
		</p>
		<a href="https://www.listamester.hu/utmutato-listamesterhez/urlapok/feliratkozasi-urlap-
beillesztese-wordpress-honlapba" target="_blank">
			<?php esc_html_e( 'https://www.listamester.hu/utmutato-listamesterhez/urlapok/feliratkozasi-urlap-beillesztese-wordpress-honlapba', 'listamester' ); ?>
		</a>
		<p>
			<?php esc_html_e( 'Az összes űrlap beillesztési módról (bemutatás + magyarázat kezdőknek és
webfejlesztőknek is) itt olvashatsz:', 'listamester' ); ?>
		</p>
		<a href="https://www.listamester.hu/utmutato-listamesterhez/urlapok/urlapok-mukodese-es-
beillesztese-technikai-magyarazat-kezdoknek" target="_blank">
			<?php esc_html_e( 'https://www.listamester.hu/utmutato-listamesterhez/urlapok/urlapok-mukodese-es-beillesztese-technikai-magyarazat-kezdoknek', 'listamester' ); ?>
		</a>
		<p>
			Beillesztés módja:&nbsp;
			<select id="lmFormMode">
				<option value="js">JavaScript</option>
				<option value="iframe">Iframe</option>
				<option value="html">HTML</option>
				<option value="popup">Exit popup</option>
			</select>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'listamester' ); ?></th>
					<th><?php esc_html_e( 'Név', 'listamester' ); ?></th>
					<th><?php esc_html_e( 'Csoport', 'listamester' ); ?></th>
					<th><?php esc_html_e( 'Shortcode', 'listamester' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $forms as $form ) {
					if ( empty( $form['id'] ) ) {
						continue;
					}
				echo '<tr><td>' . $form['id'] . '</td><td>' . $form['name'] . '</td><td>' . $form['groupName']
					. '</td><td class="lmFormShortcut"><code>[listamester id="' . $form['id'] . '" mode="js"]</code>';
				echo '<input type="button" value="' . esc_attr__( 'Vágólapra!', 'listamester' )
					. '" class="button button-secondary" style="margin-left: 10px;" onClick="javascript:copyToClipboard('
					. $form['id'] . ')"/></td></tr>';
			}
			?>
			</tbody>
		</table>

		<script>
			jQuery('#lmFormMode').on('change', function () {
				var newOptionVal = this.value;
				jQuery('.lmFormShortcut code').each(function () {
					var oldVal = this.innerHTML;
					var n1 = oldVal.indexOf('="');
					var n2 = oldVal.indexOf('="', n1+1);
					var stylePars = '';
					if (newOptionVal==='iframe') {
						stylePars = ' width="100%" height="100%"';
					}
					var newVal = oldVal.substring(0, n2 + 2) + newOptionVal + '"' + stylePars + ']';
					this.innerHTML = newVal;
				});
			});
			function copyToClipboard(val) {
				var temp = jQuery('<input>');
				jQuery('body').append(temp);
				var mode = jQuery('#lmFormMode option:selected').val();
				var stylePars = '';
				if (mode==='iframe') {
					stylePars = ' width="100%" height="100%"';
				}
				temp.val('[listamester id="' + val + '" mode="' + mode + '"' + stylePars + ']').select();
				document.execCommand('copy');
				temp.remove();
				alert('Shortcode a vágólapon!');
			}
		</script>
		</div><!-- /.wrap -->
		<?php
	}
}
