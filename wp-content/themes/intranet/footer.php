</section>
<!--main-->

<footer style="background: #363636; color: #fff;" class="mt-3">
	<div class="container pt-3 pb-3" id="irrodape">
		<div class="row">
			<div class="col-sm-3 align-middle d-flex align-items-center logo-rodape">
			<a href="https://www.capital.sp.gov.br/"><img src="<?php the_field('logo_prefeitura','conf-rodape'); ?>" alt="<?php bloginfo('name'); ?>"></a>
			</div>
			<div class="col-sm-3 align-middle bd-contact">
				<p class='footer-title'><?php the_field('nome_da_secretaria','conf-rodape'); ?></p>
				<?php the_field('endereco_da_secretaria','conf-rodape'); ?>
			</div>
			<div class="col-sm-3 align-middle">
				<p class='footer-title'>Contatos</p>
				<p><i class="fa fa-phone" aria-hidden="true"></i> <a href="tel:<?php the_field('telefone','conf-rodape'); ?>"><?php the_field('telefone','conf-rodape'); ?></a></p>
				<?php if(get_field('email','conf-rodape')) :?>
				<p><i class="fa fa-envelope" aria-hidden="true"></i> <a href="mailto:<?php the_field('email','conf-rodape'); ?>"><?php the_field('email','conf-rodape'); ?></a></p>
				<?php endif; ?>
				<?php if(get_field('texto_link','conf-rodape') && get_field('link_adicional','conf-rodape')) :?>
				<p><i class="fa fa-comment" aria-hidden="true"></i> <a href="<?php the_field('link_adicional','conf-rodape'); ?>"><?php the_field('texto_link','conf-rodape'); ?></a></p>
				<?php endif; ?>
				
			</div>
			<div class="col-sm-3 align-middle">				
				<p class='footer-title'>Redes sociais</p>				
				<?php 
					// Verifica se existe Redes Sociais
					if( have_rows('redes_sociais', 'conf-rodape') ):
						
						echo '<div class="row redes-footer">';						
						
							while( have_rows('redes_sociais', 'conf-rodape') ) : the_row();
								
								$rede_url = get_sub_field('url_rede'); 
								$rede_texto = get_sub_field('texto_alternativo');								
								$rede_rodape = get_sub_field('tipo_de_icone_rodape');
								$rede_r_imagem = get_sub_field('imagem_rodape');
								$rede_r_icone = get_sub_field('icone_rodape');								
								
							?>
								<div class="col rede-rodape">
									<a href="<?php echo $rede_url; ?>">
										<?php if($rede_rodape == 'imagem' && $rede_r_imagem != '') : ?>
											<img src="<?php echo $rede_r_imagem; ?>" alt="<?php echo $rede_texto; ?>">
										<?php elseif($rede_rodape == 'icone' && $rede_r_icone != ''): ?>
											<i class="fa <?php echo $rede_r_icone; ?>" aria-hidden="true" title="<?php echo $rede_texto; ?>"></i>
										<?php endif; ?>
									</a>
								</div>
							<?php
								

							// End loop.
							endwhile;

						echo '</div>';
					
					endif;
				?>
			</div>
		</div>
	</div>
</footer>
<div class="subfooter rodape-api-col">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center">
				<p>Prefeitura Municipal de São Paulo - Viaduto do Chá, 15 - Centro - CEP: 01002-020</p>
			</div>
		</div>
	</div>
</div>
<div class="voltar-topo">
	<a href="#" id="toTop" style="display: none;">
		<i class="fa fa-arrow-up" aria-hidden="true"></i>
		<p>Voltar ao topo</p>
	</a>
</div>
<?php
	$user = get_current_user_id();
	if($_GET['feedback'] && $user){
		update_user_meta( $user, 'feed_resp', 1 );
	}
	$modal = get_field('ativar_modal');
	$exibi = get_field('tempo_de_exibicao');
	
	$count = get_user_meta( $user, 'wp_login_count', true );
	$feed =  get_user_meta( $user, 'feed_resp', true );
	$img = get_field('imagem_modal');
	$titulo = get_field('titulo_modal');
	$mensagem = get_field('mensagem_modal');
	$botao_url = get_field('botao_modal');
	$botao_nome = get_field('nome_botao_modal');
	//print_r($feed);
?>
<?php if(!$feed): ?>
	<?php if( ($modal && $exibi == 'all') || ($modal && $exibi != 'all' && $count >= $exibi) ): ?>	
		<!-- Bootstrap Modal -->
		<div class="modal fade" id="popup" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content -->
				<div class="modal-content">
					<!-- Modal header -->  
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>&nbsp;</h4>
					</div>

					<!-- Modal body -->  
					<div class="modal-body">

						<?php if($img): ?>
							<img src="<?= $img['url']; ?>" alt="<?= $img['alt']; ?>">
						<?php endif; ?>

						<?php if($titulo): ?>
							<h2><?= $titulo; ?></h2>
						<?php endif; ?>

						<?php if($mensagem): ?>
							<p><?= $mensagem; ?></p>
						<?php endif; ?>

					</div>
					<!-- Modal footer -->  
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-primary" data-dismiss="modal"> Ver depois </button>
						<?php if($botao_url): ?>
							<a href="<?= $botao_url; ?>?feedback=1" class="btn btn-primary"><?= $botao_nome; ?></a>
						<?php endif; ?>
					</div>
				</div> <!-- // .modal-content -->
			</div> <!-- // .modal-dialog -->
		</div> <!-- // #myModal -->
	<?php endif; ?>
