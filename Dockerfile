FROM php:7.4.15-cli-buster

COPY . /app/
WORKDIR /app

EXPOSE 8080
ENTRYPOINT ["php", "-S", "localhost:8080"]
CMD ["php"]