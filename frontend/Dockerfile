# ===============================
# Build stage
# ===============================
FROM node:20-alpine AS build

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

ARG NODE_ENV=production
RUN if [ "$NODE_ENV" = "production" ]; then \
      npm run build; \
    fi

# ===============================
# Runtime stage
# ===============================
FROM node:20-alpine

WORKDIR /app
ARG NODE_ENV=production
ENV NODE_ENV=$NODE_ENV

# Copy build output
COPY --from=build /app/.output ./

EXPOSE 3000

CMD if [ "$NODE_ENV" = "production" ]; then \
      node .output/server/index.mjs; \
    else \
      npm run dev -- --host 0.0.0.0; \
    fi
