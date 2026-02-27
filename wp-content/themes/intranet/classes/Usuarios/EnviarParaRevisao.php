<?php

namespace Classes\Usuarios;


class EnviarParaRevisao
{
	private $post_status;
	private $post_type;

	public function __construct()
	{
		$this->page_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : null);

		$this->setPostStatus(get_post_status($this->page_id));
		$this->setPostType(get_post_type($this->page_id));

		add_filter('init', array($this, 'reAprovePages'), '99', 2);
		add_filter( 'init', array($this, 'reAproveCards'), '99', 2 );
		add_filter( 'init', array($this, 'reAproveContatos'), '99', 2 );

		$user = wp_get_current_user();

		$usuario = new \WP_User($user->ID);

		$post_type = get_post_type($this->page_id);
	}

	public function setPostStatus($post_status)
	{
		$this->post_status = $post_status;
	}

	public function getPostStatus()
	{
		return $this->post_status;
	}

	public function setPostType($post_type)
	{
		$this->post_type = $post_type;
	}

	public function getPostType()
	{
		return $this->post_type;
	}

	public function getRoleUser(){
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;

		return $roles[0];
	}

	public function reAproveCards() {

		if ($this->getRoleUser() == 'contributor' && 'card' === $this->getPostType()){
			if ($this->getPostStatus() == "publish"){
				wp_update_post(array(
					'ID'    =>  $this->page_id,
					'post_status'   =>  'pending'
				));
			}
		}
	}

	public function reAproveContatos() {

    	if ($this->getRoleUser() == 'contributor' && 'contato' === $this->getPostType()){
			if ($this->getPostStatus() == "publish"){
				wp_update_post(array(
					'ID'    =>  $this->page_id,
					'post_status'   =>  'pending'
				));
			}
		}
	}

	public function reAprovePages(){

		if ($this->getRoleUser() == 'contributor') {
			if ($this->getPostStatus() == "publish"){
				echo'
				<script>
				window.onload = initPage;
				function initPage(){
					//document.getElementById("post-status-select").setAttribute("style", "display: block;");
					var selectobject = document.getElementById("post_status");
					for (var i=0; i<selectobject.length; i++) {
						if (selectobject.options[i].value == "publish")
							selectobject.remove(i);
						if (selectobject.options[i].value == "draft")
							selectobject.remove(i);
					}
				}
				</script>
				';
			}
			if ($this->getPostStatus() == "pending"){
				wp_update_post(array(
					'ID'    =>  $this->page_id,
					'post_status'   =>  'pending'
				));
			}
		}
	}
}

new EnviarParaRevisao();