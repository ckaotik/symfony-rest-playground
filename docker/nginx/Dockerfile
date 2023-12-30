FROM nginx:latest

RUN mkdir -p /etc/nginx/templates

COPY nginx.conf /etc/nginx/
COPY templates/* /etc/nginx/templates/

CMD ["nginx"]

EXPOSE 80
EXPOSE 443