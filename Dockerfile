FROM node:alpine3.19 AS build

WORKDIR /app
RUN npm install -g pnpm

COPY package.json .
COPY pnpm-lock.yaml .
RUN pnpm install

COPY . .

RUN pnpm config set allow-build-scripts true && pnpm install
RUN pnpm run build

FROM node:alpine3.19 AS deploy

WORKDIR /app
RUN npm install -g pnpm

COPY --from=build /app/public ./public
COPY --from=build /app/package.json .
COPY --from=build /app/pnpm-lock.yaml .
COPY --from=build /app/vite.config.js .
COPY --from=build /app/dist ./dist
RUN ls -la

# Install semua dependencies tanpa menghapus Vite
RUN pnpm install --frozen-lockfile
RUN pnpm install --prod

CMD ["pnpm", "run", "start"]