FROM node:14.15.4-alpine

# set working directory
WORKDIR /app

# add `/app/node_modules/.bin` to $PATH
ENV PATH /app/node_modules/.bin:$PATH

# imagemin bullshit dependencies
RUN apk add --update \
    git \
    bash \
    lcms2-dev \
    libpng-dev \
    gcc \
    g++ \
    make \
    autoconf \
    python \
    automake \
  && rm -rf /var/cache/apk/*

# install app dependencies
COPY package*.json ./
COPY webpack.config.js ./
COPY yarn.lock ./
COPY ./src/ ./src/

RUN yarn install

# start app
CMD yarn install && yarn watch