<?php endif; ?>

<?php wp_footer() ?>
<script src="//api.handtalk.me/plugin/latest/handtalk.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="<?= get_template_directory_uri(); ?>/js/image-uploader.js"></script>
<script>
	
	//var ht = new HT({
        //token: "aa1f4871439ba18dabef482aae5fd934"
    //});

	document.onkeyup = PresTab;
 
	function PresTab(e)	{
		var keycode = (window.event) ? event.keyCode : e.keyCode;
		

		if (keycode == 9){
			jQuery('.cabecalho-acessibilidade').show();	
			jQuery(" a[accesskey='1']").focus();
			document.onkeyup = null;
		}
	}

	jQuery('.container-a-icones-home').click(function(){
		jQuery('.container-a-icones-home').removeClass('active');
		jQuery(this).addClass('active');
	});

	jQuery( function ( $ ) {
		// Focus styles for menus when using keyboard navigation


		// Properly update the ARIA states on focus (keyboard) and mouse over events
		$( '[role="menubar"]' ).on( 'focus.aria', '[aria-haspopup="true"]', function ( ev ) {
			$( ev.currentTarget ).attr( 'aria-expanded', true );
			$(this).parent().attr( 'aria-expanded', true );
			$(this).parent().attr( 'aria-haspopup', true );
		} );

		// Properly update the ARIA states on blur (keyboard) and mouse out events
		$( '[role="menubar"]' ).on( 'blur.aria', '[aria-haspopup="true"]', function ( ev ) {
			$( ev.currentTarget ).attr( 'aria-expanded', false );
			$(this).parent().attr( 'aria-expanded', false );
			$(this).parent().attr( 'aria-haspopup', false );

			//$(this).click();
		} );
		
	} );

	
	document.addEventListener('DOMContentLoaded', function() {
		// Verifica se o elemento '.custom-file-input' existe
		var customFileInput = document.querySelector('.custom-file-input');
		if (customFileInput) {
			// Adiciona o event listener somente se o elemento existir
			customFileInput.addEventListener('change', function(e) {
				var fileName = document.getElementById("customFile").files[0].name;
				var nextSibling = e.target.nextElementSibling;
				nextSibling.innerText = fileName + ' selecionado';
			});
		}
	});

	function calcelMural(){
		Swal.fire({
			title: '<strong>Atenção</u></strong>',
			icon: 'question',
			html: 'Deseja cancelar o cadastro da publicação?',
			showCloseButton: true,
			showCancelButton: false,
			showDenyButton: true,
			focusConfirm: false,
			confirmButtonText:
				'Não',
			confirmButtonAriaLabel: 'Cancelar ação',
			denyButtonText:
				'Sim',
			cancelButtonAriaLabel: 'Confirmar ação'
		}).then((result) => {
			/* Se for clicado no SIM */
		 	if (result.isDenied) {
				window.location.href = "<?= get_home_url(); ?>/index.php/mural-dos-professores/";
			}
		})
	}

	jQuery(document).ready(function($){		

		$('.input-images').imageUploader({
			label: 'Clique ou arraste a imagem para esta área para carregar. Adicione até 4 imagens nos formatos JPG, JPEG ou PNG.',
			maxSize: 1 * 1024 * 1024,
			maxFiles: 4
		});		

		$('#mural-enviar').submit(function(e){
			
			// Nome obrigatorio
			var nome = $('#nome').val();
			if(!nome){
				Swal.fire({
					icon: 'error',
					title: 'Atenção',
					text: 'O campo Nome é obrigatório.',
				});
				//e.preventDefault();
				return false;
			}

			// Nome da entidade obrigatorio
			var nome_ent = $('#nome_ent').val();
			if(!nome_ent){
				Swal.fire({
					icon: 'error',
					title: 'Atenção',
					text: 'O campo Nome da entidade é obrigatório.',
				});
				//e.preventDefault();
				return false;
			}

			// Título para publicação obrigatorio
			var title = $('#title').val();
			if(!title){
				Swal.fire({
					icon: 'error',
					title: 'Atenção',
					text: 'O campo Título para publicação é obrigatório.',
				});
				//e.preventDefault();
				return false;
			}

			if ($('#customFile').get(0).files.length === 0) {
				Swal.fire({
					icon: 'error',
					title: 'Atenção',
					text: 'O campo Imagem destaque é obrigatório.',
				});
				//e.preventDefault();
				return false;
			}

			// Validar o tamanho da imagem
			var fileInput = $('#customFile');
			var maxSize = fileInput.data('max-size');
			if(fileInput.get(0).files.length){
				var fileSize = fileInput.get(0).files[0].size;  //in bytes
				//console.log(fileSize);
				if(fileSize>maxSize){
					Swal.fire({
						icon: 'error',
						title: 'Atenção',
						text: 'A imagem não pode ter mais que 1mb.',
					});
					e.preventDefault();
					return false;
				}
			}

			// Descricao obrigatorio
			var conteudo = $('#descricao_publi').val();
			if(!conteudo){
				Swal.fire({
					icon: 'error',
					title: 'Atenção',
					text: 'O campo Descrição da publicação é obrigatório.',
				});
				//e.preventDefault();
				return false;
			}

			if(!$('input[name="auto_publi"]').is(':checked')){
				Swal.fire({
					icon: 'error',
					title: 'Atenção',
					text: 'Você precisa aceitar o termo de autorização de publicação.',
				});
				return false;
			}

			if(!$('input[name="auto_compa"]').is(':checked')){
				Swal.fire({
					icon: 'error',
					title: 'Atenção',
					text: 'Você precisa aceitar o termo de autorização para compartilhamento.',
				});
				return false;
			}
			
		});		

		// Start
		// sessionStorage.getItem('key');
		if (sessionStorage.getItem("story") !== 'true') {
			// sessionStorage.setItem('key', 'value'); pair
			sessionStorage.setItem("story", "true");
			// Calling the bootstrap modal
			$("#popup").modal();
		}
		// End

		// Do not include the code below, it is just for the 'Reset Session' button in the viewport.
		// This is same as closing the browser tab.
		$('#reset-session').on('click',function(){
			sessionStorage.setItem('story','');
		});

		$('#acf-field_62420050a8eb3').mask('(00) 00000-0000');
		$('#user-cpf').mask('000.000.000-00');

		$('#acf-field_6241ffb3bf190').change(function() {
			//Use $option (with the "$") to see that the variable is a jQuery object
			var $option = $(this).find('option:selected');
			//Added with the EDIT
			var value = $option.val();//to get content of "value" attrib
			//var text = $option.text();//to get <option>Text</option> content
			if(value == 'Outro'){
				$('.hide-input').show();
			} else {
				$('.hide-input').hide();
			}
		});

		$(".password-type").append('<i class="fa fa-eye-slash" id="togglePassword" style="margin-left: -30px; cursor: pointer;"></i>');

		function alterar_senha(rf, senha1, senha2){
			$.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
				type:"post",
				data: { action: 'altera_senha', user: rf, nova1: senha1, nova2: senha2 },
				success: function(data) {
				
					
					//jQuery('.leaflet-locationiq-list').prepend( data );
					var obj = JSON.parse(data);					

					if(obj.code == 200){
						$("#senha-atual").val("");
						$("#senha-nova").val("");
						$("#senha-repita").val("");
						$('#modalPass').modal('hide');

						Swal.fire({
							icon: 'success',
							title: 'Senha alterada com sucesso',
							text: 'A sua senha foi alterada com sucesso!',
						})
					} else if(obj.code == 401){
						Swal.fire({
							icon: 'error',
							title: 'Senha não alterada',
							text: obj.body,
						})
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Erro',
							text: 'Não foi possível alterar sua senha! Tente novamente.',
						})
					}
				},
				error : function(error){ console.log(error) }
			});
		}

		function validar_usuario(rf, atual, nova1, nova2){
			$.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
				type:"post",
				data: { action: 'valida_user', user: rf, atual: atual },
				success: function(data) {
				
					
					//jQuery('.leaflet-locationiq-list').prepend( data );
					//var obj = JSON.parse(data);
					var obj = data;

					if(obj == 200){
						alterar_senha(rf, nova1, nova2);
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Erro',
							text: 'Sua senha atual está incorreta!',
						})
					}
				},
				error : function(error){ console.log(error) }
			});
		}

		function alterar_senha_wp(userId, senha1, senha2){
			$.ajax({
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
				type:"post",
				data: {
					action: 'change_user_password', // Nome da ação AJAX registrada no WordPress
					user_id: userId, // ID do usuário
					new_password: senha1 // Nova senha
				},
				success: function(response) {
					// Sucesso - exibe a mensagem de sucesso
					$("#senha-nova-wp").val("");
					$("#senha-repita-wp").val("");
					$('#modalPassWp').modal('hide');
					Swal.fire({
						icon: 'success',
						title: 'Senha alterada com sucesso',
						text: 'A sua senha foi alterada com sucesso!',
					});
				},
				error: function(error) {
					// Erro - exibe a mensagem de erro
					Swal.fire({
						icon: 'error',
						title: 'Erro',
						text: 'Não foi possível alterar sua senha! Tente novamente.',
					});
					console.error('Erro ao alterar a senha:', error.responseText);
				}
			});
		}

		$("#alterPassWp").click(function(){			
			var userId = <?= get_current_user_id( ); ?>;			
			var nova1 = $("#senha-nova-wp").val();
			var nova2 = $("#senha-repita-wp").val();

			if(!nova1 || !nova2){
				Swal.fire({
					icon: 'error',
					title: 'Senhas obrigatórias',
					text: 'Preencha todos os campos de senha.',
				});
			} else if(nova1 == nova2){				
				alterar_senha_wp(userId, nova1, nova2);
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Senhas diferentes',
					text: 'As novas senhas não conferem, por gentileza revise e tente novamente.',
				});
			}
		});


		$("#alterPass").click(function(){
			var rf = $("#user-rf").html();
			var atual = $("#senha-atual").val();
			var nova1 = $("#senha-nova").val();
			var nova2 = $("#senha-repita").val();
			var ciente = $('#ciencia-senha:checked').length;            

			if($('#ciencia-senha:checked').length < 1){
				Swal.fire({
					icon: 'error',
					title: 'Erro',
					text: 'Você precisa confirmar o termo de ciência para troca da senha.',
				});
			} else if(!atual || !nova1 || !nova2){
				Swal.fire({
					icon: 'error',
					title: 'Senhas obrigatórias',
					text: 'Preencha todos os campos de senha.',
				});
			} else if(nova1 == nova2){				
				validar_usuario(rf, atual, nova1, nova2);
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Senhas diferentes',
					text: 'As novas senhas não conferem, por gentileza revise e tente novamente.',
				});
			}
		});

		// Inclui botao hide/show no campo de senha
		$(".senha-atual").append('<i class="fa fa-eye-slash" id="senha-atual-show" style="margin-left: -30px; cursor: pointer;"></i>');

		const senhaAtualShow = document.querySelector('#senha-atual-show');
		const senhaAtual = document.querySelector('#senha-atual'); 
		
		if (senhaAtualShow && senhaAtual) {
			senhaAtualShow.addEventListener('click', function (e) {
				// toggle the type attribute
				const type = senhaAtual.getAttribute('type') === 'password' ? 'text' : 'password';
				senhaAtual.setAttribute('type', type);
				// toggle the eye slash icon
				this.classList.toggle('fa-eye-slash');
				this.classList.toggle('fa-eye');
			});
		}

		

		// Inclui botao hide/show no campo de nova senha
		$(".senha-nova").append('<i class="fa fa-eye-slash" id="senha-nova-show" style="margin-left: -30px; cursor: pointer;"></i>');

		const senhaNovaShow = document.querySelector('#senha-nova-show');
		const senhaNova = document.querySelector('#senha-nova'); 
		
		if (senhaNovaShow && senhaNova) {
			senhaNovaShow.addEventListener('click', function (e) {
				// toggle the type attribute
				const type = senhaNova.getAttribute('type') === 'password' ? 'text' : 'password';
				senhaNova.setAttribute('type', type);
				// toggle the eye slash icon
				this.classList.toggle('fa-eye-slash');
				this.classList.toggle('fa-eye');
			});
		}
		

		$("#newPass").click(function(){
			alert('AQui');
			var nova1 = $("#senha-nova").val();
			var nova2 = $("#senha-repita").val();
			var ciente = $('#ciencia-senha:checked').length;
            

			if($('#ciencia-senha:checked').length < 1){
				Swal.fire({
					icon: 'error',
					title: 'Erro',
					text: 'Você precisa confirmar o termo de ciência para troca da senha.',
				});
			} else if(!nova1 || !nova2){
				Swal.fire({
					icon: 'error',
					title: 'Senhas obrigatórias',
					text: 'Preencha todos os campos de senha.',
				});
			} else if(nova1 == nova2){				
				//validar_usuario(rf, atual, nova1, nova2);
				//alert('tudo certo');
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Senhas diferentes',
					text: 'As novas senhas não conferem, por gentileza revise e tente novamente.',
				});
			}

			
		});

		// Inclui botao hide/show no campo de repita senha
		$(".senha-repita").append('<i class="fa fa-eye-slash" id="senha-repita-show" style="margin-left: -30px; cursor: pointer;"></i>');

		const senhaRepitaShow = document.querySelector('#senha-repita-show');
		const senhaRepita = document.querySelector('#senha-repita'); 
		
		if (senhaRepitaShow && senhaRepita) {
			senhaRepitaShow.addEventListener('click', function (e) {
				// toggle the type attribute
				const type = senhaRepita.getAttribute('type') === 'password' ? 'text' : 'password';
				senhaRepita.setAttribute('type', type);
				// toggle the eye slash icon
				this.classList.toggle('fa-eye-slash');
				this.classList.toggle('fa-eye');
			});
		}
		
		
		// Função para carregar posts via AJAX (Calendário)
		function loadPosts(page, searchTerm, dateIni, dateEnd) {
			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				type: 'POST',
				data: {
					action: 'load_posts_by_ajax',
					page: page,
					s: searchTerm,
					date_ini: dateIni,
					date_end: dateEnd,
					security: '<?php echo wp_create_nonce("load_more_posts"); ?>'
				},
				beforeSend: function() {
					// Adicionar um loader (opcional)
					$('.results').html('<div class="loading text-center"><img src="<?php echo get_template_directory_uri(); ?>/classes/assets/img/load-32_256.gif" alt="Carregando o conteúdo"></div>');
				},
				success: function(response) {
					$('.results').html(response);
					$('#tab-calendario').click(function() {
						$('#nav-calendario-tab').click();
					});
					$('#tab-conteudo').click(function() {
						$('#nav-conteudo-tab').click();
					});
				}
			});
		}

		// Função para carregar conteúdos via AJAX
		function loadConteudo(page, searchTerm, categoria, dateIni, dateEnd) {
			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				type: 'POST',
				data: {
					action: 'load_conteudo_by_ajax',
					s: searchTerm,
					categoria: categoria,
					date_ini: dateIni,
					date_end: dateEnd,
					pagina: page,
					security: '<?php echo wp_create_nonce("load_conteudo"); ?>'
				},
				beforeSend: function() {
					// Exibe um loader (opcional)
					$('.results-conteudo').html('<div class="loading text-center"><img src="<?php echo get_template_directory_uri(); ?>/classes/assets/img/load-32_256.gif" alt="Carregando o conteúdo"></div>');
				},
				success: function(response) {
					$('.results-conteudo').html(response);
					$('#tab-calendario').click(function() {
						$('#nav-calendario-tab').click();
					});
					$('#tab-conteudo').click(function() {
						$('#nav-conteudo-tab').click();
					});
				}
			});
		}

		// Sincroniza os campos de busca (sem disparar a busca)
		$('#busca-cal, #busca-cont').on('input', function() {
			const valor = $(this).val(); // Pega o valor do campo alterado
			$('#busca-cal, #busca-cont').val(valor); // Atualiza ambos os campos
			$('#search-front-end').val(valor); // Atualiza o campo de busca global
			$('.bread-current').html(valor); // Atualiza o breadcrumb
		});

		// Paginação da tab Calendário
		$(document).on('click', '.pagination a', function(e) {
			e.preventDefault();

			var link = $(this).attr('href');
			var page = link.match(/page\/(\d+)/)[1]; // Extrai o número da página do link

			// Captura os valores do formulário
			var searchTerm = $('#busca-cal').val();
			var dateIni = $('#data-ini-cal').val();
			var dateEnd = $('#data-end-cal').val();

			// Carrega os posts com os filtros atuais
			loadPosts(page, searchTerm, dateIni, dateEnd);
		});

		// Submit do formulário de busca da tab Calendário
		$('#busca-calendario').on('submit', function(e) {
			e.preventDefault(); // Evita o recarregamento da página

			// Captura os valores do formulário
			var searchTerm = $('#busca-cal').val();
			$('#busca-cont').val(searchTerm); // Atualiza o campo de busca global
			$('.bread-current').html(searchTerm); // Atualiza o breadcrumb
			var dateIni = $('#data-ini-cal').val();
			var dateEnd = $('#data-end-cal').val();
			var categoria = $('#categoria').val();

			// Carrega os posts com os novos filtros (página 1)
			loadPosts(1, searchTerm, dateIni, dateEnd);
			loadConteudo(1, searchTerm, categoria, dateIni, dateEnd);
		});

		// Submit do formulário de busca da tab Conteúdo
		$('#form-conteudo').on('submit', function(e) {
			e.preventDefault(); // Evita o recarregamento da página

			// Captura os valores do formulário
			var searchTerm = $('#busca-cont').val();
			$('#busca-cal').val(searchTerm); // Atualiza o campo de busca global
			$('.bread-current').html(searchTerm); // Atualiza o breadcrumb
			var categoria = $('#categoria').val();
			var dateIni = $('#data-ini').val();
			var dateEnd = $('#data-end').val();

			// Carrega os conteúdos com os novos filtros (página 1)
			loadConteudo(1, searchTerm, categoria, dateIni, dateEnd);
			loadPosts(1, searchTerm, dateIni, dateEnd);
		});

		// Paginação da tab Conteúdo
		$(document).on('click', '.paginationA, .paginationB', function(e) {
			e.preventDefault();

			var pagina = $(this).data('pagina'); // Obtém o número da página

			// Captura os valores do formulário
			var searchTerm = $('#busca-cont').val();
			var categoria = $('#categoria').val();
			var dateIni = $('#data-ini').val();
			var dateEnd = $('#data-end').val();

			// Carrega os conteúdos com os filtros atuais
			loadConteudo(pagina, searchTerm, categoria, dateIni, dateEnd);
		});


		// Limpar filtros
		$('#limpar-cont').on('click', function() {
			var valorBusca = $('#busca-cont').val(); // Armazena o valor antes de resetar
			console.log(valorBusca);
			
			$('#form-conteudo')[0].reset(); // Reseta o formulário
			$('#busca-cont').val(valorBusca); // Restaura o valor no campo de busca
			
			$('#form-conteudo').trigger('submit'); // Reenvia o formulário para carregar todos os resultados
		});

		$('#limpar-calen').on('click', function() {
			var valorBusca = $('#busca-cal').val(); // Armazena o valor antes de resetar
			console.log(valorBusca);
			
			$('#busca-calendario')[0].reset(); // Reseta o formulário
			$('#busca-cal').val(valorBusca); // Restaura o valor no campo de busca
			
			$('#busca-calendario').trigger('submit'); // Reenvia o formulário para carregar todos os resultados
		});

		$('#tab-calendario').click(function() {
			$('#nav-calendario-tab').click();
		});
		$('#tab-conteudo').click(function() {
			$('#nav-conteudo-tab').click();
		});

		
		// Remove "active" de todas as abas		
		$('#nav-tab .nav-link')
		.not('.resultados-busca *')
		.removeClass('active');

		$('#nav-tabContent .tab-pane')
		.not('.resultados-busca *')
		.removeClass('show active');

		// Ativa a aba conforme GET
		const params = new URLSearchParams(window.location.search);
		const filtro = params.get('filtro');

		if(filtro === 'encerrado'){
			$('#sort-encerrados-tab').addClass('active');
			$('#sort-encerrados').addClass('show active');
		} else {
			$('#sort-ativos-tab').addClass('active');
			$('#sort-ativos').addClass('show active');
		}

		
	});
