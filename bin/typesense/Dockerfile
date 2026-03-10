FROM typesense/typesense:26.0

RUN apt update && apt-get install -y ssl-cert
RUN make-ssl-cert generate-default-snakeoil
