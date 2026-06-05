#!/bin/bash
rm -rf node_modules package-lock.json
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/app" -w /app node:24 npm install