</script>
<?php if($_GET['updated']): ?>
	<script>
		Swal.fire({
			icon: 'success',
			title: 'Dados atualizados',
			text: 'Seus dados foram atualizados com sucesso!',
		});
	</script>
<?php endif; ?>
<?php if($_GET['publicacao'] == 'success'): ?>
	<script>
		Swal.fire({
			icon: 'success',
			title: 'Obrigada por compartilhar sua prática',
			text: 'Suas postagens serão moderadas pelo administrador do site antes de serem postadas.',
		});
	</script>
<?php endif; ?>
<?php if($_GET['expirado'] == '1'):
		$dataSorteio = '';
		if($_GET['evento'] && $_GET['evento'] != ''){
			$dataSorteio = obter_ultima_data_sorteio( $_GET['evento'] );
		}

		if($dataSorteio != ''){
			$texto = 'O sorteio será realizado ' . $dataSorteio . '. ';
		}
	?>
	<script>
		Swal.fire({
			icon: 'warning',
			title: 'Link expirado - Inscrições Encerradas!',
			text: '<?= $texto; ?>A lista de ganhadores será divulgada na página do evento. Fique atento!',
		});
	</script>
<?php endif; ?>
<?php if($_GET['expirado'] == '2'): ?>
	<script>
		Swal.fire({
			icon: 'warning',
			title: 'Link Expirado!',
			text: 'Este link de confirmação é válido por 24 horas. Por favor, solicite um novo cancelamento caso ainda deseje prosseguir.',
		});
	</script>
