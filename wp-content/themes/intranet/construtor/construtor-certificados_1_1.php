<?php
    $qtd = get_sub_field('quantidade'); // link
    if(!$qtd && $qtd != ''){
        $qtd = 10;
    }

    function formartarData($data){
        date_default_timezone_set('America/Sao_Paulo');
        
        $dateTime = new DateTime($data);
        $formatter = new IntlDateFormatter(
            'pt_BR'
            ,IntlDateFormatter::FULL
            ,IntlDateFormatter::NONE
            ,'America/Sao_Paulo'       
            ,IntlDateFormatter::GREGORIAN
            ,"dd 'de' MMMM 'de' YYYY"
        );
        $retorno = $formatter->format($dateTime);
        return $retorno;
    }
?>

<div class="container d-none d-md-block">
    <div class="row">
        <div class="col-12">
            <form class="form-recados">
                <div class="row">

                    <div class="col-12">
                        <div class="form-group">
                            <label for="busca">Nome do curso</label>
                            <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque pelo título ou palavra-chave">
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="promotora">Número de homologação</label>
                            <input type="text" value="<?= $_GET['homolog']; ?>" class="form-control" id="homolog" name="homolog" placeholder="Busque pelo número de homologação">
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
    
    
        <?php           

            $username = 'usr_certificados';
            $password = 'WgpxCufo';
            $rf = get_field('rf', 'user_' . get_current_user_id());
            try {
                $conn = new PDO('mysql:host=10.50.1.222;dbname=sme_certificados', $username, $password);
                $query = 'SELECT id, nome_curso, num_homolog_curso, dt_conclusao, arquivo FROM tb_arquivo_certificado WHERE rf = :rf';
                
                if($_GET['busca'] && $_GET['busca'] != ''){
                    $busca = str_replace(' ', '%', $_GET['busca']);
                    $query .=  ' AND nome_curso LIKE "%' . $busca . '%"';
                }

                if($_GET['homolog'] && $_GET['homolog'] != ''){
                    $homolog = $_GET['homolog'];
                    $query .=  ' AND num_homolog_curso = "' . $homolog . '"';
                }

                // Somente data inicial
                if($_GET['date-ini'] != '' && $_GET['date-end'] == ''){
                    $query .= ' AND DATE(dt_conclusao) BETWEEN "' . $_GET['date-ini'] . '" AND "2022-12-31"';
                }

                // Somente data final
                if($_GET['date-end'] != '' && $_GET['date-ini'] == ''){
                    $query .= ' AND DATE(dt_conclusao) BETWEEN "1900-01-01" AND "' . $_GET['date-end'] .'"';
                }

                // Ambas as datas
                if($_GET['date-ini'] != '' && $_GET['date-end'] != ''){
                    $query .= ' AND DATE(dt_conclusao) BETWEEN "' . $_GET['date-ini'] . '" AND "' . $_GET['date-end'] .'"';
                }

                $query .= ' ORDER BY dt_conclusao DESC';

                $stmt = $conn->prepare($query);
                $stmt->execute(array('rf' => $rf));

                $result = $stmt->fetchAll();

                

                $pagina = ! empty( $_GET['pagina'] ) ? (int) $_GET['pagina'] : 1;
                $total = count($result); //total items in array    
                $limit = $qtd; //per page    
                $totalPages = ceil( $total / $limit ); //calculate total pages
                $pagina = max($pagina, 1); //get 1 page when $_GET['page'] <= 0
                $pagina = min($pagina, $totalPages); //get last page when $_GET['page'] > $totalPages
                $offset = ($pagina - 1) * $limit;
                if( $offset < 0 ) $offset = 0;

                $result = array_slice( $result, $offset, $limit );

                if ( count($result) ) {   
                    
                    foreach($result as $row) :?>
                        <div class="curso certificado w-100">
                            <p class="date">Número de homologação: <?= $row['num_homolog_curso']; ?></p>
                            <h2>
                                <?php 
                                    $nome = str_replace('do Curso', '', $row['nome_curso']);
                                    $nome = mb_strtolower($nome, 'UTF-8');
                                    $nome = ucfirst(trim($nome));
                                    echo $nome;
                                ?>
                            </h2>
                            <p class="promotora"><strong>Data de conclusão: </strong> <?= formartarData($row['dt_conclusao']); ?> </p>
                                             
                            <hr> 

                            <a href="<?php echo get_template_directory_uri(); ?>/certificado/export-pdf.php/?id=<?= $row['id']; ?>" class="link" rel="noopener noreferrer">Baixar certificado <i class="fa fa-download" aria-hidden="true"></i></a>

                        </div>
                    <?php endforeach;
                    
                    //echo "<pre>";
                    //print_r($result);
                    //echo "</pre>";
                    
                    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					
                    if(isset($_GET['busca'])){
                        $getPagina = '&pagina=';
                        $new_url = preg_replace('/&?pagina=[^&]*/', '', $actual_link);
                    } else {
                        $getPagina = '?pagina=';
                        $new_url = preg_replace('/?pagina=[^&]*/', '', $actual_link);
                    }
                ?>
                    <?php if($result && $totalPages > 1):?>
						<div class="pagination-prog">
							<div class="wp-pagenavi">
								<div style="text-align: center;display: flex;align-items: center;justify-content: center; margin-top: 10px;">
									<a class="aaa paginationA " href="<?php echo $new_url . $getPagina . '1'?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a><!--Ir para o primeiro-->
									<a class="1bbb paginationB <?=$pagina >= 4 ? 'ok' : 'd-none';?>" href="<?php echo $new_url . $getPagina . ($pagina - 3);?>"><?=$pagina - 3;?></a>
									<a class="2bbb paginationB <?=$pagina >= 3 ? 'ok' : 'd-none';?>" href="<?php echo $new_url . $getPagina . ($pagina - 2);?>"><?=$pagina - 2;?></a>
									<a class="3ccc paginationB <?=$pagina >= 2 ? 'ok' : 'd-none';?>" href="<?php echo $new_url . $getPagina . ($pagina - 1);?>"><?=$pagina - 1;?></a>
									<a class="eee paginationA active" href="<?php echo $new_url . $getPagina . $pagina;?>"><?=$pagina;?></a>
									<a class="4bbb paginationB <?=$totalPages > $pagina + 1 ? 'ok' : 'd-none';?>" href="<?php echo $new_url . $getPagina . ($pagina + 1);?>"><?=$pagina + 1;?></a>
									<a class="5bbb paginationB <?=$totalPages > $pagina + 2  ? 'ok' : 'd-none';?>" href="<?php echo $new_url . $getPagina . ($pagina + 2);?>"><?=$pagina + 2;?></a>
									<a class="6ccc paginationB <?=$totalPages > $pagina + 3 ? 'ok' : 'd-none';?>" href="<?php echo $new_url . $getPagina . ($pagina + 3);?>"><?=$pagina + 3;?></a>
									<a class="paginationB <?=$totalPages > 1 && $pagina != $totalPages ? 'ok' : 'd-none';?>" href="<?php echo $new_url . $getPagina . $totalPages;?>"><?=$totalPages;?></a>					
									<a class="d paginationA" href="<?php echo $new_url . $getPagina . $totalPages;?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a><!--Ir para o ultimo-->
								</div>
							</div>
						</div>
					<?php endif; ?>

                <?php
                
                } else {
                    echo "Nenhum resultado retornado.";
                }
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }

        ?>
    
</div>