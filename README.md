# SETUP

docker build -t registry.sme.prefeitura.sp.gov.br/wordpress/base:7.4.33-apache-bullseye -f Dockerfile.php7 .

docker build -t registry.sme.prefeitura.sp.gov.br/wordpress/homolog/intranet .

docker push registry.sme.prefeitura.sp.gov.br/wordpress/base:7.4.33-apache-bullseye

docker push registry.sme.prefeitura.sp.gov.br/wordpress/homolog/intranet

docker login registry.sme.prefeitura.sp.gov.br

- **Usuário / Senha** (Solicitar ao time)
- acesso somente leitura