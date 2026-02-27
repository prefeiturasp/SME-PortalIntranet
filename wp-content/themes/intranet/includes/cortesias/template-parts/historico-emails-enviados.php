<?php extract( $args ); ?>

<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Nome</th>
      <th scope="col">E-mails</th>
      <th scope="col" class="tit-histo <?php echo esc_html( $escondePresenca ); ?>">Envio do e-mail de notificação</th>
      <th scope="col">Envio do E-mail de Instruções</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ( $itens as $item ) : ?>
        <tr>
            <th scope="row"><?php echo esc_html( $item['ordem'] ); ?></th>
            <td><strong><?php echo esc_html( $item['nome'] ); ?></strong></td>
            <td><?php echo wp_kses_post( $item['emails'] ); ?></td>
            <td class="cont-histo <?php echo esc_html( $item['escondePresenca'] ); ?>"><?php echo wp_kses_post( $item['notificado'] ); ?></td>
            <td><?php echo wp_kses_post( $item['instrucoes_enviadas'] ); ?></td>
        </tr>
    <?php endforeach; ?>
  </tbody>
</table>