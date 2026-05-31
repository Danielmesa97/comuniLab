FROM node:20-bullseye

WORKDIR /app

COPY docker/entrypoint.sh /usr/local/bin/frontend-entrypoint

ENTRYPOINT ["sh", "/usr/local/bin/frontend-entrypoint"]
