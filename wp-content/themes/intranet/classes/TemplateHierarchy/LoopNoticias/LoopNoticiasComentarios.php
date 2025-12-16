<?php

namespace Classes\TemplateHierarchy\LoopNoticias;


class LoopNoticiasComentarios extends LoopNoticias
{
	private $id_post_atual;

	public function __construct($id_post_atual)
	{
		$this->id_post_atual = $id_post_atual;
		$this->init();
	}

	public function init(){
		echo '<div class="col-12 mt-5 mb-3 news-comment">
			<div class="rel-infos pb-3">
				<div class="rel-title d-flex justify-content-between align-items-center">
					<h2>Comentários</h2>
				</div>';
				comments_template();
			echo '</div>';
		echo '</div>';
	}
}