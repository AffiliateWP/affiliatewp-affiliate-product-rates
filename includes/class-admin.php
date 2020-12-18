<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Affiliate_Product_Rates_Admin {
	
	public function __construct() {
		add_filter( 'affwp_edit_affiliate_bottom', array( $this, 'product_rates_table' ) );
		add_filter( 'affwp_new_affiliate_bottom', array( $this, 'product_rates_table' ) );
	}

	/**
	 * Add the product rates table to the edit affiliate screen
	 * @since 1.0
	 */
	public function product_rates_table() {

		if ( ! affwp_apr_is_affiliate_page() ) {
			return;
		}

		$affiliate_id = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '';

		$affiliate    = affwp_get_affiliate( absint( $affiliate_id ) );
		$affiliate_id = isset( $affiliate->affiliate_id ) ? $affiliate->affiliate_id : '';

		// get the affiliate's rates
		$rates = affiliatewp_affiliate_product_rates()->get_rates( $affiliate_id );

		if ( ! $rates ) {
			$rates = array();
		}

		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// remove rate
			$('.affwp_remove_rate').on('click', function(e) {
				e.preventDefault();
				$(this).closest('tr').remove();
			});

			
			// add new rate
			$('.affwp_new_rate').on('click', function(e) {

				e.preventDefault();

				var ClosestRatesTable = $(this).closest('.affiliatewp-rates');

				// clone the last row of the closest rates table
				var row = ClosestRatesTable.find( 'tbody tr:last' );

				// clone it
				clone = row.clone();

				// count the number of rows
				var count = ClosestRatesTable.find( 'tbody tr' ).length;

				// find and clear all inputs
				clone.find( 'td input' ).val( '' );

				// insert our clone after the last row
				clone.insertAfter( row );

				// empty the <td> that has the cloned select2
				clone.find( 'td:first' ).empty();

				// find the original select2
				var original = row.find('select.apr-select-multiple');

				// clone it
				var cloned = original.clone();
				
				// insert after last
				clone.find('td:first').append( cloned );

				// reinitialize the select2
				cloned.show().select2();

				var clonedName = cloned.attr('name');
				clonedName = clonedName.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

				cloned.attr( 'name', clonedName ).attr( 'id', clonedName );

				// replace the name of each input with the count
				clone.find( '.test' ).each(function() {
					var name = $( this ).attr( 'name' );

					name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

					$( this ).attr( 'name', name ).attr( 'id', name );
				});


			});
		});
		</script>

		<style type="text/css">
		.select2-container {
		  width: 100%;
		}
		.apr-select-multiple {
			width: 100%;
		}
		.affwp-product-rates-header {
			font-size: 14px;
		}
		.product-rates { margin-top: 20px; }
		.affiliatewp-rates th { padding-left: 10px; }
		.affwp_remove_rate { margin: 8px 0 0 0; cursor: pointer; width: 10px; height: 10px; display: inline-block; text-indent: -9999px; overflow: hidden; }
		.affwp_remove_rate:active, .affwp_remove_rate:hover { background-position: -10px 0!important }
		.affiliatewp-rates.widefat th, .affiliatewp-rates.widefat td { overflow: auto; }
		</style>

		<?php
		$supported_integrations = affiliatewp_affiliate_product_rates()->supported_integrations();

		$enabled_integrations = affiliate_wp()->integrations->get_enabled_integrations();

		if ( $enabled_integrations ) {
			echo '<p class="affwp-product-rates-header"><strong>' . __( 'Product Rates', 'affiliatewp-affiliate-product-rates' ) . '</strong></p>';
		}

		// add a table for each integration
		foreach ( $enabled_integrations as $integration_key => $integration ) {

			// make sure we only load a table for our supported integrations
			if ( in_array( $integration_key, $supported_integrations ) ) { ?>

			<div class="product-rates">
				<?php echo '<h3>' . $integration . '</h3>'; ?>



				<table class="form-table wp-list-table widefat fixed posts affiliatewp-rates">
					<thead>
						<tr>
							<th><?php _e( 'Product/s', 'affiliatewp-affiliate-product-rates' ); ?></th>
							<th><?php _e( 'Referral Rate', 'affiliatewp-affiliate-product-rates' ); ?></th>
							<th><?php _e( 'Type', 'affiliatewp-affiliate-product-rates' ); ?></th>
							<th style="width:5%;"></th>
						</tr>
					</thead>
					<tbody>

					<?php
					$count =  isset( $rates[$integration_key] ) ? $rates[$integration_key] : array();
					$count = count( $count );

						if ( isset( $rates[$integration_key] ) ) : 
							// index the arrays numerically
							$rates[$integration_key] = array_values( $rates[$integration_key] );
						?>

						<?php foreach( $rates[$integration_key] as $key => $rates_array ) : 
								
							
							$product = isset( $rates_array['products'] ) ? $rates_array['products'] : '';
							$rate    = isset( $rates_array['rate'] ) ? $rates_array['rate'] : '';
							$type    = ! empty( $rates_array['type'] ) ? $rates_array['type'] : 'percentage';
							
							$products = affiliatewp_affiliate_product_rates()->get_products( $integration_key );

						?>

							
							<tr class="row-<?php echo $key; ?>">
								<td>
								
									<select id="product_rates[<?php echo $integration_key;?>][<?php echo $key; ?>]" name="product_rates[<?php echo $integration_key;?>][<?php echo $key; ?>][products][]" data-placeholder="<?php _e( 'Select Product', 'affiliatewp-affiliate-product-rates' ); ?>" multiple class="apr-select-multiple">
										<?php if ( $products ) : 

										foreach ( $products as $product ) { 
										$selected = in_array( $product->ID, $rates_array['products'] ) ? $product->ID : '';
										?>
										<option value="<?php echo absint( $product->ID ); ?>" <?php echo selected( $selected, $product->ID, false ); ?>><?php echo esc_html( get_the_title( $product->ID ) ); ?></option>

										<?php } ?>

										<?php else : ?>

											<option><?php _e( 'No Products found', 'affiliatewp-affiliate-product-rates' ); ?></option>

										<?php endif; ?>

									</select>
									
								</td>
								<td>
									<input class="test" name="product_rates[<?php echo $integration_key;?>][<?php echo $key; ?>][rate]" type="text" value="<?php echo esc_attr( $rate ); ?>"/>
								</td>
								<td>
									<select class="test" name="product_rates[<?php echo $integration_key;?>][<?php echo $key; ?>][type]">
										<option value="percentage"<?php selected( 'percentage', $type ); ?>><?php _e( 'Percentage (%)', 'affiliatewp-affiliate-product-rates' ); ?></option>
										<option value="flat"<?php selected( 'flat', $type ); ?>><?php printf( __( 'Flat %s', 'affiliatewp-affiliate-product-rates' ), affwp_get_currency() ); ?></option>
									</select>
								</td>
								<td>
									<a href="#" class="affwp_remove_rate" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
								</td>
								
							</tr>

							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="3"><?php _e( 'No product rates created yet', 'affiliatewp-affiliate-product-rates' ); ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>
							<select id="product_rates[<?php echo $integration_key;?>][<?php echo $count; ?>]" name="product_rates[<?php echo $integration_key;?>][<?php echo $count; ?>][products][]" data-placeholder="<?php _e( 'Select Product', '' ); ?>" multiple="multiple" class="apr-select-multiple">
										<?php 

										$products = affiliatewp_affiliate_product_rates()->get_products( $integration_key );

										if ( $products ) : 

										foreach ( $products as $product ) { 
										?>
										<option value="<?php echo absint( $product->ID ); ?>"><?php echo esc_html( get_the_title( $product->ID ) ); ?></option>

										<?php } ?>

										<?php else : ?>

											<option><?php _e( 'No Products found', 'affiliatewp-affiliate-product-rates' ); ?></option>

										<?php endif; ?>

									</select>
							</td>
							<td>
								<input name="product_rates[<?php echo $integration_key; ?>][<?php echo $count; ?>][rate]" type="text" value="" class="test" />
							</td>
							<td>
								<select name="product_rates[<?php echo $integration_key; ?>][<?php echo $count; ?>][type]" class="test">
									<option value="percentage"><?php _e( 'Percentage (%)', 'affiliatewp-affiliate-product-rates' ); ?></option>
									<option value="flat"><?php printf( __( 'Flat %s', 'affiliatewp-affiliate-product-rates' ), affwp_get_currency() ); ?></option>
								</select>
							</td>
							<td>
								<a href="#" class="affwp_remove_rate" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="1">
								<button id="affwp_new_rate<?php echo '_' . $integration_key; ?>" name="affwp_new_rate" class="button affwp_new_rate"><?php _e( 'Add New Product Rate', 'affiliatewp-affiliate-product-rates' ); ?></button>
							</th>
							<th colspan="3">
								
							</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<?php }
		}
		?>
			
		<?php
	}

}
new AffiliateWP_Affiliate_Product_Rates_Admin;