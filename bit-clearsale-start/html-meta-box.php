<?php
/**
 * Meta box - Analisys
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>


<?php
$this->options = get_option( 'bit_clearsale_start' );

//Verificar se está em modo de homologação
if(isset( $this->options['act_hom']) && esc_attr( $this->options['act_hom'])) {
	$token_clearsale = esc_attr( $this->options['hom_token']);
	$url_clearsale = "https://homolog.clearsale.com.br/start/Entrada/EnviarPedido.aspx";
	echo "<div style='background-color:orangered;text-align:center;color:white;padding:5px;'>Homologação</div>";
}else{
	$token_clearsale = esc_attr( $this->options['prod_token']);
	$url_clearsale = "https://www.clearsale.com.br/start/Entrada/EnviarPedido.aspx";
}

echo "<form></form>";
// Não sei porque motivo, mas se eu printo um form antes, eu posso imprimir o form no Metabox
// Ficar de olho em versões futuras


//Get Order Details
$order = wc_get_order($post->ID);

include_once dirname( __FILE__ ) . '/bit-clearsale-functions.php';

$order_credit_card_masked =  getCreditCardNumberMasked($post->ID);

if(!$order_credit_card_masked){
	$order_credit_card_masked = "";	
	$order_bandeira = "";
	$card_bin = "";
	$card_fim = "";
}else{
	$detalhes_cartao = valida_cartao( $order_credit_card_masked, false );
	$order_bandeira = $detalhes_cartao[2];
	
	$content_ = explode("****", $order_credit_card_masked);
	$card_bin = substr($content[0], -6);
	$card_fim = substr($content[1], 0, 4);
}

$order_customer_name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();

$shipping_customer_name = $order->get_shipping_first_name() . " " . $order->get_shipping_last_name();

//Extrair DDD do Telefone
$phone1 = $order->get_billing_phone();
$phone1DDD = substr($phone1, 1, 2);
$phone1Number = substr($phone1, 4);

$celphone1 = $order->get_meta("_billing_cellphone");
$celphone1DDD = substr($celphone1, 1, 2);
$celphone1Number = substr($celphone1, 4);
if($phone1 == ""){
	$phone1DDD = $celphone1DDD;
	$phone1Number = $celphone1Number;
}

//Try to get Credit Card By Order Note
?>

<iframe class="bit_clearsale_iframe" name="bitClearsaleIframe">
</iframe>
<form class="bit_clearsale_form" target="bitClearsaleIframe" method="POST" action="<?php echo $url_clearsale; ?>">
	
	<input type="hidden" name="CodigoIntegracao" value="<?php echo $token_clearsale; ?>"/>
	
	<div class="clearsale_field">
		<input name="PedidoID" type="text" value="<?php echo $post->ID; ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Data" type="text" value="<?php echo $post->post_date; ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="IP" type="text" value="<?php echo $order->customer_ip_address; ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Total" type="text" value="<?php echo $order->total; ?>" contenteditable="false">
	</div>
	<div>
		<select name="TipoPagamento" id="TipoPagamento" value="<?php echo checkPaymentMethod($order->payment_method_title); ?>" required>
			<option value="1">Cartão de Crédito
</option>
			<option value="2">Boleto Bancário</option>
			<option value="3">Débito Bancário</option>
			<option value="4">Débito Bancário – Dinheiro</option>
			<option value="5">Débito Bancário – Cheque</option>
			<option value="6">Transferência Bancária</option>
			<option value="7">Sedex a Cobrar</option>
			<option value="8">Cheque</option>
			<option value="9">Dinheiro</option>
			<option value="10">Financiamento</option>
			<option value="11">Fatura</option>
			<option value="12">Cupom</option>
			<option value="13">Multicheque</option>
			<option value="14">Outros</option>
		</select>
		<?php
		if(checkPaymentMethod($order->payment_method_title) == "14"):
		?>
		<small style="color:orangered;">Forma de pagamento não identificada.</br>Por favor, selecione manualmente.</small>
		<?php
		endif;
		?>
		<div class="clearsale_cartao_fields">
			<?php if(empty($order_bandeira)):?>
				<select name="Tipo_Cartao">
					<option value="3">Visa</option>
					<option value="2">Mastercard</option>
					<option value="1">Diners</option>
					<option value="5">AMEX</option>
					<option value="7">Aura</option>
					<option value="8">Carrefour</option>
					<option value="6">Hipercard</option>
					<option value="4">Outro</option>
				</select>
			<?php else:?>
				<input name="Tipo_Cartao" type="hidden" value="<?php echo $order_bandeira; ?>">
			<?php endif;?>
			
			<input name="Cartao_Bin" id="Cartao_Bin" type="text" value="<?php echo $card_bin; ?>" style="display:none">
			<input name="Cartao_Fim" id="Cartao_Fim" type="text" value="<?php echo $card_fim; ?>" style="display:none">
			<label><b>Nº Mascarado do Cartão</b> (se tiver):</label>
			<input name="Cartao_Numero_Mascarado" id="Cartao_Numero_Mascarado" type="text" value="<?php echo $order_credit_card_masked; ?>" placeholder="Exemplo: 555555****4444">
		</div>
	</div>
	
	<!--Billing-->
	<div class="clearsale_field">
		<input name="Cobranca_Nome" type="text" value="<?php echo $order_customer_name; ?>" contenteditable="false">
	</div><!--
	<div class="clearsale_field">
		<input name="Cobranca_Nascimento" type="text" value="" contenteditable="false">
	</div>-->
	<div class="clearsale_field">
		<input name="Cobranca_Email" type="text" value="<?php echo $order->get_billing_email(); ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Documento" type="text" value="<?php  echo preg_replace('/\D/', '',$order->get_meta("_billing_cpf")); echo preg_replace('/\D/', '',$order->get_meta("_billing_cnpj")); ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Logradouro" type="text" value="<?php echo $order->get_billing_address_1();?>" contenteditable="false">
	</div>
	<!--<div class="clearsale_field">
		<input name="Cobranca_Logradouro_Numero" type="text" value="" contenteditable="false">
	</div>-->
	<div class="clearsale_field">
		<input name="Cobranca_Logradouro_Complemento" type="text" value="<?php echo $order->get_billing_address_2();?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Bairro" type="text" value="<?php echo $order->get_meta("_shipping_neighborhood"); ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Cidade" type="text" value="<?php echo $order->get_billing_city();?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Estado" type="text" value="<?php echo $order->get_billing_state();?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_CEP" type="text" value="<?php echo preg_replace('/\D/', '',$order->get_billing_postcode());?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Pais" type="text" value="<?php echo $order->get_billing_country();?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_DDD_Telefone_1" type="text" value="<?php echo $phone1DDD; ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Telefone_1" type="text" value="<?php echo preg_replace('/\D/', '',$phone1Number); ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_DDD_Celular" type="text" value="<?php echo $celphone1DDD; ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Cobranca_Celular" type="text" value="<?php echo preg_replace('/\D/', '',$celphone1Number); ?>" contenteditable="false">
	</div>
	
	<!--Shipping-->
	<div class="clearsale_field">
		<input name="Entrega_Nome" type="text" value="<?php echo $shipping_customer_name; ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Entrega_Logradouro" type="text" value="<?php echo $order->get_shipping_address_1();?>" contenteditable="false">
	</div>

	<!--<div class="clearsale_field">
		<input name="Entrega_Logradouro_Numero" type="text" value="" contenteditable="false">
	</div>-->
	<div class="clearsale_field">
		<input name="Entrega_Logradouro_Complemento" type="text" value="<?php echo $order->get_shipping_address_2();?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Entrega_Bairro" type="text" value="<?php echo $order->get_meta("_shipping_neighborhood"); ?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Entrega_Cidade" type="text" value="<?php echo $order->get_shipping_city();?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Entrega_Estado" type="text" value="<?php echo $order->get_shipping_state();?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Entrega_CEP" type="text" value="<?php echo preg_replace('/\D/', '',$order->get_shipping_postcode());?>" contenteditable="false">
	</div>
	<div class="clearsale_field">
		<input name="Entrega_Pais" type="text" value="<?php echo $order->get_shipping_country();?>" contenteditable="false">
	</div>
	
	<!--ITEMS-->
	<?php
	//Listar items
	$n_item = 1;
	foreach($order->get_items() as $item):
	?>
	<div class="clearsale_field">
		<input name="Item_ID_<?php echo $n_item;?>" type="text" value="<?php echo $item->get_product_id();?>" contenteditable="false">
		<input name="Item_Nome_<?php echo $n_item;?>" type="text" value="<?php echo $item->get_name();?>" contenteditable="false">
		<input name="Item_Qtd_<?php echo $n_item;?>" type="text" value="<?php echo $item->get_quantity();?>" contenteditable="false">
		<input name="Item_Valor_<?php echo $n_item;?>" type="text" value="<?php echo $item->get_total();?>" contenteditable="false">
		<input name="Item_Categoria_<?php echo $n_item;?>" type="text" value="<?php echo $item->get_type();?>" contenteditable="false">
	</div>
	<?php
	$n_item++;
	
	endforeach;
	?>
	<button type="submit">Analisar com Start<img src="<?php echo plugins_url('bit-clearsale-start/assets/images/clearsale_wp.png')?>"/></button>
</form>
<script>
	jQuery(".bit_clearsale_form").submit(function(){
		jQuery(".bit_clearsale_iframe").addClass("show");
	});
	
	jQuery("#TipoPagamento").change(function(){
		if(jQuery("#TipoPagamento").val() == 1 || jQuery("#TipoPagamento").val() == 3){
			jQuery(".clearsale_cartao_fields").addClass("show");
			jQuery(".clearsale_cartao_fields").attr("type","text");
		}else{
			jQuery(".clearsale_cartao_fields").removeClass("show");
			jQuery(".clearsale_cartao_fields").attr("type","hidden");
		}
	});
	
	jQuery("#TipoPagamento").trigger("change");
	
	jQuery("#Cartao_Numero_Mascarado").keyup(function(){
		if(jQuery("#Cartao_Numero_Mascarado").val().length == 14){
			var valores = jQuery("#Cartao_Numero_Mascarado").val().split("****");
			jQuery("#Cartao_Bin").val(valores[0]);
			jQuery("#Cartao_Fim").val(valores[1]);
			jQuery(this).removeClass("red");
		}else{
			if(jQuery("#Cartao_Numero_Mascarado").val().length > 0){
				jQuery(this).addClass("red");
			}else{
				jQuery(this).removeClass("red");
			}
		}
	});
</script>
<style>
	.bit_clearsale_iframe{
		border: 0;
		padding: 0;
		max-height: 0;
		transition: all .5s;
		margin-left: -12px;
	}
	
	.bit_clearsale_iframe.show{
		max-height: 100px;
	}
	
	.bit_clearsale_form .clearsale_field{
		display: none;
	}
	
	.bit_clearsale_form .clearsale_cartao_fields{
		display: none;
	}
	.bit_clearsale_form .clearsale_cartao_fields.show{
		display: block;
	}
	
	.bit_clearsale_form select,
	.bit_clearsale_form input{
		width: 100%;
	}
	
	.bit_clearsale_form button{
		margin-top: 10px;
		background-color: #bb36de;
		vertical-align: middle;
		color: white;
		text-transform: uppercase;
		border: none;
		border-radius: 5px;
		width: 100%;
		box-sizing: border-box;
		padding: 10px 5px;
		display: flex;
		cursor: pointer;
    	justify-content: center;
	}
	
	.bit_clearsale_form button img{
		margin-left: 5px;
		margin-top: -5px;
	}
	
	#Cartao_Numero_Mascarado.red{
		background-color: #FBE27C;
	}
</style>
<?php
?>