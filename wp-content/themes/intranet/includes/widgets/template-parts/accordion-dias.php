<?php

wp_enqueue_style( 'bootstrap-sorteio-css' );
wp_enqueue_style( 'widgets-dashboard' );
wp_enqueue_script( 'widgets' );

extract( $args );

?>

<?php if ( isset( $eventos ) && !empty( $eventos ) ) : ?>
	<div id="accordion-calendario-sorteios">

		<?php foreach ( $eventos as $label => $eventos_dia ) : 
			$slug   = sanitize_title( $label );
			$header = 'heading-' . $slug;
			$collapse = 'collapse-' . $slug;
			?>
			<div class="card">
				<div class="card-header d-flex justify-content-between" id="<?php echo esc_attr( $header ); ?>">
					<strong class="mb-0 text-primary">
						<div
							data-toggle="collapse"
							data-target="#<?php echo esc_attr( $collapse ); ?>"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $collapse ); ?>"
							>
							<?php echo esc_html( $label ); ?>
						</div>
					</strong>
					<span
						data-toggle="collapse"
						data-target="#<?php echo esc_attr( $collapse ); ?>"
						aria-expanded="false"
						aria-controls="<?php echo esc_attr( $collapse ); ?>"
						class="dashicons dashicons-arrow-down-alt2 collapsed text-secondary"
						>
					</span>
				</div>
				<div id="<?php echo esc_attr( $collapse ); ?>" class="collapse" aria-labelledby="<?php echo esc_attr( $header ); ?>" data-parent="#accordion-calendario-sorteios">
					<div class="card-body p-3">

						<?php if ( empty( $eventos_dia ) ) : ?>
							<span>Sem sorteios para este dia.</span>
						<?php endif; ?>

						<?php if ( !empty( $eventos_dia ) ) : ?>
							<div class="community-events">
								<ul class="community-events-results activity-block last" aria-hidden="false">
									<?php
									foreach ($eventos_dia as $evento) :
										$sorteios_dia = obter_informacoes_datas_sorteio( $evento['post_id'], 'data', $evento['data'] );
										$local = $evento['local'];
										?>
										<li class="event event-wordcamp wp-clearfix">
											<div class="container">
												<div class="dashicons event-icon" aria-hidden="true"></div>
												<div class="event-info-inner">
													<a class="event-title" href="<?php echo esc_url( $evento['link'] ); ?>">
														<?php echo esc_html( $evento['title'] ); ?>
													</a>

													<?php
													if ( $local && $local !== 'outros' ) :
														$term = get_term($local);
														?>
														<?php if ( $term && !is_wp_error( $term ) ) : ?>
															<small class="event-city"><b><?php echo esc_html( "- Local: {$term->name}" ); ?></b></small>
														<?php endif; ?>
														<?php
													endif;
													?>

													<?php if ( !empty( $sorteios_dia ) ) : ?>
														<ul>
														<?php foreach ( $sorteios_dia as $info ) : ?>
															<li>
																<span class="ce-separator"></span> <?php echo esc_html( "{$info['data']} - {$info['status']}" ); ?>
																<?php if($info['status'] == 'Sorteio Realizado' && $info['instrucoes'] != '') : ?>
																	<br>- <strong><?php echo esc_html("{$info['instrucoes']}"); ?></strong>
																<?php endif; ?>
															</li>
														<?php endforeach ?>
														</ul>
													<?php endif; ?>

												</div>
											</div>
											<div class="event-date-time">
												<span class="event-date">
													<?= esc_html($data_formatada); ?>
													<?php if (!empty($hora)) : ?>
														<span class="ce-separator"></span>
														<?= esc_html(formatar_hora($hora)); ?>
													<?php endif; ?>
												</span>
											</div>
										</li>
										<?php
									endforeach;
									?>
								</ul>
							</div>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

