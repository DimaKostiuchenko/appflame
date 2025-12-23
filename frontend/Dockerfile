# ===============================
# STAGE 1: Base (Shared)
# ===============================
FROM node:20-alpine AS base
WORKDIR /app

# Copy package files first to cache the npm install layer
COPY package*.json ./
RUN npm install

# Copy the rest of your source code
COPY . .

# ===============================
# STAGE 2: Development (Stop here for Local Dev)
# ===============================
FROM base AS development
ENV NODE_ENV=development

# Nuxt dev server with hot-reload enabled
EXPOSE 3000
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

# ===============================
# STAGE 3: Build (Production Output)
# ===============================
FROM base AS build
RUN npm run build

# ===============================
# STAGE 4: Production Runtime
# ===============================
FROM node:20-alpine AS production
WORKDIR /app
ENV NODE_ENV=production

# Only copy the final built output from the build stage
COPY --from=build /app/.output .output

EXPOSE 3000
CMD ["node", ".output/server/index.mjs"]