<?php endif; ?>
<?php if($_GET['expirado'] == '3'): ?>
	<script>
		Swal.fire({
			icon: 'warning',
			title: 'Ação Concluída!',
			text: 'Este link já foi utilizado para confirmar o cancelamento da sua inscrição e, por segurança, não está mais ativo. Não se preocupe o cancelamento da sua inscrição já foi processado com sucesso.',
		});
	</script>
<?php endif; ?>
<?php if($_GET['expirado'] == '4'): ?>
	<script>
		Swal.fire({
			icon: 'warning',
			title: 'Link Inválido!',
			text: 'Este link foi substituído por um novo. Solicite novamente se necessário.',
		});
	</script>
<?php endif; ?>
<?php if($_GET['conf-expirado'] == '1'): ?>
	<script>
		Swal.fire({
			icon: 'info',
			title: 'Atenção!',
			text: 'Esse link não está mais ativo porque o evento já foi realizado. O período para confirmar presença encerrou junto com a data do evento.',
		});
	</script>
<?php endif; ?>
<?php if($_GET['conf-expirado'] == '2'): ?>
	<script>
		Swal.fire({
			icon: 'info',
			title: 'Atenção!',
			text: 'Esse link não está mais ativo porque o prazo para confirmar sua presença neste evento expirou. Sentimos muito, mas seu ingresso foi disponibilizado para outro participante. Fique atento(a) aos próximos sorteios!',
		});
	</script>
