docker run -d \
  --name classchat-db \
  -e POSTGRES_DB=classchat \
  -e POSTGRES_USER=admin \
  -e POSTGRES_PASSWORD=1234 \
  -p 5432:5432 \
  -v classchat-data:/var/lib/postgresql/data \
  postgres:latest
