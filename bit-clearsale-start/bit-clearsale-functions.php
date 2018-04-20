<?php
/**
 * @author Felipe Braz
 * @website https://www.braz.pro.br/blog
 * @param int $cartao
 * @param int $cvc
 * @return array
 * Modificado para Bit Clearsale por Rodrigo Portillo
 */
function valida_cartao( $cartao, $cvc = false ) {
	$cartao = preg_replace( "/[^0-9]/", "", $cartao );
	if ( $cvc )$cvc = preg_replace( "/[^0-9]/", "", $cvc );

	$cartoes = array(
		'visa' => array( 'len' => array( 13, 16 ), 'cvc' => 3 ),
		'mastercard' => array( 'len' => array( 16 ), 'cvc' => 3 ),
		'diners' => array( 'len' => array( 14, 16 ), 'cvc' => 3 ),
		'elo' => array( 'len' => array( 16 ), 'cvc' => 3 ),
		'amex' => array( 'len' => array( 15 ), 'cvc' => 4 ),
		'discover' => array( 'len' => array( 16 ), 'cvc' => 4 ),
		'aura' => array( 'len' => array( 16 ), 'cvc' => 3 ),
		'jcb' => array( 'len' => array( 16 ), 'cvc' => 3 ),
		'hipercard' => array( 'len' => array( 13, 16, 19 ), 'cvc' => 3 ),
	);

	$codigo = 4;

	switch ( $cartao ) {
		case ( bool )preg_match( '/^(636368|438935|504175|451416|636297)/', $cartao ):
			$bandeira = 'elo';
			break;

		case ( bool )preg_match( '/^(606282)/', $cartao ):
			$bandeira = 'hipercard';
			$codigo = 6;
			break;

		case ( bool )preg_match( '/^(5067|4576|4011)/', $cartao ):
			$bandeira = 'elo';
			break;

		case ( bool )preg_match( '/^(3841)/', $cartao ):
			$bandeira = 'hipercard';
			$codigo = 6;
			break;

		case ( bool )preg_match( '/^(6011)/', $cartao ):
			$bandeira = 'discover';
			break;

		case ( bool )preg_match( '/^(622)/', $cartao ):
			$bandeira = 'discover';
			break;

		case ( bool )preg_match( '/^(301|305)/', $cartao ):
			$bandeira = 'diners';
		    $codigo = 1;
			break;

		case ( bool )preg_match( '/^(34|37)/', $cartao ):
			$bandeira = 'amex';
			$codigo = 5;
			break;

		case ( bool )preg_match( '/^(36,38)/', $cartao ):
			$bandeira = 'diners';
			$codigo = 1;
			break;

		case ( bool )preg_match( '/^(64,65)/', $cartao ):
			$bandeira = 'discover';
			break;

		case ( bool )preg_match( '/^(50)/', $cartao ):
			$bandeira = 'aura';
			$codigo = 7;
			break;

		case ( bool )preg_match( '/^(35)/', $cartao ):
			$bandeira = 'jcb';
			break;

		case ( bool )preg_match( '/^(60)/', $cartao ):
			$bandeira = 'hipercard';
			$codigo = 6;
			break;

		case ( bool )preg_match( '/^(4)/', $cartao ):
			$bandeira = 'visa';
			$codigo = 3;
			break;

		case ( bool )preg_match( '/^(5)/', $cartao ):
			$bandeira = 'mastercard';
			$codigo = 2;
			break;
	}

	$dados_cartao = $cartoes[ $bandeira ];
	if ( !is_array( $dados_cartao ) ) return array( false, false, false );

	$valid = true;
	$valid_cvc = false;

	if ( !in_array( strlen( $cartao ), $dados_cartao[ 'len' ] ) )$valid = false;
	if ( $cvc AND strlen( $cvc ) <= $dados_cartao[ 'cvc' ]AND strlen( $cvc ) != 0 )$valid_cvc = true;
	return array( $bandeira, $valid, $codigo );
}



/*
Tentar capturar o tipo de pagamento pelo título.
O Woocommerce não dá uma opção direta do valor.
Como varia muito a forma de pagamento, essa foi uma opção para facilitar.
Caso não esteja disponível, o usuário poderá escolher.
Esse conteúdo não obrigatório.
*/
function checkPaymentMethod($method){
	if(strpos($method, "Crédito") !== false){
		return "1";
	}
	else if(strpos($method, "Boleto") !== false){
		return "2";
	}
	else if(strpos($method, "Transferência") !== false){
		return "6";
	}
	else if(strpos($method, "Sedex") !== false){
		return "7";
	}
	else if(strpos($method, "Cheque") !== false){
		return "8";
	}
	else if(strpos($method, "Dinheiro") !== false){
		return "9";
	}
	else if(strpos($method, "Financiamento") !== false){
		return "10";
	}
	else if(strpos($method, "Fatura") !== false){
		return "11";
	}
	else if(strpos($method, "Cupom") !== false){
		return "12";
	}else{
		return "14";
	}
}

/**
* Tentar capturar o número Mascarado do Cartão baseado nas
* notas do pedido
**/
function getCreditCardNumberMasked($order_id){
    global $wpdb;

    $table_perfixed = $wpdb->prefix . 'comments';
    $results = $wpdb->get_results("
        SELECT *
        FROM $table_perfixed
        WHERE  `comment_post_ID` = $order_id
        AND  `comment_type` LIKE  'order_note'
    ");

    foreach($results as $note){
		if(strpos($note->comment_content,"****") !== false){
			$content = explode("****", $note->comment_content);
			$masked_c = substr($content[0], -6) . "****" . substr($content[1], 0, 4);
			return $masked_c;
		}
    }
	
	return false;
}

?>