<?php endif; ?>
<?php if($_GET['confirmacao'] == '1'): ?>
	<script>
		Swal.fire({
			icon: 'success',
			title: 'Sua presença foi confirmada!',
			text: 'Você receberá um e-mail com as instruções.',
		});
	</script>
<?php endif; ?>
<?php if($_GET['confirmacao'] == '2'): ?>
	<script>
		Swal.fire({
			icon: 'success',
			title: 'Sua participação foi cancelada!',
			text: 'Seu ingresso será resorteado! Obrigado pelo participação!',
		});
	</script>
<?php endif; ?>
<?php if($_GET['confirmacao'] == '3'): ?>
	<script>
		Swal.fire({
			icon: 'info',
			title: 'Atenção!',
			text: 'Esse link não está mais ativo porque você já confirmou ou cancelou sua presença. Se já confirmou, fique tranquilo(a): em breve você receberá um novo e-mail com as instruções.',
		});
	</script>
<?php endif; ?>
<script>
    tinymce.init({
      selector: '.mural-textarea',
	  menubar: false,
	  block_formats: 'Paragraph=p; Header 1=h1; Header 2=h2; Header 3=h3',
      plugins: 'lists textcolor',
      toolbar: 'undo redo | blocks | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat | forecolor',
      language: 'pt_BR'
    });	
