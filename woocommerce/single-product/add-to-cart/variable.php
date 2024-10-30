<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
		
global $product, $post;
$variation_ids = $product->children;		
foreach( $variation_ids as $var_id ) :
	$variationid=$var_id;
    $all_cfs = get_post_custom($variationid[0]); 
endforeach;

$SKU = $all_cfs['_sku'][0];
$IWEB = $all_cfs['_iweb'][0];
?>

<?php 
	if($IWEB==1){
		$nonce = wp_create_nonce("my_imaxel_iweb_editor_nonce");
	}elseif($IWEB==-1){
		$nonce = wp_create_nonce("my_imaxel_editor_nonce");
	}
	
	$variationid=$variationid;
	$nombreboton=__('Create now','Imaxel');
	$nombrebotonBorrar=__('Delete your project','Imaxel');	
	echo '<span id="getvariationid" style="display:none;">'.$variationid[0].'</span>'; 
	echo '<span id="wordpressid" style="display:none;">'.$postid.'</span>'; 
	echo '<span id="productcode" style="display:none;">'.$SKU.'</span>'; 
?>
		

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	<?php if ( ! empty( $available_variations ) ) : ?>
		<table class="variations" cellspacing="0"> <?php /*  style="display: none;" */ ?>
			<tbody>
				<?php $loop = 0; foreach ( $attributes as $name => $options ) : $loop++; ?>
					<tr>
						<?php ################### ?>
						<?php if(is_array($options) && $options[0] == "CUSTOM_TEXT") : ?>
				         <td colspan="2">
							<input type="hidden" class="fullwidth req" id="<?php echo esc_attr( sanitize_title( $name ) ).''.$SKU; ?>" name="attribute_<?php echo sanitize_title( $name ); ?>" value="" placeholder="ID Proyecto aqui" />
						</td>
				        
						
				        <?php else: ?>   
						<?php ################### ?>
						<td class="label"><label for="<?php echo sanitize_title( $name ); ?>"><?php //echo wc_attribute_label( $name ); ?></label></td>
						<td class="value"><select id="<?php echo esc_attr( sanitize_title( $name ) ); ?>" name="attribute_<?php echo sanitize_title( $name ); ?>" data-attribute_name="attribute_<?php echo sanitize_title( $name ); ?>">
							<option value=""><?php echo __( 'Choose an option', 'woocommerce' ) ?>&hellip;</option>
							<?php
								if ( is_array( $options ) ) {

									if ( isset( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ) {
										$selected_value = $_REQUEST[ 'attribute_' . sanitize_title( $name ) ];
									} elseif ( isset( $selected_attributes[ sanitize_title( $name ) ] ) ) {
										$selected_value = $selected_attributes[ sanitize_title( $name ) ];
									} else {
										$selected_value = '';
									}

									// Get terms if this is a taxonomy - ordered
									if ( taxonomy_exists( $name ) ) {

										$terms = wc_get_product_terms( $post->ID, $name, array( 'fields' => 'all' ) );

										foreach ( $terms as $term ) {
											if ( ! in_array( $term->slug, $options ) ) {
												continue;
											}
											echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $term->slug ), false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
										}

									} else {

										foreach ( $options as $option ) {
											echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( sanitize_title( $selected_value ), sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
										}

									}
								}
							?>
						</select>
						</td>
						<?php endif;?>
					</tr>
		        <?php endforeach;?>
			</tbody>
		</table>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		<div class="single_variation_wrap" <?php if($loop==1){ }else{ echo 'style="display:none;"'; } ?>>
			<?php do_action( 'woocommerce_before_single_variation' ); ?>

			<div class="single_variation"></div>

			<div class="variations_button">
				
				<?php
				if($IWEB==1){ 
					$link = admin_url('admin-ajax.php?action=imaxel_iweb_editor&productCode='.$SKU.'&productsID='.$product->id.'&variation_id='.$variationid[0].'&nonce='.$nonce);
					echo '<a class="single_add_to_cart_button quiero secondary button alt editor_imaxel_iweb" data-nonce="' . $nonce . '" data-productCode="' . $SKU . '" data-productsID="'.$product->id.'" data-variation_id="'.$variationid[0].'" href="' . $link . '">'.$nombreboton.'</a>';
				}elseif($IWEB==-1){
					$link = admin_url('admin-ajax.php?action=imaxel_editor&productCode='.$SKU.'&productsID='.$product->id.'&variation_id='.$variationid[0].'&nonce='.$nonce);
					echo '<a class="single_add_to_cart_button quiero secondary button alt editor_imaxel" data-nonce="' . $nonce . '" data-productCode="' . $SKU . '" data-productsID="'.$product->id.'" data-variation_id="'.$variationid[0].'" href="' . $link . '">'.$nombreboton.'</a>';
				}
    ?>
			</div>

			<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
			<input type="hidden" name="variation_id" class="variation_id" value="" />

			<?php do_action( 'woocommerce_after_single_variation' ); ?>
		</div>
		
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php else : ?>

		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
