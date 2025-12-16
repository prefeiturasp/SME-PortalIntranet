<?php

namespace Classes\TemplateHierarchy\ArchiveContato;


use Classes\Lib\Util;

class ArchiveContatoMetabox extends Util
{
	public function __construct(){}

	public function init() {
		// Adicionando metaboxes aos CPTs
		add_action('add_meta_boxes',  array($this, 'metaBoxAdd'));
		add_action('save_post', array($this, 'metaBoxSave'));

		// Adicionando metaboxes as Taxonomys
		add_action( 'categorias-contato_add_form_fields', array($this, 'metaBoxAddItensContatoTaxonomy'), 10, 2 );
		//add_action( 'categorias-contato_edit_form_fields', array($this,'metaBoxAddItensContatoTaxonomy'), 10, 2 );
		add_action( 'categorias-contato_edit_form', array($this,'metaBoxAddItensContatoTaxonomy'), 10, 2 );

		add_action( 'edited_categorias-contato', array($this,'metaBoxSave'), 10, 2 );
		add_action( 'create_categorias-contato', array($this,'metaBoxSave'), 10, 2 );
	}

	public function metaBoxAdd(){
		add_meta_box('meta-box-contato', 'Insira os campos deste contato', array($this,'metaBoxAddItensContato'), 'contato', 'normal', 'high');

	}

	public function metaBoxAddItensContato($term){
		wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
		?>

		<div>
			<p><strong>Escolha o campo do contato</strong></p>
		</div>
		<select name="select_add_campo_contato" id="select_add_campo_contato">
			<option value="text" >Campo de Texto</option>
			<option value="tel" >Campo de Telefone</option>
			<option value="email" >Campo de Email</option>
			<option value="site" >Campo de Site</option>
		</select>
		<p><input class="add_campos_contato button-primary" id="add_campos_contato" type="button" value="Adicionar Campo"></p>

		<div id="conteudo_a_ser_exibido_contato" class="conteudo_a_ser_exibido_contato"></div>
		<?php
		$this->exibeCamposCadastrados($term);
	}

	public function metaBoxAddItensContatoTaxonomy($term){
		echo '<div class="postbox">';
		echo '<div class="padding-05">';
		echo '<h2><span>Insira os campos deste contato</span></h2>';
		$campo_contato_nivel = get_post_meta($term->term_id, 'campo_contato_nivel', true);
		?>
		<div>
			<p><strong>Escolha o nível do contato</strong></p>

			<select name="select_add_nivel_contato" id="select_add_nivel_contato">
				<option value="1" <?=($campo_contato_nivel == 1)?'selected':''?> >Nível 1</option>
				<option value="2" <?=($campo_contato_nivel == 2)?'selected':''?> >Nível 2</option>
				<option value="3" <?=($campo_contato_nivel == 3)?'selected':''?> >Nível 3</option>
			</select>
		</div>
		<?php

		$this->metaBoxAddItensContato($term);
		echo '</div>';
		echo '</div>';
	}

	public function criaCamposContato(){
		$select_add_campo_contato = $_POST['select_add_campo_contato'];
		?>
		<div class="container-cria-campos">
			<p>
				<?= $this->getNomeTipoCampo($select_add_campo_contato); ?>
				<input name="campo_contato[tipo][tipo_<?= self::randString(5) ?>]" type="hidden" value="<?= $select_add_campo_contato ?>">
				<input class="regular-text" name="campo_contato[valor][valor_<?= self::randString(5) ?>]" type="<?= $select_add_campo_contato?>" required>
				<button title="Excluir Campo" class="excluir_campo_contato">X</button>
			</p>
		</div>
		<?php
	}

	public function metaBoxSave($term_id)
	{
		if ($term_id){
			// Caso seja a taxonomia
			update_post_meta($term_id, 'campo_contato', $_POST['campo_contato']);
			update_post_meta($term_id, 'campo_contato_nivel', $_POST['select_add_nivel_contato']);
		}else {
			// Caso seja a CPT
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
			if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'my_meta_box_nonce')) return;
			if (!current_user_can('edit_post')) return;
			update_post_meta(get_the_ID(), 'campo_contato', $_POST['campo_contato']);
		}
	}

	public function exibeCamposCadastrados($term){

		// Caso seja a taxonomia
		if ($term->term_id){
			// put the term ID into a variable
			$t_id = $term->term_id;
			$campo_contato = get_post_meta($t_id, 'campo_contato', true);
		}else{
			// Caso seja o cpt
			$campo_contato = get_post_meta(get_the_ID(), 'campo_contato', true);
		}

		$array_tipo_de_campo=[];
		$array_valor_do_campo=[];

		if ($campo_contato) {

			foreach ($campo_contato as $index => $valor) {
				if ($index == 'tipo') {
					foreach ($valor as $tipo) {
						$array_tipo_de_campo[] = $tipo;
					}
				}
				if ($index == 'valor') {
					foreach ($valor as $v) {
						$array_valor_do_campo[] = $v;
					}
				}
			}
		}

		foreach($array_tipo_de_campo as $key => $value) {
			$data[] = array('tipo' => $value, 'valor' => $array_valor_do_campo[$key]);
		}

		echo '<div class="container-sortable sortable">';

		if ($data) {

			foreach ($data as $d) {
				?>
				<div class="sortable-item">
					<?= $this->getNomeTipoCampo($d['tipo']); ?>
					<input name="campo_contato[tipo][tipo_<?= self::randString(5) ?>]" type="hidden" value="<?= $d['tipo'] ?>">
					<p>
						<input class="regular-text" name="campo_contato[valor][valor_<?= self::randString(5) ?>]" type="<?= $d['tipo'] ?>" value="<?= esc_attr($d['valor']) ?>" required>
						<button title="Excluir Campo" class="excluir_campo_contato">X</button>
					</p>

				</div>

				<?php

			}
		}

		echo '</div>'; // sortable
	}

	public function getNomeTipoCampo($tipo_de_campo){
		switch ($tipo_de_campo) {
			case 'text':
				return '<span><strong>Campo de texto:</strong> </span>';
			//break;
		}
		switch ($tipo_de_campo) {
			case 'tel':
				return '<span><strong>Campo de telefone:</strong> </span>';
			//break;
		}

		switch ($tipo_de_campo) {
			case 'email':
				return '<span><strong>Campo de email:</strong> </span>';
			//break;
		}

		switch ($tipo_de_campo) {
			case 'site':
				return '<span><strong>Site:</strong> </span>';
			//break;
		}

	}

}

$archive_contato_metabox = new ArchiveContatoMetabox();
$archive_contato_metabox->init();