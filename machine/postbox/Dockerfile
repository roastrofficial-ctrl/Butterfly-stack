FROM golang:1.23-alpine AS build
WORKDIR /src
COPY systems/mailweb/apps/postbox/go.mod ./
COPY systems/mailweb/apps/postbox .
RUN go test ./... && CGO_ENABLED=0 go build -o /postbox ./cmd/postbox

FROM node:22-alpine
RUN apk add --no-cache ca-certificates wget
COPY --from=build /postbox /usr/local/bin/postbox
USER node
EXPOSE 9847
ENTRYPOINT ["postbox"]
