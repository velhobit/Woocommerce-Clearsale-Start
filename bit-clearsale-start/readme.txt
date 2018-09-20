=== Clearsale Start ===
Contributors: velhobit
Donate link: https://velhobit.com.br/projetos/clearsale-start-plugin-woocommerce-gratuito.html
Tags: woocommerce, clearsale, start, antifraude  
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 3.6.0
Requires PHP: 5.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl.html

Integration between the Clearsale Start Brazil and WooCommerce 

*Atenção, este plugin foi feito seguindo o manual da Versão 2.1 do manual http://www.clearsale.com.br/start/manual/Start_Manual_Integracao.pdf*

== Description == 
Este plugin não tem nenhuma relação comercial com a Clearsale. Foi criado unicamente para auxiliar pessoas que desejam facilitar a integração com sua loja no Woocommerce.

Este plugin foi criado para facilitar a integração entre o Clearsale Start com o Woocommerce. A Clearsale é um famoso serviço que oferece soluções anti-fraude para e-commerces.

==Como utilizar =

Nas configurações do plugin, preencha os tokens de Homologação e Produção que foram enviados por e-mail pela Clearsale. Por padrão,o checkbox de homologação está marcado. Lembre-se que em modo de homologação os valores de retorno são fictícios.

Na página do pedido, você verá um botão roxo com a opção de escolher a forma de pagamento e colar a máscara do Cartão de Crédito (se for o caso). O plugin irá tentar reconhecer automaticamente, mas dependendo das formas de pagamento que estão instaladas em sua loja, pode ser que ele não detecte automaticamente. Otimizações poderão ser feitas em versões futuras.

Após finalizar a homologação, desative o checkbox de homologação nas configurações. Durante a homologação, um alerta laranja indicará que você está utilizando em modo de homologação.

= Mais informações =
- O plugin tenta detectar algumas informações através da leitura das atualizações do perfil. Para isso, ele irá tentar verificar o título da forma de pagamento e as notas do pedido. Ele pode não conseguir detectar a forma de pagamento dependendo do tipo de checkout/webservice que você está usando em seu site.

- Devido ao Woocommerce não separar número do endereço, o número da residência não está sendo passado para a Clearsale de forma individual, mas sim junto ao logradouro.

- Se você não usa o WooCommerce Extra Checkout Fields Plugin, altere o arquivo html-meta-box.php para mudar a fonte de dados.