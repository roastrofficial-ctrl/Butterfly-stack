FROM golang:1.23-alpine AS build
WORKDIR /src
COPY systems/mailweb/apps/postbox/go.mod ./
COPY systems/mailweb/apps/postbox .
RUN go test ./... && CGO_ENABLED=0 go build -o /postbox ./cmd/postbox

FROM node:22-alpine
RUN apk add --no-cache ca-certificates wget && mkdir -p /postbox-data && chown node:node /postbox-data
COPY --from=build /postbox /usr/local/bin/postbox
COPY systems/passports/package.json /passport/package.json
COPY systems/passports/src /passport/src
COPY systems/passports/bin/holder-agent.js /passport/bin/holder-agent.js
USER node
EXPOSE 9847
ENTRYPOINT ["postbox"]
