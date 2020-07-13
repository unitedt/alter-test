# Dockerfile
FROM ubuntu:latest

ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update \
    && apt-get --no-install-recommends -y install \
    siege \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists

WORKDIR /usr/src/loadtest

COPY . .

CMD ["./run_siege.sh", "http://alter-test.chuprunov.name"]