imagem hoststats multi plataforma

### Fazer o build no mac
```bash
docker buildx build --push --platform linux/amd64,linux/arm64 --tag deviggy/hoststats:0 .
```

### Como habilitar o builder com multi plataforma
```bash
docker buildx create --use
```