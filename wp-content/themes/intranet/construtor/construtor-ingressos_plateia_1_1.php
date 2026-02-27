<?php
$qtd = get_sub_field('quantidade');
$colunas = get_sub_field('colunas');
$curl = curl_init();

curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://plateia-api.sme.prefeitura.sp.gov.br/api/v1/eventos',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
$ingressos = json_decode($response, true);

$categorias = array();

foreach($ingressos as $ingresso){
	$categorias[] = $ingresso['TipoEspetaculo'];
}
$categorias = array_unique($categorias);
sort($categorias);

//print_r($ingressos);

?>

<div class="container">

    <form class="form-recados">
        <div class="row">

            <div class="col-12">
                <div class="form-group">
                    <label for="busca">Filtrar por termo</label>
                    <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque por título ou palavra-chave">
                </div>
            </div>

			<div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="categoria">Tipo de espetáculo</label>
                    <select class="form-control" id="categoria" name="categoria">
                        <option value="" disabled selected>Selecione uma categoria</option>
                        <?php
                            if($categorias){
                                foreach($categorias as $categoria){
                                    $selected = '';
                                    if($_GET['categoria'] == $categoria)
                                        $selected = 'selected';
                                    echo '<option value="' . $categoria . '" ' . $selected . '>' . ucfirst(strtolower( str_replace('Ç', 'ç', $categoria) )) . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="data-ini">Filtrar por intervalo de datas</label>
                    <input type="date" id="data-ini" name="date-ini" value="<?= $_GET['date-ini']; ?>" min="<?= date("Y-m-d"); ?>" max="<?= date('Y-m-d',strtotime('+5 day')); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="data-end">&nbsp;</label>
                    <input type="date" id="data-end" name="date-end" value="<?= $_GET['date-end']; ?>" min="<?= date("Y-m-d"); ?>" max="<?= date('Y-m-d',strtotime('+5 day')); ?>">
                </div>
            </div>

            <div class="col-12">
                <div class="form-group d-flex justify-content-end">
                    <input type="hidden" name="filter" value="1">
                    <button type="button" class="btn btn-outline-primary mr-md-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                    <button type="submit" class="btn btn-primary" id="filtrar">Filtrar</button>
                </div>
            </div>

        </div>
    </form>

	<?php

		function inverteData($data){
			if(count(explode("/",$data)) > 1){
				return implode("-",array_reverse(explode("/",$data)));
			}elseif(count(explode("-",$data)) > 1){
				return implode("/",array_reverse(explode("-",$data)));
			}
		}

		if(isset($_GET['busca']) && $_GET['busca'] != ''){
			
			foreach($ingressos as $ingresso) {
				if(stripos($ingresso['Titulo'], $_GET['busca']) !== false || stripos($ingresso['Sintese'], $_GET['busca']) !== false) {
					$resultsBusca[] = $ingresso; 
				}
			}

			$ingressos = $resultsBusca;
		}

		if(isset($_GET['categoria']) && $_GET['categoria'] != ''){
				
			foreach($ingressos as $ingresso) {
				if(stripos($ingresso['TipoEspetaculo'], $_GET['categoria']) !== false) {
					$resultsCateg[] = $ingresso; 
				}
			}

			$ingressos = $resultsCateg;
		}

		//print_r($ingressos);
		
		if(isset($_GET['date-ini']) && $_GET['date-ini'] != '' && isset($_GET['date-end']) && $_GET['date-end'] != ''){
			$dataini = $_GET['date-ini'];
			$dataend = $_GET['date-end'];
			foreach($ingressos as $ingresso){
				$data = explode('/', $ingresso['Data']);
				$newDate = $data[2] . '-' . $data[1] . '-' . $data[0];				
				if($dataini <= $newDate && $dataend >= $newDate){
					$resultsdates[] = $ingresso;					
				}
			}

			$ingressos = $resultsdates;
		}

		if(isset($_GET['date-ini']) && $_GET['date-ini'] != '' && isset($_GET['date-end']) && $_GET['date-end'] == ''){
			$dataini = $_GET['date-ini'];
			foreach($ingressos as $ingresso){
				$data = explode('/', $ingresso['Data']);
				$newDate = $data[2] . '-' . $data[1] . '-' . $data[0];				
				if($dataini <= $newDate){
					$resultsIni[] = $ingresso;					
				}
			}

			$ingressos = $resultsIni;
		}

		if(isset($_GET['date-ini']) && $_GET['date-ini'] == '' && isset($_GET['date-end']) && $_GET['date-end'] != ''){			
			$dataend = $_GET['date-end'];
			foreach($ingressos as $ingresso){
				$data = explode('/', $ingresso['Data']);
				$newDate = $data[2] . '-' . $data[1] . '-' . $data[0];				
				if($dataend >= $newDate){
					$resultsEnd[] = $ingresso;					
				}
			}

			$ingressos = $resultsEnd;
		}
		
	?>

	<?php
		$pagina = ! empty( $_GET['pagina'] ) ? (int) $_GET['pagina'] : 1;
		$total = count( $ingressos ); //total items in array    
		$limit = $qtd; //per page    
		$totalPages = ceil( $total/ $limit ); //calculate total pages
		$pagina = max($pagina, 1); //get 1 page when $_GET['page'] <= 0
		$pagina = min($pagina, $totalPages); //get last page when $_GET['page'] > $totalPages
		$offset = ($pagina - 1) * $limit;
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		if( $offset < 0 ) $offset = 0;
		$ingressos = array_slice( $ingressos, $offset, $limit );

		if(isset($_GET['busca'])){
			$getPagina = '&pagina=';
			$new_url = preg_replace('/&?pagina=[^&]*/', '', $actual_link);
		} else {
			$getPagina = '?pagina=';
			$new_url = preg_replace('/?pagina=[^&]*/', '', $actual_link);
		}

		//echo $new_url;
	?>

	<div class="lista-ingressos">
		<div class="row">
			<?php foreach($ingressos as $ingresso): ?>
				<?php $data = explode('/', $ingresso['Data']); ?>
				<div class="col-md-<?= $colunas; ?>">
					<div class="ingresso">
						<p class="categ"><?= ucfirst(strtolower( str_replace('Ç', 'ç', $ingresso['TipoEspetaculo']) )); ?></p>
						<h3><?= $ingresso['Titulo']; ?></h3>
						<p>
							<?= mb_strimwidth($ingresso['Sintese'], 0, 100, '...'); ?>
							<?php if( strlen($ingresso['Sintese']) > 100): ?>
								<a href="#bannerformmodal" data-toggle="modal" data-target="#sintese<?= $ingresso['IdEvento']; ?>">Leia mais</a>
								<div class="modal fade" id="sintese<?= $ingresso['IdEvento']; ?>" tabindex="-1" role="dialog" aria-labelledby="sintese<?= $ingresso['IdEvento']; ?>Title" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered modal-ingressos" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLongTitle"><?= $ingresso['Titulo']; ?></h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<?= $ingresso['Sintese']; ?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</p>
						<p>Quando: <?= $data[0]; ?>/<?= $data[1]; ?></p>
						<p class="m-0"><a href="https://plateia.sme.prefeitura.sp.gov.br/show/detail?id=<?= $ingresso['IdEvento']; ?>">Inscreva-se no Portal Plateia</a></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if($ingressos && $totalPages > 1):?>
		<div style="width:100%;text-align: center;">
			<div class="pagination <?=ceil($GLOBALS['i']/$GLOBALS['paginacao']) > 1 && $GLOBALS['i'] !== $GLOBALS['paginacao'] ? 'ok' : 'dddnone';?>">
				<a href="<?php echo $new_url . $getPagina . ($pagina - 1);?>" class="anterior <?=$pagina > 1 ? 'ok' : 'dnone';?>">Anterior</a><!--Ir para o anterior-->
				<a class="aaa paginationA " href="<?php echo $new_url . $getPagina . '1'?>">&laquo;</a><!--Ir para o primeiro-->                       
				
				
				<a class="1bbb paginationB <?=$pagina >= 4 ? 'ok' : 'dnone';?>" href="<?php echo $new_url . $getPagina . ($pagina - 3);?>"><?=$pagina - 3;?></a>
				<a class="2bbb paginationB <?=$pagina >= 3 ? 'ok' : 'dnone';?>" href="<?php echo $new_url . $getPagina . ($pagina - 2);?>"><?=$pagina - 2;?></a>
				<a class="3ccc paginationB <?=$pagina >= 2 ? 'ok' : 'dnone';?>" href="<?php echo $new_url . $getPagina . ($pagina - 1);?>"><?=$pagina - 1;?></a>

				
				<a class="eee paginationA active" href="<?php echo $new_url . $getPagina . $pagina;?>"><?=$pagina;?></a>

				<a class="4bbb paginationB <?=$totalPages > $pagina + 1 ? 'ok' : 'dnone';?>" href="<?php echo $new_url . $getPagina . ($pagina + 1);?>"><?=$pagina + 1;?></a>
				<a class="5bbb paginationB <?=$totalPages > $pagina + 2  ? 'ok' : 'dnone';?>" href="<?php echo $new_url . $getPagina . ($pagina + 2);?>"><?=$pagina + 2;?></a>
				<a class="6ccc paginationB <?=$totalPages > $pagina + 3 ? 'ok' : 'dnone';?>" href="<?php echo $new_url . $getPagina . ($pagina + 3);?>"><?=$pagina + 3;?></a>

				<a class="paginationB <?=$totalPages > 1 && $pagina != $totalPages ? 'ok' : 'dnone';?>" href="<?php echo $new_url . $getPagina . $totalPages;?>"><?=$totalPages;?></a>
									
				<a class="d paginationA" href="<?php echo $new_url . $getPagina . $totalPages;?>">»</a><!--Ir para o ultimo-->
				<a href="<?php echo $new_url . $getPagina . ($pagina + 1);?>" class="proximo <?=$pagina != $totalPages  ? 'ok' : 'dnone';?>">Próximo</a><!--Ir para o próximo-->
			</div>
		</div>
	<?php endif; ?>

</div>