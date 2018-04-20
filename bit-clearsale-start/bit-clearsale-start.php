<?php
/**
 * Plugin Name: ClearSale Start
 * Plugin URI: https://bitcolor.com.br/bit-clearsale-start
 * Description: Integração do Clearsale Start para Woocommerce.
 * Version: 1.0
 * Author: Rodrigo Portillo
 * Author URI: https://www.velhobit.com.br
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('ClearsaleStart')) :
	class ClearsaleStart
	{
		function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
            add_action('admin_menu', array($this, 'register_menu_page'));
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        	add_action( 'admin_init', array( $this, 'page_init' ) );
		}
		
		function register_menu_page() {
            add_menu_page('ClearSale', 'ClearSale Start', 'manage_options', 'bit-clearsale-start', 'get_options', plugins_url('bit-clearsale-start/assets/images/clearsale_wp.png'), 69.9);
        }
		/**
		 * Add options page
		 */
		public function add_plugin_page()
		{
			add_options_page(
				'Clearsale', 
				'Clearsale Start', 
				'manage_options', 
				'bit-clearsale-start', 
				array( $this, 'create_admin_page' )
			);
		}

		/**
		 * Options page callback
		 */
		public function create_admin_page()
		{
			// Set class property
			$this->options = get_option( 'bit_clearsale_start' );
			?>
			<div class="wrap">
				<h1 style="margin-bottom: 0;"><img src="<?php echo plugins_url('bit-clearsale-start/assets/images/clearsale_wp.png')?>"/><br/>Clearsale Start</h1>
				<h2 style="font-size: 13px;margin-top:0;">Criado by Velhobit (Rodrigo Portillo)</h2>
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'bit_clearsale_start_group' );
					do_settings_sections( 'bit-clearsale-start' );
					submit_button();
				?>
				</form>
			</div>
			<?php
		}

		/**
		 * Register and add settings
		 */
		public function page_init()
		{        
			register_setting(
				'bit_clearsale_start_group', // Option group
				'bit_clearsale_start', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);

			add_settings_section(
				'bit_clearsale_start_settings_', // ID
				'Configurações Clearsale Start', // Title
				array( $this, 'print_section_info' ), // Callback
				'bit-clearsale-start' // Page
			);  

			add_settings_field(
				'hom_token', // ID
				'Homologação', // Title 
				array( $this, 'hom_callback' ), // Callback
				'bit-clearsale-start', // Page
				'bit_clearsale_start_settings_' // Section           
			);      

			add_settings_field(
				'prod_token', 
				'Produção', 
				array( $this, 'prod_callback' ), 
				'bit-clearsale-start', 
				'bit_clearsale_start_settings_'
			);

			add_settings_field(
				'is_hom', 
				'Ativar Homologação?', 
				array( $this, 'act_hom_callback' ), 
				'bit-clearsale-start', 
				'bit_clearsale_start_settings_'
			);
		}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public function sanitize( $input )
		{
			$new_input = array();
			if( isset( $input['hom_token'] ) )
				$new_input['hom_token'] = sanitize_text_field( $input['hom_token'] );

			if( isset( $input['prod_token'] ) )
				$new_input['prod_token'] = sanitize_text_field( $input['prod_token'] );

			if( isset( $input['act_hom'] ) ){
				$new_input['act_hom'] = true;
			}else{
				$new_input['act_hom'] = false;
			}
				

			return $new_input;
		}
		
		/** 
		 * Print the Section text
		 */
		public function print_section_info()
		{
			print 'Insira os tokens enviados por e-mail pela Clearsale:';
		}


		public function hom_callback()
		{
			printf(
				'<input type="text" style="width:250px" id="hom_token" name="bit_clearsale_start[hom_token]" value="%s" />',
				isset( $this->options['hom_token'] ) ? esc_attr( $this->options['hom_token']) : ''
			);
		}

		public function prod_callback()
		{
			printf(
				'<input type="text" style="width:250px" id="prod_token" name="bit_clearsale_start[prod_token]" value="%s" />',
				isset( $this->options['prod_token'] ) ? esc_attr( $this->options['prod_token']) : ''
			);
		}

		public function act_hom_callback()
		{
			
			if(isset( $this->options['act_hom'] )){
				if (esc_attr( $this->options['act_hom'])) {
					$checked = ' checked="checked" ';
				}else{
					$checked = ' ';
				}
			}else{
				$checked = ' checked="checked" ';
			}
			printf(
				'<input type="checkbox" id="act_hom" name="bit_clearsale_start[act_hom]" value="true" %s/>',
				$checked
			);
		}
		
		/**
		Adicionar a tela admin do pedido
		**/
		public function register_metabox() {
			add_meta_box(
				'bit_clearsale_start',
				'ClearSale Start',
				array( $this, 'metabox_content' ),
				'shop_order',
				'side',
				'high'
			);
		}
		
		public function metabox_content( $post ) {
			include_once dirname( __FILE__ ) . '/html-meta-box.php';
		}
	}

if( is_admin() )
    $clearsale_start = new ClearsaleStart();

endif;