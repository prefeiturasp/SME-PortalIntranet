<?php
    // Area Promotora
    $url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/promotora/?per_page=99';

    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

    $promoList = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    $promoResponse = json_decode($promoList);

    // Formacao
    $url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/formacao/?per_page=99';

    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

    $formaList = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    $formaResponse = json_decode($formaList);

    // Publico Alvo
    $url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/publico/?per_page=99';

    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

    $publicoList = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    $publicoResponse = json_decode($publicoList);

    $countBusca = 0;

    if($_GET['promotora'] && $_GET['promotora'] != '')
        $countBusca++;

    if($_GET['busca'] && $_GET['busca'] != '')
        $countBusca++;

    if($_GET['formacao'] && $_GET['formacao'] != '')
        $countBusca++;

    if($_GET['publico'] && $_GET['publico'] != '')
        $countBusca++;

    if($_GET['date-ini'] && $_GET['date-ini'] != '')
        $countBusca++;

    if($_GET['date-end'] && $_GET['date-end'] != '')
        $countBusca++;
    
?>

<div class="container d-none d-md-block">
    <div class="row">
        <div class="col-12">
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
                            <label for="formacao">Filtrar por tipo de formação</label>
                            <select class="form-control" id="formacao" name="formacao">
                                <option value="" selected>Selecione uma formação</option>
                                <?php
                                    if($formaResponse){
                                        foreach($formaResponse as $formacao){
                                            $selected = '';
                                            if($_GET['formacao'] == $formacao->slug)
                                                $selected = 'selected';
                                            echo '<option value="' . $formacao->slug . '" ' . $selected . '>' . $formacao->name . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="publico">Filtrar por público alvo</label>
                            <select class="form-control" id="publico" name="publico">
                                <option value="" selected>Selecione um público</option>
                                <?php
                                    if($publicoResponse){
                                        foreach($publicoResponse as $publico){
                                            $selected = '';
                                            if($_GET['publico'] == $publico->slug)
                                                $selected = 'selected';
                                            echo '<option value="' . $publico->slug . '" ' . $selected . '>' . $publico->name . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="promotora">Filtrar por área promotora</label>
                            <select class="form-control" id="promotora" name="promotora">
                                <option value="" selected>Selecione uma área</option>
                                <?php
                                    if($promoResponse){
                                        foreach($promoResponse as $promotora){
                                            $selected = '';
                                            if($_GET['promotora'] == $promotora->slug)
                                                $selected = 'selected';
                                            echo '<option value="' . $promotora->slug . '" ' . $selected . '>' . $promotora->name . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="data-ini">Filtrar por intervalo de datas</label>
                            <input type="date" id="data-ini" name="date-ini" value="<?= $_GET['date-ini']; ?>" max="<?= date("Y-m-d"); ?>">
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="data-end">&nbsp;</label>
                            <input type="date" id="data-end" name="date-end" value="<?= $_GET['date-end']; ?>" max="<?= date("Y-m-d"); ?>">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group d-flex justify-content-end">
                            <input type="hidden" name="filter" value="1">
                            <button type="button" class="btn btn-outline-primary mr-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12 d-md-none">
            <button type="button" class="btn btn-outline-primary btn-avanc-f btn-avanc btn-avanc-m mb-4" data-toggle="modal" data-target="#filtroBusca">
                <i class="fa fa-filter" aria-hidden="true"></i> Filtrar 
                <?php if($countBusca > 0): ?>
                    <span class="badge badge-primary"><?php echo $countBusca; ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>
</div>
    

<div class="container">
    <div class="row">
            <?php
                $pagina = ! empty( $_GET['pagina'] ) ? (int) $_GET['pagina'] : 1;                

                $page_id = get_the_ID();
                //$categorias = get_sub_field('categorias');
                //$ativarCategorias = get_sub_field('ativar_categorias');
                $qtd = get_sub_field('quantidade');
                $url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/acervo/?per_page=' . $qtd . '&page=' . $pagina . '&filter[categoria_acervo]=acesso-a-informacao';
                if($_GET['promotora'] && $_GET['promotora'] != '')
                    $url .= '&filter[promotora]=' . $_GET['promotora'];

                if($_GET['busca'] && $_GET['busca'] != ''){
                    $busca = str_replace(' ', '+', $_GET['busca']);
                    $url .= '&search=' . $busca; 
                }                       

                if($_GET['formacao'] && $_GET['formacao'] != '')
                    $url .= '&filter[formacao]=' . $_GET['formacao'];

                if($_GET['publico'] && $_GET['publico'] != '')
                    $url .= '&filter[publico]=' . $_GET['publico'];

                if($_GET['date-ini'] && $_GET['date-ini'] != '')
                    $url .= '&after=' . $_GET['date-ini'] . 'T00:00:01';

                if($_GET['date-end'] && $_GET['date-end'] != '')
                    $url .= '&before=' . $_GET['date-end'] . 'T23:59:59';  
                    
                //echo $url;
               
                $headers = [];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADERFUNCTION,
                    function ($curl, $header) use (&$headers) {
                        $len = strlen($header);
                        $header = explode(':', $header, 2);
                        if (count($header) < 2) // ignore invalid headers
                            return $len;

                        $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                        return $len;
                    }
                );
                $response = curl_exec($ch);                
                
                $jsonArrayResponse = json_decode($response);

                //echo "<pre>";
                //print_r($jsonArrayResponse);
                //echo "</pre>";

                foreach($jsonArrayResponse as $acervo):
                    $old_date_timestamp = strtotime($acervo->date);        
                    $data = getDay(date('w', $old_date_timestamp)) . ', ' . converter_mes(date('m', $old_date_timestamp)) . ' ' . date('d', $old_date_timestamp) . ' às ' . date('H\hi\m\i\n', $old_date_timestamp);
                
            ?>
                <div class="col-12">
                    <div class="curso">
                        <p class="date">
                            <?php if($acervo->numero_de_despacho_de_homologacao && $acervo->numero_de_despacho_de_homologacao != ''): ?>
                                Homologação <?= $acervo->numero_de_despacho_de_homologacao; ?> -                                 
                            <?php endif; ?>
                            <?= $data; ?>

                            <?php if($acervo->pagina_do_diario_oficial && $acervo->pagina_do_diario_oficial != ''): ?>
                                - página <?= $acervo->pagina_do_diario_oficial; ?>                              
                            <?php endif; ?>
                        </p>
                        <h2><a target="_blank" href="<?= $acervo->link; ?>"><?= $acervo->title->rendered; ?></a></h2>
                        <?php if($acervo->area_promotora && $acervo->area_promotora[0] != ''): ?>
                            <p class="promotora"><strong>Área promotora: </strong>
                                <?php
                                    $i = 0;
                                    foreach($acervo->area_promotora as $area){
                                        if($i == 0){
                                            echo get_tax_name('promotora', $area);
                                        } else {
                                            echo '/ ' . get_tax_name('promotora', $area);
                                        }
                                        $i++;
                                    }
                                ?>
                            </p>
                        <?php endif; ?>                        
                        <hr>                        
                    
                        <?php
                            $arquivo = '';
                            
                            if($acervo->arquivo_acervo_digital && $acervo->arquivo_acervo_digital != '')
                                $arquivo = get_file_url($acervo->arquivo_acervo_digital);
                            
                            if($acervo->arquivos_particionados_0_arquivo && $acervo->arquivos_particionados_0_arquivo != '')
                               $arquivo = get_file_url($acervo->arquivos_particionados_0_arquivo);
                            
                            if($arquivo && $arquivo != ''){ 
                                                                
                            ?>                           

                            <i class="fa fa-search" aria-hidden="true"></i> <a href="#modal-<?=$acervo->id; ?>" class="link" data-toggle="modal" data-target="#modal-<?=$acervo->id; ?>">Visualizar</a> / 

                                <?php if(substr($arquivo, -3) == 'jpg' || substr($arquivo, -3) == 'jpeg' || substr($arquivo, -3) == 'png' || substr($arquivo, -3) == 'gif' || substr($arquivo, -3) == 'webp') : ?>
                        
                                    <!-- Modal -->
                                    <div class="modal fade" id="modal-<?=$acervo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <p class="modal-title"><?= $acervo->title->rendered; ?></p>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php if($arquivo) : ?>
                                                        <img src="<?php echo $arquivo; ?>" class="img-fluid d-block mx-auto py-2">
                                                    <?php else: ?>
                                                        <p>Visualização não disponível.</p>
                                                    <?php endif; ?>
                                                </div>															
                                            </div>
                                        </div>
                                    </div>

                                <?php elseif(substr($arquivo, -3) == 'pdf'): ?>

                                    <div class="modal fade" id="modal-<?=$acervo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <p class="modal-title"><?= $acervo->title->rendered; ?></p>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="embed-responsive embed-responsive-16by9">                                                        
                                                        <iframe style="largura: 718px; altura: 700px;" src="<?= $arquivo; ?>" frameborder="0"></iframe>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                <?php else : ?>
                                    
                                    <div class="modal fade" id="modal-<?=$acervo->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <p class="modal-title"><?= $acervo->title->rendered; ?></p>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="embed-responsive embed-responsive-16by9">
                                                        <iframe title="doc" type="application/pdf" src="https://docs.google.com/gview?url=<?php echo $arquivo; ?>&amp;embedded=true" class="jsx-690872788 eafe-embed-file-iframe"></iframe>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                <?php endif;                              
                            }
                                
                        ?> 
                    
                    <a href="<?= $acervo->link; ?>" class="link" target="_blank" rel="noopener noreferrer">Ver detalhes no Acervo Digital</a>

                    </div>
                </div>
        
        <?php
            endforeach;
        ?>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12">

            <?php
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $actual_link = str_replace('/?', '/', $actual_link);
                $new_url = preg_replace('/&?pagina=[^&]*/', '', $actual_link);             
                $totalPages = $headers['x-wp-totalpages'][0];
                $param = '?pagina=';
                $pageOne = '?pagina=1';
                if($_GET['filter']){
                    $param = '&pagina=';
                    $pageOne = '&pagina=1';
                }
            ?>

            <?php if($totalPages > 1): ?>
                <div class="pagination-prog">
                    <div class="wp-pagenavi">
                        <div style="text-align: center;display: flex;align-items: center;justify-content: center; margin-top: 10px;">                                
                                <a class="aaa paginationA " href="<?= $new_url . $pageOne; ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a><!--Ir para o primeiro-->
                                <a class="1bbb paginationB <?=$pagina >= 4 ? 'ok' : 'd-none';?>" href="<?= $new_url . $param . ($pagina - 3);?>"><?=$pagina - 3;?></a>
                                <a class="2bbb paginationB <?=$pagina >= 3 ? 'ok' : 'd-none';?>" href="<?= $new_url . $param . ($pagina - 2);?>"><?=$pagina - 2;?></a>
                                <a class="3ccc paginationB <?=$pagina >= 2 ? 'ok' : 'd-none';?>" href="<?= $new_url . $param . ($pagina - 1);?>"><?=$pagina - 1;?></a>
                                <a class="eee paginationA active" href="<?= $new_url . $param . $pagina;?>"><?=$pagina;?></a>
                                <a class="4bbb paginationB <?=$totalPages > $pagina + 1 ? 'ok' : 'd-none';?>" href="<?= $new_url . $param . ($pagina + 1);?>"><?=$pagina + 1;?></a>
                                <a class="5bbb paginationB <?=$totalPages > $pagina + 2  ? 'ok' : 'd-none';?>" href="<?= $new_url . $param . ($pagina + 2);?>"><?=$pagina + 2;?></a>
                                <a class="6ccc paginationB <?=$totalPages > $pagina + 3 ? 'ok' : 'd-none';?>" href="<?= $new_url . $param . ($pagina + 3);?>"><?=$pagina + 3;?></a>
                                <a class="paginationB <?=$totalPages > 1 && $pagina != $totalPages ? 'ok' : 'd-none';?>" href="<?= $new_url . $param . $totalPages;?>"><?=$totalPages;?></a>
                                <a class="d paginationA" href="<?= $new_url . $param . $totalPages;?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a><!--Ir para o ultimo-->
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal right fade" id="filtroBusca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<p class="modal-title" id="myModalLabel2">Filtrar por:</p>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>				
			</div>

			<div class="modal-body">
				<div class="acord-busca my-2">
					<form method="get" class="text-left" action="<?= get_the_permalink(); ?>">
						
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="busca">Filtrar por termo</label>
                                    <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque por título ou palavra-chave">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="formacao">Filtrar por tipo de formação</label>
                                    <select class="form-control" id="formacao" name="formacao">
                                        <option value="" selected>Selecione uma formação</option>
                                        <?php
                                            if($formaResponse){
                                                foreach($formaResponse as $formacao){
                                                    $selected = '';
                                                    if($_GET['formacao'] == $formacao->slug)
                                                        $selected = 'selected';
                                                    echo '<option value="' . $formacao->slug . '" ' . $selected . '>' . $formacao->name . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="publico">Filtrar por público alvo</label>
                                    <select class="form-control" id="publico" name="publico">
                                        <option value="" selected>Selecione um público</option>
                                        <?php
                                            if($publicoResponse){
                                                foreach($publicoResponse as $publico){
                                                    $selected = '';
                                                    if($_GET['publico'] == $publico->slug)
                                                        $selected = 'selected';
                                                    echo '<option value="' . $publico->slug . '" ' . $selected . '>' . $publico->name . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="promotora">Filtrar por área promotora</label>
                                    <select class="form-control" id="promotora" name="promotora">
                                        <option value="" selected>Selecione uma área</option>
                                        <?php
                                            if($promoResponse){
                                                foreach($promoResponse as $promotora){
                                                    $selected = '';
                                                    if($_GET['promotora'] == $promotora->slug)
                                                        $selected = 'selected';
                                                    echo '<option value="' . $promotora->slug . '" ' . $selected . '>' . $promotora->name . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="data-ini">Filtrar por intervalo de datas</label>
                                    <input type="date" id="data-ini" name="date-ini" value="<?= $_GET['date-ini']; ?>" max="<?= date("Y-m-d"); ?>">
                                </div>
                            </div>

                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="data-end">&nbsp;</label>
                                    <input type="date" id="data-end" name="date-end" value="<?= $_GET['date-end']; ?>" max="<?= date("Y-m-d"); ?>">
                                </div>
                            </div>

                            <div class="col-12 btn-filtro">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-primary mr-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                                    <button type="submit" class="btn btn-primary" id="filtrar">Filtrar</button>
                                </div>
                            </div>

                        </div>

					</form>
				</div>	
			</div>

		</div><!-- modal-content -->
	</div><!-- modal-dialog -->
</div><!-- modal -->