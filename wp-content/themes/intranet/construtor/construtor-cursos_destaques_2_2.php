<div class="cursos-destaques">
    
    <div class="cursos-title d-flex justify-content-between">
        <h3>Cursos</h3>
        <?php
            $ver_mais = get_sub_field('link_ver_mais');
            if($ver_mais)
                echo '<p><a href="' . $ver_mais . '">Ver mais</a></p>';
        ?>
    </div>

    <div class="row">
        <?php
            
            $qtd = get_sub_field('quantidade');
            $url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/acervo/?per_page=' . $qtd . '&filter[categoria_acervo]=acesso-a-informacao';
                    
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
                    
                </div>
            </div>
        
        <?php
            endforeach;
        ?>
    </div>

</div>