</script>

<?php if(is_single()): ?>
	<script>
		jQuery(document).ready(function ($) {

			function formatarDataBrasileira(dataISO) {
				// Separa data e hora
				const [data, horaCompleta] = dataISO.split(' ');
				const [ano, mes, dia] = data.split('-');

				let horaFormatada = '';
				if (horaCompleta) {
					const [hora, minuto] = horaCompleta.split(':');
					const min = parseInt(minuto, 10);

					// Aplica a regra: suprimir "00", senão exibir com dois dígitos
					horaFormatada = min === 0 
						? `${hora}h` 
						: `${hora}h${min.toString().padStart(2, '0')}`;
				}

				return `${dia}/${mes}/${ano} ${horaFormatada}`;
			}

			// Botão "Cancelar Inscrição"
			$('#cancelarInscricao').on('click', function () {
				const postId = $('#comment_post_ID').val();

				$.post('/wp-admin/admin-ajax.php', {
					action: 'buscar_datas_inscricao',
					postId: postId
				}, function (response) {
					if (response.success) {
						const datas = response.data.datas;
						const modelo = response.data.modelo;
						const premios = response.data.premios; // Objeto com datas e prêmios correspondentes

						console.log(response.data);

						if (modelo === 'unico') {
							const dataFormatada = datas[0]; // única data retornada
							const dataFormatadaBr = formatarDataBrasileira(dataFormatada);

							Swal.fire({
								title: 'Cancelar Inscrição',
								html: `<p>Deseja cancelar sua inscrição no evento do dia <strong>${dataFormatadaBr}</strong>? Seus dados serão removidos da listagem do sorteio.</p>`,
								icon: 'warning',
								showCancelButton: true,
								confirmButtonText: 'Cancelar Inscrição',
								cancelButtonText: 'Voltar',
								reverseButtons: true
							}).then((result) => {
								if (result.isConfirmed) {
									cancelarInscricao([dataFormatada], modelo); // envia array com a data única
								}
							});
						} else if ( modelo === 'periodo' ) {

							Swal.fire({
								title: 'Cancelar Inscrição',
								html: `<p>Deseja cancelar sua inscrição no evento? Seus dados serão removidos da listagem do sorteio.</p>`,
								icon: 'warning',
								showCancelButton: true,
								confirmButtonText: 'Cancelar Inscrição',
								cancelButtonText: 'Voltar',
								reverseButtons: true
							}).then((result) => {
								if (result.isConfirmed) {
									cancelarInscricao(null, modelo); // envia array com a data única
								}
							});
						} else if (modelo === 'multi') {
							if (datas.length === 1) {
								// Apenas uma data no modelo multi → exibe como modelo 'unico', mas mantendo modelo 'multi'
								const dataFormatada = datas[0];
								const dataFormatadaBr = formatarDataBrasileira(dataFormatada);

								Swal.fire({
									title: 'Cancelar Inscrição',
									html: `<p>Deseja cancelar sua inscrição no evento do dia <strong>${dataFormatadaBr}</strong>? Seus dados serão removidos da listagem do sorteio.</p>`,
									icon: 'warning',
									showCancelButton: true,
									confirmButtonText: 'Cancelar Inscrição',
									cancelButtonText: 'Voltar',
									reverseButtons: true
								}).then((result) => {
									if (result.isConfirmed) {
										cancelarInscricao([dataFormatada], modelo); // ainda envia como array
									}
								});
							} else {
								// Mais de uma data → exibe os checkboxes
								const checkboxesHtml = datas.map(data => {
									const label = formatarDataBrasileira(data);
									return `
										<label style="display:block; margin: 4px 0;">
											<input type="checkbox" name="data_cancelar" value="${data}"> ${label}
										</label>
									`;
								}).join('');

								Swal.fire({
									title: 'Cancelar a inscrição',
									html: `
										<p>Selecione abaixo quais datas deseja cancelar sua inscrição:</p>
										<div id="datasCancelamento" style="text-align:left; max-height: 200px; overflow-y:auto;">
											${checkboxesHtml}
										</div>
									`,
									icon: 'warning',
									showCancelButton: true,
									confirmButtonText: 'Confirmar Cancelamento',
									cancelButtonText: 'Voltar',
									reverseButtons: true,
									preConfirm: () => {
										const selecionadas = [...document.querySelectorAll('input[name="data_cancelar"]:checked')].map(cb => cb.value);

										if (selecionadas.length === 0) {
											Swal.showValidationMessage('Selecione pelo menos uma data.');
											return false;
										}

										return selecionadas;
									}
								}).then((result) => {
									if (result.isConfirmed) {
										cancelarInscricao(result.value, modelo); // envia as datas selecionadas
									}
								});
							}
						} else if (modelo === 'premio') {
							if (datas.length === 1) {
								// Apenas uma data no modelo multi → exibe como modelo 'unico', mas mantendo modelo 'multi'
								const dataFormatada = datas[0];
								const premio = premios[dataFormatada];
								const dataFormatadaBr = formatarDataBrasileira(dataFormatada);

								Swal.fire({
									title: 'Cancelar Inscrição',
									html: `<p>Deseja cancelar sua inscrição para concorrer ao prêmio <strong>${premio}</strong>? Seus dados serão removidos da listagem do sorteio.</p>`,
									icon: 'warning',
									showCancelButton: true,
									confirmButtonText: 'Cancelar Inscrição',
									cancelButtonText: 'Voltar',
									reverseButtons: true
								}).then((result) => {
									if (result.isConfirmed) {
										cancelarInscricao([dataFormatada], modelo, premios); // ainda envia como array
									}
								});
							} else {
								// Mais de uma data → exibe os checkboxes
								const checkboxesHtml = datas.map(data => {
									const label = formatarDataBrasileira(data);
									return `
										<label style="display:block; margin: 4px 0;">
											<input type="checkbox" name="data_cancelar" value="${data}"> ${premios[data]}
										</label>
									`;
								}).join('');

								Swal.fire({
									title: 'Cancelar a inscrição',
									html: `
										<p>Selecione abaixo quais prêmios deseja cancelar sua inscrição:</p>
										<div id="datasCancelamento" style="text-align:left; max-height: 200px; overflow-y:auto;">
											${checkboxesHtml}
										</div>
									`,
									icon: 'warning',
									showCancelButton: true,
									confirmButtonText: 'Confirmar Cancelamento',
									cancelButtonText: 'Voltar',
									reverseButtons: true,
									preConfirm: () => {
										const selecionadas = [...document.querySelectorAll('input[name="data_cancelar"]:checked')].map(cb => cb.value);

										if (selecionadas.length === 0) {
											Swal.showValidationMessage('Selecione pelo menos um prêmio.');
											return false;
										}

										return selecionadas;
									}
								}).then((result) => {
									if (result.isConfirmed) {
										cancelarInscricao(result.value, modelo, premios); // envia as datas selecionadas
									}
								});
							}
						}
					} else {
						Swal.fire('Erro', 'Não foi possível buscar suas datas de inscrição.', 'error');
					}
				});
			});

			// Função para cancelar a inscrição
			function cancelarInscricao(datas, modelo, premios = null) {
				const userId = <?php echo get_current_user_id(); ?>;
				const postId = $('#comment_post_ID').val();

				if (userId && postId) {
					$.ajax({
						url: '/wp-admin/admin-ajax.php',
						method: 'POST',
						data: {
							action: 'cancelar_inscricao',
							user_id: userId,
							post_id: postId,
							datas: datas,     // envia array ou string
							modelo: modelo,    // 'unico' ou 'multi'
							premios: premios   // opcional, para modelo 'premio'
						},
						success: function (response) {
							if (response.success) {
								Swal.fire({
									icon: 'success',
									title: 'Inscrição cancelada com sucesso!',
									html: response.data.mensagem, // permite <br> funcionar
									confirmButtonText: 'Fechar',
								}).then(() => {
									window.location.href = window.location.href.split('?')[0] + '?inscricao_cancelada=true';
								});
							} else {
								Swal.fire({
									icon: 'error',
									title: 'Erro ao cancelar inscrição',
									text: 'Ocorreu um erro ao tentar cancelar sua inscrição. Tente novamente.',
									confirmButtonText: 'Fechar',
								});
							}
						},
						error: function (error) {
							console.error('Erro ao cancelar inscrição:', error);
							Swal.fire({
								icon: 'error',
								title: 'Erro ao cancelar inscrição',
								text: 'Ocorreu um erro ao tentar cancelar sua inscrição. Tente novamente.',
								confirmButtonText: 'Fechar',
							});
						},
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Erro ao cancelar inscrição',
						text: 'Não foi possível identificar o usuário ou o post. Tente novamente.',
						confirmButtonText: 'Fechar',
					});
				}
			}

		});
	</script>		
<?php endif; ?>
</body>
</html>