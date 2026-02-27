docker_compose('docker-compose.yml')
docker_build('wordpress/intranet', '.',
  live_update = [
    sync('.', '/var/www/html')
  ])